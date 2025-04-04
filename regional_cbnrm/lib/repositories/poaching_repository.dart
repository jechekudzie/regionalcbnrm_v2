import 'dart:convert';

import 'package:regional_cbnrm/core/api_service.dart';
import 'package:regional_cbnrm/core/app_exceptions.dart';
import 'package:regional_cbnrm/core/database_helper.dart';
import 'package:regional_cbnrm/models/poaching_model.dart';
import 'package:regional_cbnrm/models/wildlife_conflict_model.dart';

class PoachingRepository {
  final ApiService _apiService = ApiService();
  final DatabaseHelper _dbHelper = DatabaseHelper();

  // Get poaching incidents for an organisation
  Future<List<PoachingIncident>> getIncidents(int organisationId) async {
    try {
      // Try to fetch from API first
      final response = await _apiService.get('/organisations/$organisationId/poaching-incidents');
      
      if (response['status'] == 'success') {
        final incidents = (response['data']['data'] as List)
            .map((incident) => PoachingIncident.fromJson(incident))
            .toList();
        
        // Cache incidents in local database
        await _saveIncidentsToDb(incidents);
        
        return incidents;
      } else {
        throw AppException(response['message'] ?? 'Failed to fetch poaching incidents');
      }
    } catch (e) {
      // If API call fails, try to get from local database
      try {
        final incidentsData = await _dbHelper.query(
          'poaching_incidents',
          where: 'organisation_id = ?',
          whereArgs: [organisationId],
          orderBy: 'date DESC',
        );
        
        List<PoachingIncident> incidents = [];
        
        for (var incidentData in incidentsData) {
          // Create basic incident
          PoachingIncident incident = PoachingIncident.fromJson(incidentData);
          
          // Fetch related species for this incident
          final speciesData = await _dbHelper.query(
            'poaching_incident_species',
            where: 'poaching_incident_id = ?',
            whereArgs: [incident.id],
          );
          
          List<PoachingIncidentSpecies> species = [];
          for (var speciesItem in speciesData) {
            // Get species details
            final speciesDef = await _dbHelper.query(
              'species',
              where: 'id = ?',
              whereArgs: [speciesItem['species_id']],
            );
            
            if (speciesDef.isNotEmpty) {
              species.add(PoachingIncidentSpecies(
                id: speciesItem['id'],
                poachingIncidentId: speciesItem['poaching_incident_id'],
                speciesId: speciesItem['species_id'],
                quantity: speciesItem['quantity'],
                createdAt: speciesItem['created_at'] != null 
                    ? DateTime.parse(speciesItem['created_at']) 
                    : null,
                updatedAt: speciesItem['updated_at'] != null 
                    ? DateTime.parse(speciesItem['updated_at']) 
                    : null,
                species: Species.fromJson(speciesDef.first),
              ));
            }
          }
          
          // Fetch related methods for this incident
          final methodsData = await _dbHelper.query(
            'poaching_incident_methods',
            where: 'poaching_incident_id = ?',
            whereArgs: [incident.id],
          );
          
          List<PoachingIncidentMethod> methods = [];
          for (var methodItem in methodsData) {
            // Get method details
            final methodDef = await _dbHelper.query(
              'poaching_methods',
              where: 'id = ?',
              whereArgs: [methodItem['poaching_method_id']],
            );
            
            if (methodDef.isNotEmpty) {
              methods.add(PoachingIncidentMethod(
                id: methodItem['id'],
                poachingIncidentId: methodItem['poaching_incident_id'],
                poachingMethodId: methodItem['poaching_method_id'],
                createdAt: methodItem['created_at'] != null 
                    ? DateTime.parse(methodItem['created_at']) 
                    : null,
                updatedAt: methodItem['updated_at'] != null 
                    ? DateTime.parse(methodItem['updated_at']) 
                    : null,
                poachingMethod: PoachingMethod.fromJson(methodDef.first),
              ));
            }
          }
          
          // Fetch related poachers for this incident
          final poachersData = await _dbHelper.query(
            'poachers',
            where: 'poaching_incident_id = ?',
            whereArgs: [incident.id],
          );
          
          List<Poacher> poachers = poachersData.map((p) => Poacher.fromJson(p)).toList();
          
          // Update incident with relations
          incidents.add(incident.copyWith(
            species: species,
            methods: methods,
            poachers: poachers,
          ));
        }
        
        return incidents;
      } catch (dbException) {
        // If both API and DB fail, rethrow the original exception
        if (e is AppException) rethrow;
        throw AppException('Failed to fetch poaching incidents: ${e.toString()}');
      }
    }
  }
  
  // Save incidents to local database
  Future<void> _saveIncidentsToDb(List<PoachingIncident> incidents) async {
    for (var incident in incidents) {
      await _dbHelper.insert('poaching_incidents', {
        'id': incident.id,
        'organisation_id': incident.organisationId,
        'title': incident.title,
        'date': incident.date.toIso8601String(),
        'time': incident.time,
        'latitude': incident.latitude,
        'longitude': incident.longitude,
        'description': incident.description,
        'docket_number': incident.docketNumber,
        'docket_status': incident.docketStatus,
        'created_at': incident.createdAt?.toIso8601String(),
        'updated_at': incident.updatedAt?.toIso8601String(),
        'sync_status': 'synced',
        'remote_id': incident.id,
      });
      
      // Save related species if available
      if (incident.species != null) {
        for (var speciesItem in incident.species!) {
          await _dbHelper.insert('poaching_incident_species', {
            'id': speciesItem.id,
            'poaching_incident_id': incident.id,
            'species_id': speciesItem.speciesId,
            'quantity': speciesItem.quantity,
            'created_at': speciesItem.createdAt?.toIso8601String(),
            'updated_at': speciesItem.updatedAt?.toIso8601String(),
          });
          
          // Save species if available
          if (speciesItem.species != null) {
            await _dbHelper.insert('species', {
              'id': speciesItem.species!.id,
              'name': speciesItem.species!.name,
              'species_gender_id': speciesItem.species!.speciesGenderId,
              'maturity_id': speciesItem.species!.maturityId,
              'created_at': speciesItem.species!.createdAt?.toIso8601String(),
              'updated_at': speciesItem.species!.updatedAt?.toIso8601String(),
            }, where: 'id = ?', whereArgs: [speciesItem.species!.id]);
          }
        }
      }
      
      // Save related methods if available
      if (incident.methods != null) {
        for (var methodItem in incident.methods!) {
          await _dbHelper.insert('poaching_incident_methods', {
            'id': methodItem.id,
            'poaching_incident_id': incident.id,
            'poaching_method_id': methodItem.poachingMethodId,
            'created_at': methodItem.createdAt?.toIso8601String(),
            'updated_at': methodItem.updatedAt?.toIso8601String(),
          });
          
          // Save method if available
          if (methodItem.poachingMethod != null) {
            await _dbHelper.insert('poaching_methods', {
              'id': methodItem.poachingMethod!.id,
              'name': methodItem.poachingMethod!.name,
              'created_at': methodItem.poachingMethod!.createdAt?.toIso8601String(),
              'updated_at': methodItem.poachingMethod!.updatedAt?.toIso8601String(),
            }, where: 'id = ?', whereArgs: [methodItem.poachingMethod!.id]);
          }
        }
      }
      
      // Save related poachers if available
      if (incident.poachers != null) {
        for (var poacher in incident.poachers!) {
          await _dbHelper.insert('poachers', {
            'id': poacher.id,
            'poaching_incident_id': incident.id,
            'name': poacher.name,
            'id_number': poacher.idNumber,
            'identification_type_id': poacher.identificationTypeId,
            'gender': poacher.gender,
            'age': poacher.age,
            'nationality': poacher.nationality,
            'status': poacher.status,
            'created_at': poacher.createdAt?.toIso8601String(),
            'updated_at': poacher.updatedAt?.toIso8601String(),
            'sync_status': 'synced',
            'remote_id': poacher.id,
          });
        }
      }
    }
  }

  // Get a specific poaching incident
  Future<PoachingIncident> getIncident(int incidentId) async {
    try {
      final response = await _apiService.get('/poaching-incidents/$incidentId');
      
      if (response['status'] == 'success') {
        return PoachingIncident.fromJson(response['data']['incident']);
      } else {
        throw AppException(response['message'] ?? 'Failed to fetch poaching incident');
      }
    } catch (e) {
      // If API call fails, try to get from local database
      try {
        final incidentData = await _dbHelper.query(
          'poaching_incidents',
          where: 'id = ? OR remote_id = ?',
          whereArgs: [incidentId, incidentId],
        );
        
        if (incidentData.isEmpty) {
          throw NotFoundException('Poaching incident not found');
        }
        
        PoachingIncident incident = PoachingIncident.fromJson(incidentData.first);
        
        // Fetch related species for this incident
        final speciesData = await _dbHelper.query(
          'poaching_incident_species',
          where: 'poaching_incident_id = ?',
          whereArgs: [incident.id],
        );
        
        List<PoachingIncidentSpecies> species = [];
        for (var speciesItem in speciesData) {
          // Get species details
          final speciesDef = await _dbHelper.query(
            'species',
            where: 'id = ?',
            whereArgs: [speciesItem['species_id']],
          );
          
          if (speciesDef.isNotEmpty) {
            species.add(PoachingIncidentSpecies(
              id: speciesItem['id'],
              poachingIncidentId: speciesItem['poaching_incident_id'],
              speciesId: speciesItem['species_id'],
              quantity: speciesItem['quantity'],
              createdAt: speciesItem['created_at'] != null 
                  ? DateTime.parse(speciesItem['created_at']) 
                  : null,
              updatedAt: speciesItem['updated_at'] != null 
                  ? DateTime.parse(speciesItem['updated_at']) 
                  : null,
              species: Species.fromJson(speciesDef.first),
            ));
          }
        }
        
        // Fetch related methods for this incident
        final methodsData = await _dbHelper.query(
          'poaching_incident_methods',
          where: 'poaching_incident_id = ?',
          whereArgs: [incident.id],
        );
        
        List<PoachingIncidentMethod> methods = [];
        for (var methodItem in methodsData) {
          // Get method details
          final methodDef = await _dbHelper.query(
            'poaching_methods',
            where: 'id = ?',
            whereArgs: [methodItem['poaching_method_id']],
          );
          
          if (methodDef.isNotEmpty) {
            methods.add(PoachingIncidentMethod(
              id: methodItem['id'],
              poachingIncidentId: methodItem['poaching_incident_id'],
              poachingMethodId: methodItem['poaching_method_id'],
              createdAt: methodItem['created_at'] != null 
                  ? DateTime.parse(methodItem['created_at']) 
                  : null,
              updatedAt: methodItem['updated_at'] != null 
                  ? DateTime.parse(methodItem['updated_at']) 
                  : null,
              poachingMethod: PoachingMethod.fromJson(methodDef.first),
            ));
          }
        }
        
        // Fetch related poachers for this incident
        final poachersData = await _dbHelper.query(
          'poachers',
          where: 'poaching_incident_id = ?',
          whereArgs: [incident.id],
        );
        
        List<Poacher> poachers = poachersData.map((p) => Poacher.fromJson(p)).toList();
        
        // Update incident with relations
        return incident.copyWith(
          species: species,
          methods: methods,
          poachers: poachers,
        );
      } catch (dbException) {
        // If both API and DB fail, rethrow the original exception
        if (e is AppException) rethrow;
        throw AppException('Failed to fetch poaching incident: ${e.toString()}');
      }
    }
  }

  // Create a new poaching incident
  Future<PoachingIncident> createIncident(PoachingIncident incident) async {
    try {
      final response = await _apiService.post(
        '/poaching-incidents',
        data: incident.toApiJson(),
      );
      
      if (response['status'] == 'success') {
        final createdIncident = PoachingIncident.fromJson(response['data']['incident']);
        
        // Save to local database
        await _dbHelper.insert('poaching_incidents', {
          'id': createdIncident.id,
          'organisation_id': createdIncident.organisationId,
          'title': createdIncident.title,
          'date': createdIncident.date.toIso8601String(),
          'time': createdIncident.time,
          'latitude': createdIncident.latitude,
          'longitude': createdIncident.longitude,
          'description': createdIncident.description,
          'docket_number': createdIncident.docketNumber,
          'docket_status': createdIncident.docketStatus,
          'created_at': createdIncident.createdAt?.toIso8601String(),
          'updated_at': createdIncident.updatedAt?.toIso8601String(),
          'sync_status': 'synced',
          'remote_id': createdIncident.id,
        });
        
        // Save related data if available
        if (createdIncident.species != null) {
          for (var speciesItem in createdIncident.species!) {
            await _dbHelper.insert('poaching_incident_species', {
              'id': speciesItem.id,
              'poaching_incident_id': createdIncident.id,
              'species_id': speciesItem.speciesId,
              'quantity': speciesItem.quantity,
              'created_at': speciesItem.createdAt?.toIso8601String(),
              'updated_at': speciesItem.updatedAt?.toIso8601String(),
            });
          }
        }
        
        if (createdIncident.methods != null) {
          for (var methodItem in createdIncident.methods!) {
            await _dbHelper.insert('poaching_incident_methods', {
              'id': methodItem.id,
              'poaching_incident_id': createdIncident.id,
              'poaching_method_id': methodItem.poachingMethodId,
              'created_at': methodItem.createdAt?.toIso8601String(),
              'updated_at': methodItem.updatedAt?.toIso8601String(),
            });
          }
        }
        
        return createdIncident;
      } else {
        throw AppException(response['message'] ?? 'Failed to create poaching incident');
      }
    } catch (e) {
      // If API call fails, save to local database with pending sync status
      if (e is AppException && !(e is NoInternetException || e is TimeoutException)) rethrow;
      
      // Save to local database with pending sync status
      final currentDateTime = DateTime.now().toIso8601String();
      final localId = await _dbHelper.insert('poaching_incidents', {
        'organisation_id': incident.organisationId,
        'title': incident.title,
        'date': incident.date.toIso8601String(),
        'time': incident.time,
        'latitude': incident.latitude,
        'longitude': incident.longitude,
        'description': incident.description,
        'docket_number': incident.docketNumber,
        'docket_status': incident.docketStatus,
        'created_at': currentDateTime,
        'updated_at': currentDateTime,
        'sync_status': 'pending',
        'remote_id': null,
      });
      
      // Save related species if available
      if (incident.species != null) {
        for (var speciesItem in incident.species!) {
          await _dbHelper.insert('poaching_incident_species', {
            'poaching_incident_id': localId,
            'species_id': speciesItem.speciesId,
            'quantity': speciesItem.quantity,
            'created_at': currentDateTime,
            'updated_at': currentDateTime,
          });
        }
      }
      
      // Save related methods if available
      if (incident.methods != null) {
        for (var methodItem in incident.methods!) {
          await _dbHelper.insert('poaching_incident_methods', {
            'poaching_incident_id': localId,
            'poaching_method_id': methodItem.poachingMethodId,
            'created_at': currentDateTime,
            'updated_at': currentDateTime,
          });
        }
      }
      
      // Add to sync queue
      await _dbHelper.addToSyncQueue(
        'poaching_incidents',
        localId,
        'create',
        jsonEncode(incident.toApiJson()),
      );
      
      // Return the locally created incident
      final incidentData = await _dbHelper.query(
        'poaching_incidents',
        where: 'id = ?',
        whereArgs: [localId],
      );
      
      return PoachingIncident.fromJson(incidentData.first);
    }
  }
  
  // Add a poacher to a poaching incident
  Future<Poacher> addPoacher(
    int incidentId,
    String name,
    String? idNumber,
    int? identificationTypeId,
    String? gender,
    int? age,
    String? nationality,
    String status,
  ) async {
    try {
      final data = {
        'name': name,
        'id_number': idNumber,
        'identification_type_id': identificationTypeId,
        'gender': gender,
        'age': age,
        'nationality': nationality,
        'status': status,
      };
      
      final response = await _apiService.post(
        '/poaching-incidents/$incidentId/poachers',
        data: data,
      );
      
      if (response['status'] == 'success') {
        final createdPoacher = Poacher.fromJson(response['data']['poacher']);
        
        // Save to local database
        await _dbHelper.insert('poachers', {
          'id': createdPoacher.id,
          'poaching_incident_id': createdPoacher.poachingIncidentId,
          'name': createdPoacher.name,
          'id_number': createdPoacher.idNumber,
          'identification_type_id': createdPoacher.identificationTypeId,
          'gender': createdPoacher.gender,
          'age': createdPoacher.age,
          'nationality': createdPoacher.nationality,
          'status': createdPoacher.status,
          'created_at': createdPoacher.createdAt?.toIso8601String(),
          'updated_at': createdPoacher.updatedAt?.toIso8601String(),
          'sync_status': 'synced',
          'remote_id': createdPoacher.id,
        });
        
        return createdPoacher;
      } else {
        throw AppException(response['message'] ?? 'Failed to add poacher');
      }
    } catch (e) {
      // If API call fails, save to local database with pending sync status
      if (e is AppException && !(e is NoInternetException || e is TimeoutException)) rethrow;
      
      // Save to local database with pending sync status
      final currentDateTime = DateTime.now().toIso8601String();
      final localId = await _dbHelper.insert('poachers', {
        'poaching_incident_id': incidentId,
        'name': name,
        'id_number': idNumber,
        'identification_type_id': identificationTypeId,
        'gender': gender,
        'age': age,
        'nationality': nationality,
        'status': status,
        'created_at': currentDateTime,
        'updated_at': currentDateTime,
        'sync_status': 'pending',
        'remote_id': null,
      });
      
      // Add to sync queue
      final data = {
        'name': name,
        'id_number': idNumber,
        'identification_type_id': identificationTypeId,
        'gender': gender,
        'age': age,
        'nationality': nationality,
        'status': status,
      };
      
      await _dbHelper.addToSyncQueue(
        'poachers',
        localId,
        'create',
        jsonEncode(data),
      );
      
      // Return the locally created poacher
      final poacherData = await _dbHelper.query(
        'poachers',
        where: 'id = ?',
        whereArgs: [localId],
      );
      
      return Poacher.fromJson(poacherData.first);
    }
  }
  
  // Get poaching methods
  Future<List<PoachingMethod>> getPoachingMethods() async {
    try {
      // Try to fetch from API first
      final response = await _apiService.get('/poaching-methods');
      
      if (response['status'] == 'success') {
        final methods = (response['data']['methods'] as List)
            .map((method) => PoachingMethod.fromJson(method))
            .toList();
        
        // Cache methods in local database
        for (var method in methods) {
          await _dbHelper.insert('poaching_methods', {
            'id': method.id,
            'name': method.name,
            'created_at': method.createdAt?.toIso8601String(),
            'updated_at': method.updatedAt?.toIso8601String(),
          }, where: 'id = ?', whereArgs: [method.id]);
        }
        
        return methods;
      } else {
        throw AppException(response['message'] ?? 'Failed to fetch poaching methods');
      }
    } catch (e) {
      // If API call fails, try to get from local database
      try {
        final methodsData = await _dbHelper.query('poaching_methods');
        return methodsData.map((method) => PoachingMethod.fromJson(method)).toList();
      } catch (dbException) {
        // If both API and DB fail, rethrow the original exception
        if (e is AppException) rethrow;
        throw AppException('Failed to fetch poaching methods: ${e.toString()}');
      }
    }
  }
  
  // Get poaching reasons
  Future<List<PoachingReason>> getPoachingReasons() async {
    try {
      // Try to fetch from API first
      final response = await _apiService.get('/poaching-reasons');
      
      if (response['status'] == 'success') {
        final reasons = (response['data']['reasons'] as List)
            .map((reason) => PoachingReason.fromJson(reason))
            .toList();
        
        // Cache reasons in local database
        for (var reason in reasons) {
          await _dbHelper.insert('poaching_reasons', {
            'id': reason.id,
            'name': reason.name,
            'created_at': reason.createdAt?.toIso8601String(),
            'updated_at': reason.updatedAt?.toIso8601String(),
          }, where: 'id = ?', whereArgs: [reason.id]);
        }
        
        return reasons;
      } else {
        throw AppException(response['message'] ?? 'Failed to fetch poaching reasons');
      }
    } catch (e) {
      // If API call fails, try to get from local database
      try {
        final reasonsData = await _dbHelper.query('poaching_reasons');
        return reasonsData.map((reason) => PoachingReason.fromJson(reason)).toList();
      } catch (dbException) {
        // If both API and DB fail, rethrow the original exception
        if (e is AppException) rethrow;
        throw AppException('Failed to fetch poaching reasons: ${e.toString()}');
      }
    }
  }
  
  // Get species list
  Future<List<Species>> getSpeciesList() async {
    try {
      // Try to fetch from API first
      final response = await _apiService.get('/species');
      
      if (response['status'] == 'success') {
        final species = (response['data']['species'] as List)
            .map((s) => Species.fromJson(s))
            .toList();
        
        // Cache species in local database
        for (var s in species) {
          await _dbHelper.insert('species', {
            'id': s.id,
            'name': s.name,
            'species_gender_id': s.speciesGenderId,
            'maturity_id': s.maturityId,
            'created_at': s.createdAt?.toIso8601String(),
            'updated_at': s.updatedAt?.toIso8601String(),
          }, where: 'id = ?', whereArgs: [s.id]);
        }
        
        return species;
      } else {
        throw AppException(response['message'] ?? 'Failed to fetch species');
      }
    } catch (e) {
      // If API call fails, try to get from local database
      try {
        final speciesData = await _dbHelper.query('species');
        return speciesData.map((s) => Species.fromJson(s)).toList();
      } catch (dbException) {
        // If both API and DB fail, rethrow the original exception
        if (e is AppException) rethrow;
        throw AppException('Failed to fetch species: ${e.toString()}');
      }
    }
  }
}