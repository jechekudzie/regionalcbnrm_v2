import 'package:get/get.dart';
import 'package:sqflite/sqflite.dart';
import 'package:regional_cbnrm/core/api_service.dart';
import 'package:regional_cbnrm/core/app_exceptions.dart';
import 'package:regional_cbnrm/models/wildlife_conflict_model.dart';

class WildlifeConflictRepository {
  final ApiService _apiService = ApiService();
  
  // Get database from GetX dependency injection
  Database get _db => Get.find<Database>(tag: 'app_database');

  // Get wildlife conflict incidents for an organisation
  Future<List<WildlifeConflictIncident>> getIncidents(int organisationId) async {
    try {
      // Try to fetch from API first
      final response = await _apiService.get('/organisations/$organisationId/wildlife-conflicts');

      if (response['status'] == 'success') {
        // Check if response data contains paginated results
        if (response['data'] is Map && response['data'].containsKey('data')) {
          final incidents = (response['data']['data'] as List)
              .map((incident) => WildlifeConflictIncident.fromApiJson(incident))
              .toList();

          // Cache incidents in local database
          await _saveIncidentsToDb(incidents);

          return incidents;
        } else if (response['data'] is List) {
          // If data is directly a list (non-paginated)
          final incidents = (response['data'] as List)
              .map((incident) => WildlifeConflictIncident.fromApiJson(incident))
              .toList();

          // Cache incidents in local database
          await _saveIncidentsToDb(incidents);

          return incidents;
        } else {
          print('Unexpected data format: ${response['data']}');
          return [];
        }
      } else {
        throw AppException(response['message'] ?? 'Failed to fetch incidents');
      }
    } catch (e) {
      // If API call fails, try to get from local database
      try {
        final incidentsData = await _db.query(
          'wildlife_conflict_incidents',
          where: 'organisation_id = ?',
          whereArgs: [organisationId],
          orderBy: 'incident_date DESC',
        );

        List<WildlifeConflictIncident> incidents = incidentsData
            .map((data) => WildlifeConflictIncident.fromDatabaseJson(data))
            .toList();

        // Fetch species IDs for each incident
        for (var i = 0; i < incidents.length; i++) {
          final incidentId = incidents[i].id;
          if (incidentId != null) {
            final speciesRelations = await _db.query(
              'wildlife_conflict_species',
              where: 'wildlife_conflict_incident_id = ?',
              whereArgs: [incidentId],
            );
            
            if (speciesRelations.isNotEmpty) {
              final speciesIds = speciesRelations
                  .map((relation) => relation['species_id'] as int)
                  .toList();
              
              // Create a new incident with the species IDs
              incidents[i] = incidents[i].copyWith(speciesIds: speciesIds);
            }
          }
        }

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
    final batch = _db.batch();
    
    for (var incident in incidents) {
      // Convert to database format
      final incidentMap = incident.toDatabaseJson();
      
      // Insert or replace incident
      batch.insert(
        'wildlife_conflict_incidents',
        incidentMap,
        conflictAlgorithm: ConflictAlgorithm.replace,
      );
      
      // Handle species relationships
      if (incident.id != null && incident.speciesIds != null && incident.speciesIds!.isNotEmpty) {
        // First delete existing relations
        batch.delete(
          'wildlife_conflict_species',
          where: 'wildlife_conflict_incident_id = ?',
          whereArgs: [incident.id],
        );
        
        // Then insert new relations
        for (final speciesId in incident.speciesIds!) {
          batch.insert(
            'wildlife_conflict_species',
            {
              'wildlife_conflict_incident_id': incident.id,
              'species_id': speciesId,
              'created_at': DateTime.now().toIso8601String(),
              'updated_at': DateTime.now().toIso8601String(),
            },
          );
        }
      }
    }
    
    await batch.commit();
  }

  // Get a specific wildlife conflict incident
  Future<WildlifeConflictIncident> getIncident(int incidentId) async {
    try {
      final response = await _apiService.get('/wildlife-conflicts/$incidentId');

      if (response['status'] == 'success') {
        return WildlifeConflictIncident.fromApiJson(response['data']['incident']);
      } else {
        throw AppException(response['message'] ?? 'Failed to fetch incident');
      }
    } catch (e) {
      // If API call fails, try to get from local database
      try {
        final incidentData = await _db.query(
          'wildlife_conflict_incidents',
          where: 'id = ? OR remote_id = ?',
          whereArgs: [incidentId, incidentId],
        );

        if (incidentData.isEmpty) {
          throw NotFoundException('Incident not found');
        }

        // Convert database record to model
        WildlifeConflictIncident incident = WildlifeConflictIncident.fromDatabaseJson(incidentData.first);

        // Get associated species IDs
        final speciesRelations = await _db.query(
          'wildlife_conflict_species',
          where: 'wildlife_conflict_incident_id = ?',
          whereArgs: [incident.id],
        );
        
        if (speciesRelations.isNotEmpty) {
          final speciesIds = speciesRelations
              .map((relation) => relation['species_id'] as int)
              .toList();
          
          // Update the incident with species IDs
          incident = incident.copyWith(speciesIds: speciesIds);
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
  Future<WildlifeConflictIncident> createIncident(
    WildlifeConflictIncident incident, {
    Map<String, dynamic>? additionalData,
  }) async {
    try {
      // Use additionalData if provided, otherwise use standard API JSON
      final data = additionalData ?? incident.toApiJson();

      // Log the request data for debugging
      print('Creating incident with data: $data');

      final response = await _apiService.post(
        '/wildlife-conflicts',
        data: data,
      );

      if (response['status'] == 'success') {
        final createdIncident = WildlifeConflictIncident.fromApiJson(response['data']['incident']);

        // Save to local database (this will handle the species relationships)
        await _saveIncidentsToDb([createdIncident]);

        return createdIncident;
      } else {
        throw AppException(response['message'] ?? 'Failed to create incident');
      }
    } catch (e) {
      print('Error creating incident: $e');
      // If API call fails, save to local database with pending sync status
      if (e is AppException && !(e is NoInternetException || e is TimeoutException)) rethrow;

      // Create a copy with pending sync status
      final pendingIncident = incident.copyWith(syncStatus: 'pending');
      
      // Save to local database
      final incidentMap = pendingIncident.toDatabaseJson();
      final localId = await _db.insert('wildlife_conflict_incidents', incidentMap);
      
      // Save species relationships if available
      if (pendingIncident.speciesIds != null && pendingIncident.speciesIds!.isNotEmpty) {
        for (final speciesId in pendingIncident.speciesIds!) {
          await _db.insert(
            'wildlife_conflict_species',
            {
              'wildlife_conflict_incident_id': localId,
              'species_id': speciesId,
              'created_at': DateTime.now().toIso8601String(),
              'updated_at': DateTime.now().toIso8601String(),
            },
          );
        }
      }

      // Add to sync queue - use additionalData if provided
      // final syncData = additionalData ?? incident.toApiJson();
      // await _db.addToSyncQueue(
      //   'wildlife_conflict_incidents',
      //   localId,
      //   'create',
      //   jsonEncode(syncData),
      // );

      // Return the locally created incident
      final incidentData = await _db.query(
        'wildlife_conflict_incidents',
        where: 'id = ?',
        whereArgs: [localId],
      );

      WildlifeConflictIncident savedIncident = WildlifeConflictIncident.fromDatabaseJson(incidentData.first);
      
      // Retrieve species IDs
      if (pendingIncident.speciesIds != null) {
        savedIncident = savedIncident.copyWith(speciesIds: pendingIncident.speciesIds);
      }
      
      return savedIncident;
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
        final updatedIncident = WildlifeConflictIncident.fromApiJson(response['data']['incident']);

        // Save to local database (this will handle species relationships)
        await _saveIncidentsToDb([updatedIncident]);

        return updatedIncident;
      } else {
        throw AppException(response['message'] ?? 'Failed to update incident');
      }
    } catch (e) {
      // If API call fails, update in local database with pending sync status
      if (e is AppException && !(e is NoInternetException || e is TimeoutException)) rethrow;

      // Fetch the current incident from local database
      final incidentData = await _db.query(
        'wildlife_conflict_incidents',
        where: 'id = ? OR remote_id = ?',
        whereArgs: [incidentId, incidentId],
      );

      if (incidentData.isEmpty) {
        throw NotFoundException('Incident not found');
      }

      final currentIncident = WildlifeConflictIncident.fromDatabaseJson(incidentData.first);
      
      // Get current species IDs
      final speciesRelations = await _db.query(
        'wildlife_conflict_species',
        where: 'wildlife_conflict_incident_id = ?',
        whereArgs: [currentIncident.id],
      );
      
      List<int>? currentSpeciesIds;
      if (speciesRelations.isNotEmpty) {
        currentSpeciesIds = speciesRelations
            .map((relation) => relation['species_id'] as int)
            .toList();
      }

      // Create updated incident with pending sync status
      WildlifeConflictIncident updatedIncident = currentIncident.copyWith(
        title: data['title'] ?? currentIncident.title,
        incidentDate: data['incident_date'] != null 
            ? DateTime.parse(data['incident_date']) 
            : currentIncident.incidentDate,
        incidentTime: data['incident_time'] ?? currentIncident.incidentTime,
        latitude: data['latitude'] != null 
            ? (data['latitude'] is String ? double.parse(data['latitude']) : data['latitude'].toDouble()) 
            : currentIncident.latitude,
        longitude: data['longitude'] != null 
            ? (data['longitude'] is String ? double.parse(data['longitude']) : data['longitude'].toDouble()) 
            : currentIncident.longitude,
        description: data['description'] ?? currentIncident.description,
        conflictTypeId: data['conflict_type_id'] ?? currentIncident.conflictTypeId,
        updatedAt: DateTime.now(),
        syncStatus: 'pending',
        speciesIds: data['species_ids'] as List<int>? ?? currentSpeciesIds,
      );

      // Update in local database
      await _db.update(
        'wildlife_conflict_incidents',
        updatedIncident.toDatabaseJson(),
        where: 'id = ?',
        whereArgs: [currentIncident.id!],
      );
      
      // Update species relationships if species IDs are provided
      if (data['species_ids'] != null) {
        // Delete existing relations
        await _db.delete(
          'wildlife_conflict_species',
          where: 'wildlife_conflict_incident_id = ?',
          whereArgs: [currentIncident.id],
        );
        
        // Insert new relations
        final speciesIds = data['species_ids'] as List<int>;
        for (final speciesId in speciesIds) {
          await _db.insert(
            'wildlife_conflict_species',
            {
              'wildlife_conflict_incident_id': currentIncident.id,
              'species_id': speciesId,
              'created_at': DateTime.now().toIso8601String(),
              'updated_at': DateTime.now().toIso8601String(),
            },
          );
        }
      }

      // Add to sync queue
      // await _db.addToSyncQueue(
      //   'wildlife_conflict_incidents',
      //   currentIncident.id!,
      //   'update',
      //   jsonEncode(data),
      // );

      return updatedIncident;
    }
  }

  // Delete a wildlife conflict incident
  Future<void> deleteIncident(int incidentId, bool deleteFromServer) async {
    try {
      if (deleteFromServer) {
        // Delete from API
        final response = await _apiService.delete('/wildlife-conflicts/$incidentId');

        if (response['status'] != 'success') {
          throw AppException(response['message'] ?? 'Failed to delete incident from server');
        }
      }

      // Delete from local database (including related records)
      await _db.transaction((txn) async {
        // Delete species relationships
        await txn.delete(
          'wildlife_conflict_species',
          where: 'wildlife_conflict_incident_id = ?',
          whereArgs: [incidentId],
        );
        
        // Delete the incident
        await txn.delete(
          'wildlife_conflict_incidents',
          where: 'id = ? OR remote_id = ?',
          whereArgs: [incidentId, incidentId],
        );
      });
    } catch (e) {
      throw AppException('Failed to delete incident: ${e.toString()}');
    }
  }

  // Get all species
  Future<List<Map<String, dynamic>>> getSpecies({int? organisationId}) async {
    try {
      // Try to fetch from API first
      const String endpoint = '/species';

      print('Fetching species from endpoint: $endpoint');
      final response = await _apiService.get(endpoint);

      print('Species API response: $response');

      if (response['status'] == 'success') {
        List<dynamic> speciesList;

        // Handle different API response structures
        if (response['data'] is Map && response['data'].containsKey('species')) {
          speciesList = response['data']['species'] as List;
        } else if (response['data'] is List) {
          speciesList = response['data'];
        } else {
          print('Unexpected species data format: ${response['data']}');
          speciesList = [];
        }

        // Cache species in local database - we're only storing the raw data now
        for (var species in speciesList) {
          await _db.insert(
            'species',
            {
              'id': species['id'],
              'name': species['name'],
              'created_at': species['created_at'],
              'updated_at': species['updated_at'],
            },
            conflictAlgorithm: ConflictAlgorithm.replace,
          );
        }

        // Return the raw species data for use elsewhere
        return speciesList.cast<Map<String, dynamic>>();
      } else {
        print('Failed to fetch species: ${response['message']}');
        throw AppException(response['message'] ?? 'Failed to fetch species');
      }
    } catch (e) {
      print('Error fetching species from API: $e');
      // If API call fails, try to get from local database
      try {
        final speciesData = await _db.query('species');
        
        if (speciesData.isNotEmpty) {
          print('Loaded ${speciesData.length} species from local database');
          return speciesData;
        } else {
          print('No species found in local database');
          throw AppException('No species available. Please check your internet connection and try again.');
        }
      } catch (dbException) {
        print('Error fetching species from database: $dbException');
        // If both API and DB fail, rethrow the original exception
        if (e is AppException) rethrow;
        throw AppException('Failed to fetch species: ${e.toString()}');
      }
    }
  }

  // Get all conflict outcomes
  Future<List<Map<String, dynamic>>> getConflictOutcomes({int? organisationId}) async {
  try {
    // Try to fetch from API first
    final String endpoint = organisationId != null 
        ? '/organisations/$organisationId/conflict-outcomes'
        : '/conflict-outcomes';

    print('Fetching conflict outcomes from endpoint: $endpoint');
    final response = await _apiService.get(endpoint);

    print('Conflict outcomes API response: $response');

    if (response['status'] == 'success') {
      List<dynamic> outcomesList;

      // Handle different API response structures
      if (response['data'] is Map && response['data'].containsKey('conflict_outcomes')) {
        outcomesList = response['data']['conflict_outcomes'] as List;
      } else if (response['data'] is List) {
        outcomesList = response['data'];
      } else {
        print('Unexpected conflict outcomes data format: ${response['data']}');
        outcomesList = [];
      }

      // Cache conflict outcomes in local database
      for (var outcome in outcomesList) {
        await _db.insert(
          'conflict_out_comes',
          {
            'id': outcome['id'],
            'conflict_type_id': outcome['conflict_type_id'],
            'name': outcome['name'],
            'description': outcome['description'],
            'slug': outcome['slug'],
            'created_at': outcome['created_at'],
            'updated_at': outcome['updated_at'],
            'author': outcome['author'],
            'sync_status': 'synced',
          },
          conflictAlgorithm: ConflictAlgorithm.replace,
        );

        // Store dynamic fields if available
        if (outcome['dynamic_fields'] != null && outcome['dynamic_fields'] is List) {
          for (var field in outcome['dynamic_fields']) {
            await _db.insert(
              'dynamic_fields',
              {
                'id': field['id'],
                'organisation_id': field['organisation_id'],
                'conflict_outcome_id': outcome['id'],
                'field_name': field['field_name'],
                'field_type': field['field_type'],
                'label': field['label'],
                'slug': field['slug'],
                'created_at': field['created_at'],
                'updated_at': field['updated_at'],
                'author': field['author'],
                'sync_status': 'synced',
              },
              conflictAlgorithm: ConflictAlgorithm.replace,
            );
            
            // Store field options if available
            if (field['options'] != null && field['options'] is List) {
              for (var option in field['options']) {
                await _db.insert(
                  'dynamic_field_options',
                  {
                    'id': option['id'],
                    'dynamic_field_id': field['id'],
                    'value': option['value'],
                    'label': option['label'],
                    'created_at': option['created_at'],
                    'updated_at': option['updated_at'],
                  },
                  conflictAlgorithm: ConflictAlgorithm.replace,
                );
              }
            }
          }
        }
      }

      // Return the raw conflict outcomes data for use elsewhere
      return outcomesList.cast<Map<String, dynamic>>();
    } else {
      print('Failed to fetch conflict outcomes: ${response['message']}');
      throw AppException(response['message'] ?? 'Failed to fetch conflict outcomes');
    }
  } catch (e) {
    print('Error fetching conflict outcomes from API: $e');
    // If API call fails, try to get from local database
    try {
      final outcomesData = await _db.query('conflict_out_comes');
      
      if (outcomesData.isNotEmpty) {
        print('Loaded ${outcomesData.length} conflict outcomes from local database');
        
        // For each outcome, fetch its dynamic fields
        List<Map<String, dynamic>> outcomesWithFields = [];
        
        for (var outcome in outcomesData) {
          final outcomeId = outcome['id'];
          
          // Query for dynamic fields associated with this outcome
          final fields = await _db.query(
            'dynamic_fields',
            where: 'conflict_outcome_id = ?',
            whereArgs: [outcomeId],
          );
          
          // For each field, fetch its options
          List<Map<String, dynamic>> fieldsWithOptions = [];
          
          for (var field in fields) {
            final fieldId = field['id'];
            
            // Query for options associated with this field
            final options = await _db.query(
              'dynamic_field_options',
              where: 'dynamic_field_id = ?',
              whereArgs: [fieldId],
            );
            
            // Add options to the field
            final fieldWithOptions = Map<String, dynamic>.from(field);
            fieldWithOptions['options'] = options;
            fieldsWithOptions.add(fieldWithOptions);
          }
          
          // Add fields to the outcome
          final outcomeWithFields = Map<String, dynamic>.from(outcome);
          outcomeWithFields['dynamic_fields'] = fieldsWithOptions;
          outcomesWithFields.add(outcomeWithFields);
        }
        
        return outcomesWithFields;
      } else {
        print('No conflict outcomes found in local database');
        throw AppException('No conflict outcomes available. Please check your internet connection and try again.');
      }
    } catch (dbException) {
      print('Error fetching conflict outcomes from database: $dbException');
      // If both API and DB fail, rethrow the original exception
      if (e is AppException) rethrow;
      throw AppException('Failed to fetch conflict outcomes: ${e.toString()}');
    }
  }
}

}