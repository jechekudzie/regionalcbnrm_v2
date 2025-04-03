import 'dart:convert';

import 'package:resource_africa/core/api_service.dart';
import 'package:resource_africa/core/app_exceptions.dart';
import 'package:resource_africa/core/database_helper.dart';
import 'package:resource_africa/models/wildlife_conflict_model.dart';

class WildlifeConflictRepository {
  final ApiService _apiService = ApiService();
  final DatabaseHelper _dbHelper = DatabaseHelper();

  // Get wildlife conflict incidents for an organisation
  Future<List<WildlifeConflictIncident>> getIncidents(int organisationId) async {
    try {
      // Try to fetch from API first
      final response = await _apiService.get('/organisations/$organisationId/wildlife-conflicts');
      
      if (response['status'] == 'success') {
        final incidents = (response['data']['data'] as List)
            .map((incident) => WildlifeConflictIncident.fromJson(incident))
            .toList();
        
        // Cache incidents in local database
        await _saveIncidentsToDb(incidents);
        
        return incidents;
      } else {
        throw AppException(response['message'] ?? 'Failed to fetch incidents');
      }
    } catch (e) {
      // If API call fails, try to get from local database
      try {
        final incidentsData = await _dbHelper.query(
          'wildlife_conflict_incidents',
          where: 'organisation_id = ?',
          whereArgs: [organisationId],
          orderBy: 'date DESC',
        );
        
        List<WildlifeConflictIncident> incidents = await Future.wait(
          incidentsData.map((incidentData) async {
            WildlifeConflictIncident incident = WildlifeConflictIncident.fromJson(incidentData);
            
            // Fetch related data
            final conflictTypeData = await _dbHelper.query(
              'conflict_types',
              where: 'id = ?',
              whereArgs: [incident.conflictTypeId],
            );
            
            final speciesData = await _dbHelper.query(
              'species',
              where: 'id = ?',
              whereArgs: [incident.speciesId],
            );
            
            if (conflictTypeData.isNotEmpty) {
              incident = incident.copyWith(
                conflictType: ConflictType.fromJson(conflictTypeData.first)
              );
            }
            
            if (speciesData.isNotEmpty) {
              incident = incident.copyWith(
                species: Species.fromJson(speciesData.first)
              );
            }
            
            return incident;
          }).toList(),
        );
        
        return incidents;
      } catch (dbException) {
        // If both API and DB fail, rethrow the original exception
        if (e is AppException) rethrow;
        throw AppException('Failed to fetch incidents: ${e.toString()}');
      }
    }
  }
  
  // Save incidents to local database
  Future<void> _saveIncidentsToDb(List<WildlifeConflictIncident> incidents) async {
    for (var incident in incidents) {
      await _dbHelper.insert('wildlife_conflict_incidents', {
        'id': incident.id,
        'organisation_id': incident.organisationId,
        'title': incident.title,
        'date': incident.date.toIso8601String(),
        'time': incident.time,
        'latitude': incident.latitude,
        'longitude': incident.longitude,
        'description': incident.description,
        'conflict_type_id': incident.conflictTypeId,
        'species_id': incident.speciesId,
        'created_at': incident.createdAt?.toIso8601String(),
        'updated_at': incident.updatedAt?.toIso8601String(),
        'sync_status': 'synced',
        'remote_id': incident.id,
      });
      
      // Save conflict type if available
      if (incident.conflictType != null) {
        await _dbHelper.insert('conflict_types', {
          'id': incident.conflictType!.id,
          'name': incident.conflictType!.name,
          'created_at': incident.conflictType!.createdAt?.toIso8601String(),
          'updated_at': incident.conflictType!.updatedAt?.toIso8601String(),
        }, where: 'id = ?', whereArgs: [incident.conflictType!.id]);
      }
      
      // Save species if available
      if (incident.species != null) {
        await _dbHelper.insert('species', {
          'id': incident.species!.id,
          'name': incident.species!.name,
          'species_gender_id': incident.species!.speciesGenderId,
          'maturity_id': incident.species!.maturityId,
          'created_at': incident.species!.createdAt?.toIso8601String(),
          'updated_at': incident.species!.updatedAt?.toIso8601String(),
        }, where: 'id = ?', whereArgs: [incident.species!.id]);
      }
    }
  }

  // Get a specific wildlife conflict incident
  Future<WildlifeConflictIncident> getIncident(int incidentId) async {
    try {
      final response = await _apiService.get('/wildlife-conflicts/$incidentId');
      
      if (response['status'] == 'success') {
        return WildlifeConflictIncident.fromJson(response['data']['incident']);
      } else {
        throw AppException(response['message'] ?? 'Failed to fetch incident');
      }
    } catch (e) {
      // If API call fails, try to get from local database
      try {
        final incidentData = await _dbHelper.query(
          'wildlife_conflict_incidents',
          where: 'id = ? OR remote_id = ?',
          whereArgs: [incidentId, incidentId],
        );
        
        if (incidentData.isEmpty) {
          throw NotFoundException('Incident not found');
        }
        
        WildlifeConflictIncident incident = WildlifeConflictIncident.fromJson(incidentData.first);
        
        // Fetch related data
        final conflictTypeData = await _dbHelper.query(
          'conflict_types',
          where: 'id = ?',
          whereArgs: [incident.conflictTypeId],
        );
        
        final speciesData = await _dbHelper.query(
          'species',
          where: 'id = ?',
          whereArgs: [incident.speciesId],
        );
        
        final outcomesData = await _dbHelper.query(
          'wildlife_conflict_outcomes',
          where: 'incident_id = ?',
          whereArgs: [incident.id],
        );
        
        if (conflictTypeData.isNotEmpty) {
          incident = incident.copyWith(
            conflictType: ConflictType.fromJson(conflictTypeData.first)
          );
        }
        
        if (speciesData.isNotEmpty) {
          incident = incident.copyWith(
            species: Species.fromJson(speciesData.first)
          );
        }
        
        if (outcomesData.isNotEmpty) {
          List<WildlifeConflictOutcome> outcomes = await Future.wait(
            outcomesData.map((outcomeData) async {
              WildlifeConflictOutcome outcome = WildlifeConflictOutcome.fromJson(outcomeData);
              
              final conflictOutcomeData = await _dbHelper.query(
                'conflict_outcomes',
                where: 'id = ?',
                whereArgs: [outcome.conflictOutcomeId],
              );
              
              if (conflictOutcomeData.isNotEmpty) {
                outcome = WildlifeConflictOutcome(
                  id: outcome.id,
                  wildlifeConflictIncidentId: outcome.wildlifeConflictIncidentId,
                  conflictOutcomeId: outcome.conflictOutcomeId,
                  notes: outcome.notes,
                  date: outcome.date,
                  createdAt: outcome.createdAt,
                  updatedAt: outcome.updatedAt,
                  syncStatus: outcome.syncStatus,
                  remoteId: outcome.remoteId,
                  conflictOutcome: ConflictOutcome.fromJson(conflictOutcomeData.first),
                );
              }
              
              return outcome;
            }).toList(),
          );
          
          incident = incident.copyWith(outcomes: outcomes);
        }
        
        return incident;
      } catch (dbException) {
        // If both API and DB fail, rethrow the original exception
        if (e is AppException) rethrow;
        throw AppException('Failed to fetch incident: ${e.toString()}');
      }
    }
  }

  // Create a new wildlife conflict incident
  Future<WildlifeConflictIncident> createIncident(WildlifeConflictIncident incident) async {
    try {
      final response = await _apiService.post(
        '/wildlife-conflicts',
        data: incident.toApiJson(),
      );
      
      if (response['status'] == 'success') {
        final createdIncident = WildlifeConflictIncident.fromJson(response['data']['incident']);
        
        // Save to local database
        await _dbHelper.insert('wildlife_conflict_incidents', {
          'id': createdIncident.id,
          'organisation_id': createdIncident.organisationId,
          'title': createdIncident.title,
          'date': createdIncident.date.toIso8601String(),
          'time': createdIncident.time,
          'latitude': createdIncident.latitude,
          'longitude': createdIncident.longitude,
          'description': createdIncident.description,
          'conflict_type_id': createdIncident.conflictTypeId,
          'species_id': createdIncident.speciesId,
          'created_at': createdIncident.createdAt?.toIso8601String(),
          'updated_at': createdIncident.updatedAt?.toIso8601String(),
          'sync_status': 'synced',
          'remote_id': createdIncident.id,
        });
        
        return createdIncident;
      } else {
        throw AppException(response['message'] ?? 'Failed to create incident');
      }
    } catch (e) {
      // If API call fails, save to local database with pending sync status
      if (e is AppException && !(e is NoInternetException || e is TimeoutException)) rethrow;
      
      // Save to local database with pending sync status
      final currentDateTime = DateTime.now().toIso8601String();
      final localId = await _dbHelper.insert('wildlife_conflict_incidents', {
        'organisation_id': incident.organisationId,
        'title': incident.title,
        'date': incident.date.toIso8601String(),
        'time': incident.time,
        'latitude': incident.latitude,
        'longitude': incident.longitude,
        'description': incident.description,
        'conflict_type_id': incident.conflictTypeId,
        'species_id': incident.speciesId,
        'created_at': currentDateTime,
        'updated_at': currentDateTime,
        'sync_status': 'pending',
        'remote_id': null,
      });
      
      // Add to sync queue
      await _dbHelper.addToSyncQueue(
        'wildlife_conflict_incidents',
        localId,
        'create',
        jsonEncode(incident.toApiJson()),
      );
      
      // Return the locally created incident
      final incidentData = await _dbHelper.query(
        'wildlife_conflict_incidents',
        where: 'id = ?',
        whereArgs: [localId],
      );
      
      return WildlifeConflictIncident.fromJson(incidentData.first);
    }
  }

  // Update a wildlife conflict incident
  Future<WildlifeConflictIncident> updateIncident(int incidentId, Map<String, dynamic> data) async {
    try {
      final response = await _apiService.put(
        '/wildlife-conflicts/$incidentId',
        data: data,
      );
      
      if (response['status'] == 'success') {
        final updatedIncident = WildlifeConflictIncident.fromJson(response['data']['incident']);
        
        // Update in local database
        await _dbHelper.update(
          'wildlife_conflict_incidents',
          {
            'title': updatedIncident.title,
            'date': updatedIncident.date.toIso8601String(),
            'time': updatedIncident.time,
            'latitude': updatedIncident.latitude,
            'longitude': updatedIncident.longitude,
            'description': updatedIncident.description,
            'conflict_type_id': updatedIncident.conflictTypeId,
            'species_id': updatedIncident.speciesId,
            'updated_at': updatedIncident.updatedAt?.toIso8601String(),
            'sync_status': 'synced',
          },
          where: 'id = ? OR remote_id = ?',
          whereArgs: [updatedIncident.id, updatedIncident.id],
        );
        
        return updatedIncident;
      } else {
        throw AppException(response['message'] ?? 'Failed to update incident');
      }
    } catch (e) {
      // If API call fails, update in local database with pending sync status
      if (e is AppException && !(e is NoInternetException || e is TimeoutException)) rethrow;
      
      // Fetch the current incident from local database
      final incidentData = await _dbHelper.query(
        'wildlife_conflict_incidents',
        where: 'id = ? OR remote_id = ?',
        whereArgs: [incidentId, incidentId],
      );
      
      if (incidentData.isEmpty) {
        throw NotFoundException('Incident not found');
      }
      
      final currentIncident = WildlifeConflictIncident.fromJson(incidentData.first);
      
      // Update in local database with pending sync status
      final updatedFields = {
        'title': data['title'] ?? currentIncident.title,
        'date': data['date'] != null
            ? DateTime.parse(data['date']).toIso8601String()
            : currentIncident.date.toIso8601String(),
        'time': data['time'] ?? currentIncident.time,
        'latitude': data['latitude'] ?? currentIncident.latitude,
        'longitude': data['longitude'] ?? currentIncident.longitude,
        'description': data['description'] ?? currentIncident.description,
        'conflict_type_id': data['conflict_type_id'] ?? currentIncident.conflictTypeId,
        'species_id': data['species_id'] ?? currentIncident.speciesId,
        'updated_at': DateTime.now().toIso8601String(),
        'sync_status': 'pending',
      };
      
      await _dbHelper.update(
        'wildlife_conflict_incidents',
        updatedFields,
        where: 'id = ?',
        whereArgs: [currentIncident.id!],
      );
      
      // Add to sync queue
      await _dbHelper.addToSyncQueue(
        'wildlife_conflict_incidents',
        currentIncident.id!,
        'update',
        jsonEncode(data),
      );
      
      // Return the locally updated incident
      final updatedIncidentData = await _dbHelper.query(
        'wildlife_conflict_incidents',
        where: 'id = ?',
        whereArgs: [currentIncident.id!],
      );
      
      return WildlifeConflictIncident.fromJson(updatedIncidentData.first);
    }
  }

  // Get conflict types
  Future<List<ConflictType>> getConflictTypes() async {
    try {
      // Try to fetch from API first
      final response = await _apiService.get('/conflict-types');
      
      if (response['status'] == 'success') {
        final conflictTypes = (response['data']['conflict_types'] as List)
            .map((type) => ConflictType.fromJson(type))
            .toList();
        
        // Cache conflict types in local database
        for (var type in conflictTypes) {
          await _dbHelper.insert('conflict_types', {
            'id': type.id,
            'name': type.name,
            'created_at': type.createdAt?.toIso8601String(),
            'updated_at': type.updatedAt?.toIso8601String(),
          }, where: 'id = ?', whereArgs: [type.id]);
        }
        
        return conflictTypes;
      } else {
        throw AppException(response['message'] ?? 'Failed to fetch conflict types');
      }
    } catch (e) {
      // If API call fails, try to get from local database
      try {
        final typesData = await _dbHelper.query('conflict_types');
        return typesData.map((type) => ConflictType.fromJson(type)).toList();
      } catch (dbException) {
        // If both API and DB fail, rethrow the original exception
        if (e is AppException) rethrow;
        throw AppException('Failed to fetch conflict types: ${e.toString()}');
      }
    }
  }

  // Get conflict outcomes
  Future<List<ConflictOutcome>> getConflictOutcomes() async {
    try {
      // Try to fetch from API first
      final response = await _apiService.get('/conflict-outcomes');
      
      if (response['status'] == 'success') {
        final conflictOutcomes = (response['data']['conflict_outcomes'] as List)
            .map((outcome) => ConflictOutcome.fromJson(outcome))
            .toList();
        
        // Cache conflict outcomes in local database
        for (var outcome in conflictOutcomes) {
          await _dbHelper.insert('conflict_outcomes', {
            'id': outcome.id,
            'name': outcome.name,
            'created_at': outcome.createdAt?.toIso8601String(),
            'updated_at': outcome.updatedAt?.toIso8601String(),
          }, where: 'id = ?', whereArgs: [outcome.id]);
          
          // Save dynamic fields if available
          if (outcome.dynamicFields != null) {
            for (var field in outcome.dynamicFields!) {
              // Save dynamic field
              await _dbHelper.insert('dynamic_fields', {
                'id': field.id,
                'name': field.name,
                'type': field.type,
                'label': field.label,
                'required': field.required ? 1 : 0,
                'created_at': field.createdAt?.toIso8601String(),
                'updated_at': field.updatedAt?.toIso8601String(),
              }, where: 'id = ?', whereArgs: [field.id]);
              
              // Save options if available
              if (field.options != null) {
                for (var option in field.options!) {
                  await _dbHelper.insert('dynamic_field_options', {
                    'id': option.id,
                    'dynamic_field_id': option.dynamicFieldId,
                    'value': option.value,
                    'label': option.label,
                    'created_at': option.createdAt?.toIso8601String(),
                    'updated_at': option.updatedAt?.toIso8601String(),
                  }, where: 'id = ?', whereArgs: [option.id]);
                }
              }
            }
          }
        }
        
        return conflictOutcomes;
      } else {
        throw AppException(response['message'] ?? 'Failed to fetch conflict outcomes');
      }
    } catch (e) {
      // If API call fails, try to get from local database
      try {
        final outcomesData = await _dbHelper.query('conflict_outcomes');
        
        List<ConflictOutcome> outcomes = await Future.wait(
          outcomesData.map((outcomeData) async {
            final outcomeId = outcomeData['id'];
            
            // Fetch dynamic fields for this outcome
            final fieldsData = await _dbHelper.rawQuery('''
              SELECT df.*
              FROM dynamic_fields df
              JOIN conflict_outcome_dynamic_field_values codfv ON df.id = codfv.dynamic_field_id
              WHERE codfv.conflict_outcome_id = ?
            ''', [outcomeId]);
            
            List<DynamicField> fields = await Future.wait(
              fieldsData.map((fieldData) async {
                final fieldId = fieldData['id'];
                
                // Fetch options for this field
                final optionsData = await _dbHelper.query(
                  'dynamic_field_options',
                  where: 'dynamic_field_id = ?',
                  whereArgs: [fieldId],
                );
                
                final options = optionsData
                    .map((option) => DynamicFieldOption.fromJson(option))
                    .toList();
                
                return DynamicField(
                  id: fieldData['id'],
                  name: fieldData['name'],
                  type: fieldData['type'],
                  label: fieldData['label'],
                  required: fieldData['required'] == 1,
                  createdAt: fieldData['created_at'] != null
                      ? DateTime.parse(fieldData['created_at'])
                      : null,
                  updatedAt: fieldData['updated_at'] != null
                      ? DateTime.parse(fieldData['updated_at'])
                      : null,
                  options: options,
                );
              }).toList(),
            );
            
            return ConflictOutcome(
              id: outcomeData['id'],
              name: outcomeData['name'],
              createdAt: outcomeData['created_at'] != null
                  ? DateTime.parse(outcomeData['created_at'])
                  : null,
              updatedAt: outcomeData['updated_at'] != null
                  ? DateTime.parse(outcomeData['updated_at'])
                  : null,
              dynamicFields: fields,
            );
          }).toList(),
        );
        
        return outcomes;
      } catch (dbException) {
        // If both API and DB fail, rethrow the original exception
        if (e is AppException) rethrow;
        throw AppException('Failed to fetch conflict outcomes: ${e.toString()}');
      }
    }
  }

  // Add outcome to a wildlife conflict incident
  Future<WildlifeConflictOutcome> addOutcome(
    int incidentId,
    int conflictOutcomeId,
    String? notes,
    DateTime date,
    List<DynamicValue>? dynamicValues,
  ) async {
    try {
      final data = {
        'conflict_outcome_id': conflictOutcomeId,
        'notes': notes,
        'date': date.toIso8601String().split('T')[0],
        'dynamic_values': dynamicValues?.map((value) => value.toApiJson()).toList(),
      };
      
      final response = await _apiService.post(
        '/wildlife-conflicts/$incidentId/outcomes',
        data: data,
      );
      
      if (response['status'] == 'success') {
        final createdOutcome = WildlifeConflictOutcome.fromJson(response['data']['outcome']);
        
        // Save to local database
        await _dbHelper.insert('wildlife_conflict_outcomes', {
          'id': createdOutcome.id,
          'incident_id': incidentId,
          'conflict_outcome_id': createdOutcome.conflictOutcomeId,
          'notes': createdOutcome.notes,
          'date': createdOutcome.date.toIso8601String(),
          'created_at': createdOutcome.createdAt?.toIso8601String(),
          'updated_at': createdOutcome.updatedAt?.toIso8601String(),
          'sync_status': 'synced',
          'remote_id': createdOutcome.id,
        });
        
        return createdOutcome;
      } else {
        throw AppException(response['message'] ?? 'Failed to add outcome');
      }
    } catch (e) {
      // If API call fails, save to local database with pending sync status
      if (e is AppException && !(e is NoInternetException || e is TimeoutException)) rethrow;
      
      // Save to local database with pending sync status
      final currentDateTime = DateTime.now().toIso8601String();
      final localId = await _dbHelper.insert('wildlife_conflict_outcomes', {
        'incident_id': incidentId,
        'conflict_outcome_id': conflictOutcomeId,
        'notes': notes,
        'date': date.toIso8601String(),
        'created_at': currentDateTime,
        'updated_at': currentDateTime,
        'sync_status': 'pending',
        'remote_id': null,
      });
      
      // Save dynamic values if available
      if (dynamicValues != null && dynamicValues.isNotEmpty) {
        for (var value in dynamicValues) {
          await _dbHelper.insert('wildlife_conflict_dynamic_values', {
            'wildlife_conflict_outcome_id': localId,
            'dynamic_field_id': value.dynamicFieldId,
            'value': value.value,
            'created_at': currentDateTime,
            'updated_at': currentDateTime,
          });
        }
      }
      
      // Add to sync queue
      final data = {
        'conflict_outcome_id': conflictOutcomeId,
        'notes': notes,
        'date': date.toIso8601String().split('T')[0],
        'dynamic_values': dynamicValues?.map((value) => value.toApiJson()).toList(),
      };
      
      await _dbHelper.addToSyncQueue(
        'wildlife_conflict_outcomes',
        localId,
        'create',
        jsonEncode(data),
      );
      
      // Return the locally created outcome
      final outcomeData = await _dbHelper.query(
        'wildlife_conflict_outcomes',
        where: 'id = ?',
        whereArgs: [localId],
      );
      
      final outcomeValues = await _dbHelper.query(
        'wildlife_conflict_dynamic_values',
        where: 'wildlife_conflict_outcome_id = ?',
        whereArgs: [localId],
      );
      
      final outcome = WildlifeConflictOutcome.fromJson(outcomeData.first);
      
      // Get conflict outcome details
      final conflictOutcomeData = await _dbHelper.query(
        'conflict_outcomes',
        where: 'id = ?',
        whereArgs: [conflictOutcomeId],
      );
      
      final values = outcomeValues.map((valueData) => DynamicValue.fromJson(valueData)).toList();
      
      return WildlifeConflictOutcome(
        id: outcome.id,
        wildlifeConflictIncidentId: outcome.wildlifeConflictIncidentId,
        conflictOutcomeId: outcome.conflictOutcomeId,
        notes: outcome.notes,
        date: outcome.date,
        createdAt: outcome.createdAt,
        updatedAt: outcome.updatedAt,
        syncStatus: outcome.syncStatus,
        remoteId: outcome.remoteId,
        conflictOutcome: conflictOutcomeData.isNotEmpty
            ? ConflictOutcome.fromJson(conflictOutcomeData.first)
            : null,
        dynamicValues: values,
      );
    }
  }
}