import 'dart:convert';
import 'package:regional_cbnrm/core/api_service.dart';
import 'package:regional_cbnrm/core/app_exceptions.dart';
import 'package:regional_cbnrm/core/database_helper.dart';
import 'package:regional_cbnrm/models/problem_animal_control_model.dart';
import 'package:regional_cbnrm/models/wildlife_conflict_model.dart';

class ProblemAnimalControlRepository {
  final ApiService _apiService = ApiService();
  final DatabaseHelper _dbHelper = DatabaseHelper();

  // Get problem animal controls for an organisation
  Future<List<ProblemAnimalControl>> getControls(int organisationId) async {
    try {
      // Try to fetch from API first
      final response = await _apiService.get('/organisations/$organisationId/problem-animal-controls');

      if (response['status'] == 'success') {
        final controls = (response['data']['data'] as List)
            .map((control) => ProblemAnimalControl.fromJson(control))
            .toList();

        // Cache controls in local database
        await _saveControlsToDb(controls);

        return controls;
      } else {
        throw AppException(response['message'] ?? 'Failed to fetch controls');
      }
    } catch (e) {
      // If API call fails, try to get from local database
      try {
        final controlsData = await _dbHelper.query(
          'problem_animal_controls',
          where: 'organisation_id = ?',
          whereArgs: [organisationId],
          orderBy: 'date DESC',
        );

        List<ProblemAnimalControl> controls = await Future.wait(
          controlsData.map((controlData) async {
            ProblemAnimalControl control = ProblemAnimalControl.fromJson(controlData);

            // Fetch related data
            final controlMeasureData = await _dbHelper.query(
              'control_measures',
              where: 'id = ?',
              whereArgs: [control.controlMeasureId],
            );

            final incidentData = await _dbHelper.query(
              'wildlife_conflict_incidents',
              where: 'id = ?',
              whereArgs: [control.wildlifeConflictIncidentId],
            );

            if (controlMeasureData.isNotEmpty) {
              control = control.copyWith(
                controlMeasure: ControlMeasure.fromJson(controlMeasureData.first)
              );
            }

            if (incidentData.isNotEmpty) {
              control = control.copyWith(
                wildlifeConflictIncident: WildlifeConflictIncident.fromJson(incidentData.first)
              );
            }

            return control;
          }).toList(),
        );

        return controls;
      } catch (dbException) {
        // If both API and DB fail, rethrow the original exception
        if (e is AppException) rethrow;
        throw AppException('Failed to fetch controls: ${e.toString()}');
      }
    }
  }

  // Save controls to local database
  Future<void> _saveControlsToDb(List<ProblemAnimalControl> controls) async {
    for (var control in controls) {
      await _dbHelper.insert('problem_animal_controls', {
        'id': control.id,
        'wildlife_conflict_incident_id': control.wildlifeConflictIncidentId,
        'control_measure_id': control.controlMeasureId,
        'organisation_id': control.organisationId,
        'date': control.date.toIso8601String(),
        'time': control.time,
        'description': control.description,
        'latitude': control.latitude,
        'longitude': control.longitude,
        'number_of_animals': control.numberOfAnimals,
        'created_at': control.createdAt?.toIso8601String(),
        'updated_at': control.updatedAt?.toIso8601String(),
        'sync_status': 'synced',
        'remote_id': control.id,
      });

      // Save control measure if available
      if (control.controlMeasure != null) {
        await _dbHelper.insert('control_measures', {
          'id': control.controlMeasure!.id,
          'name': control.controlMeasure!.name,
          'created_at': control.controlMeasure!.createdAt?.toIso8601String(),
          'updated_at': control.controlMeasure!.updatedAt?.toIso8601String(),
        }, where: 'id = ?', whereArgs: [control.controlMeasure!.id]);
      }

      // Save incident if available
      if (control.wildlifeConflictIncident != null) {
        await _dbHelper.insert('wildlife_conflict_incidents', {
          'id': control.wildlifeConflictIncident!.id,
          'organisation_id': control.wildlifeConflictIncident!.organisationId,
          'title': control.wildlifeConflictIncident!.title,
          'date': control.wildlifeConflictIncident!.date.toIso8601String(),
          'time': control.wildlifeConflictIncident!.time,
          'latitude': control.wildlifeConflictIncident!.latitude,
          'longitude': control.wildlifeConflictIncident!.longitude,
          'description': control.wildlifeConflictIncident!.description,
          'conflict_type_id': control.wildlifeConflictIncident!.conflictTypeId,
          'created_at': control.wildlifeConflictIncident!.createdAt?.toIso8601String(),
          'updated_at': control.wildlifeConflictIncident!.updatedAt?.toIso8601String(),
          'sync_status': 'synced',
          'remote_id': control.wildlifeConflictIncident!.id,
        }, where: 'id = ?', whereArgs: [control.wildlifeConflictIncident!.id]);
      }
    }
  }

  // Get a specific problem animal control
  Future<ProblemAnimalControl> getControl(int controlId) async {
    try {
      final response = await _apiService.get('/problem-animal-controls/$controlId');

      if (response['status'] == 'success') {
        return ProblemAnimalControl.fromJson(response['data']['control']);
      } else {
        throw AppException(response['message'] ?? 'Failed to fetch control');
      }
    } catch (e) {
      // If API call fails, try to get from local database
      try {
        final controlData = await _dbHelper.query(
          'problem_animal_controls',
          where: 'id = ? OR remote_id = ?',
          whereArgs: [controlId, controlId],
        );

        if (controlData.isEmpty) {
          throw NotFoundException('Control not found');
        }

        ProblemAnimalControl control = ProblemAnimalControl.fromJson(controlData.first);

        // Fetch related data
        final controlMeasureData = await _dbHelper.query(
          'control_measures',
          where: 'id = ?',
          whereArgs: [control.controlMeasureId],
        );

        final incidentData = await _dbHelper.query(
          'wildlife_conflict_incidents',
          where: 'id = ?',
          whereArgs: [control.wildlifeConflictIncidentId],
        );

        if (controlMeasureData.isNotEmpty) {
          control = control.copyWith(
            controlMeasure: ControlMeasure.fromJson(controlMeasureData.first)
          );
        }

        if (incidentData.isNotEmpty) {
          control = control.copyWith(
            wildlifeConflictIncident: WildlifeConflictIncident.fromJson(incidentData.first)
          );
        }

        return control;
      } catch (dbException) {
        // If both API and DB fail, rethrow the original exception
        if (e is AppException) rethrow;
        throw AppException('Failed to fetch control: ${e.toString()}');
      }
    }
  }

  // Create a new problem animal control
  Future<ProblemAnimalControl> createControl(ProblemAnimalControl control) async {
    try {
      final response = await _apiService.post(
        '/problem-animal-controls',
        data: control.toApiJson(),
      );

      if (response['status'] == 'success') {
        final createdControl = ProblemAnimalControl.fromJson(response['data']['control']);

        // Save to local database
        await _dbHelper.insert('problem_animal_controls', {
          'id': createdControl.id,
          'wildlife_conflict_incident_id': createdControl.wildlifeConflictIncidentId,
          'control_measure_id': createdControl.controlMeasureId,
          'organisation_id': createdControl.organisationId,
          'date': createdControl.date.toIso8601String(),
          'time': createdControl.time,
          'description': createdControl.description,
          'latitude': createdControl.latitude,
          'longitude': createdControl.longitude,
          'number_of_animals': createdControl.numberOfAnimals,
          'created_at': createdControl.createdAt?.toIso8601String(),
          'updated_at': createdControl.updatedAt?.toIso8601String(),
          'sync_status': 'synced',
          'remote_id': createdControl.id,
        });

        return createdControl;
      } else {
        throw AppException(response['message'] ?? 'Failed to create control');
      }
    } catch (e) {
      // If API call fails, save to local database with pending sync status
      if (e is AppException && !(e is NoInternetException || e is TimeoutException)) rethrow;

      // Save to local database with pending sync status
      final currentDateTime = DateTime.now().toIso8601String();
      final localId = await _dbHelper.insert('problem_animal_controls', {
        'wildlife_conflict_incident_id': control.wildlifeConflictIncidentId,
        'control_measure_id': control.controlMeasureId,
        'organisation_id': control.organisationId,
        'date': control.date.toIso8601String(),
        'time': control.time,
        'description': control.description,
        'latitude': control.latitude,
        'longitude': control.longitude,
        'number_of_animals': control.numberOfAnimals,
        'created_at': currentDateTime,
        'updated_at': currentDateTime,
        'sync_status': 'pending',
        'remote_id': null,
      });

      // Add to sync queue
      await _dbHelper.addToSyncQueue(
        'problem_animal_controls',
        localId,
        'create',
        jsonEncode(control.toApiJson()),
      );

      // Return the locally created control
      final controlData = await _dbHelper.query(
        'problem_animal_controls',
        where: 'id = ?',
        whereArgs: [localId],
      );

      return ProblemAnimalControl.fromJson(controlData.first);
    }
  }

  // Get control measures
  Future<List<ControlMeasure>> getControlMeasures() async {
    try {
      // Try to fetch from API first
      final response = await _apiService.get('/control-measures');

      if (response['status'] == 'success') {
        final measures = (response['data']['control_measures'] as List)
            .map((measure) => ControlMeasure.fromJson(measure))
            .toList();

        // Cache measures in local database
        for (var measure in measures) {
          await _dbHelper.insert('control_measures', {
            'id': measure.id,
            'name': measure.name,
            'created_at': measure.createdAt?.toIso8601String(),
            'updated_at': measure.updatedAt?.toIso8601String(),
          }, where: 'id = ?', whereArgs: [measure.id]);
        }

        return measures;
      } else {
        throw AppException(response['message'] ?? 'Failed to fetch control measures');
      }
    } catch (e) {
      // If API call fails, try to get from local database
      try {
        final measuresData = await _dbHelper.query('control_measures');
        return measuresData.map((measure) => ControlMeasure.fromJson(measure)).toList();
      } catch (dbException) {
        // If both API and DB fail, rethrow the original exception
        if (e is AppException) rethrow;
        throw AppException('Failed to fetch control measures: ${e.toString()}');
      }
    }
  }
}
