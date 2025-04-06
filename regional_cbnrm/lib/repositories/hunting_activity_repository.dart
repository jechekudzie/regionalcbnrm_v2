import 'dart:convert';

import 'package:regional_cbnrm/core/api_service.dart';
import 'package:regional_cbnrm/core/app_exceptions.dart';
import 'package:regional_cbnrm/core/database_helper.dart';
import 'package:regional_cbnrm/models/hunting_model.dart';
import 'package:regional_cbnrm/models/species.dart';
import 'package:regional_cbnrm/models/wildlife_conflict_model.dart';

class HuntingActivityRepository {
  final ApiService _apiService = ApiService();
  final DatabaseHelper _dbHelper = DatabaseHelper();
  
  // Add const constructor for widget usage
  HuntingActivityRepository();

  // Get hunting activities for an organisation
  Future<List<HuntingActivity>> getHuntingActivities(int organisationId) async {
    try {
      // Try to fetch from API first
      final response = await _apiService.get('/organisations/$organisationId/hunting-activities');
      
      if (response['status'] == 'success') {
        // Check if response data contains paginated results
        if (response['data'] is Map && response['data'].containsKey('data')) {
          final activities = (response['data']['data'] as List)
              .map((activity) => HuntingActivity.fromJson(activity))
              .toList();
          
          // Cache activities in local database
          await _saveHuntingActivitiesToDb(activities);
          
          return activities;
        } else if (response['data'] is List) {
          // If data is directly a list (non-paginated)
          final activities = (response['data'] as List)
              .map((activity) => HuntingActivity.fromJson(activity))
              .toList();
          
          // Cache activities in local database
          await _saveHuntingActivitiesToDb(activities);
          
          return activities;
        } else {
          print('Unexpected data format: ${response['data']}');
          return [];
        }
      } else {
        throw AppException(response['message'] ?? 'Failed to fetch hunting activities');
      }
    } catch (e) {
      // If API call fails, try to get from local database
      try {
        final activitiesData = await _dbHelper.query(
          'hunting_activities',
          where: 'organisation_id = ?',
          whereArgs: [organisationId],
          orderBy: 'start_date DESC',
        );
        
        return activitiesData.map((data) => HuntingActivity.fromJson(data)).toList();
      } catch (dbException) {
        // If both API and DB fail, rethrow the original exception
        if (e is AppException) rethrow;
        throw AppException('Failed to fetch hunting activities: ${e.toString()}');
      }
    }
  }
  
  // Save hunting activities to local database
  Future<void> _saveHuntingActivitiesToDb(List<HuntingActivity> activities) async {
    for (var activity in activities) {
      await _dbHelper.insert('hunting_activities', {
        'id': activity.id,
        'organisation_id': activity.organisationId,
        'hunting_concession_id': activity.huntingConcessionId,
        'safari_operator_id': activity.safariOperatorId,
        'period': activity.period,
        'start_date': activity.startDate.toIso8601String(),
        'end_date': activity.endDate.toIso8601String(),
        'client_name': activity.clientName,
        'client_nationality': activity.clientNationality,
        'client_country_of_residence': activity.clientCountryOfResidence,
        'created_at': activity.createdAt?.toIso8601String(),
        'updated_at': activity.updatedAt?.toIso8601String(),
        'sync_status': 'synced',
        'remote_id': activity.id,
      }, where: 'id = ?', whereArgs: [activity.id]);
      
      // Save species if available
      if (activity.species != null) {
        for (var species in activity.species!) {
          await _dbHelper.insert('hunting_activity_species', {
            'id': species.id,
            'hunting_activity_id': species.huntingActivityId,
            'species_id': species.speciesId,
            'quantity': species.quantity,
            'quota_allocation_balance_id': species.quotaAllocationBalanceId,
            'created_at': species.createdAt?.toIso8601String(),
            'updated_at': species.updatedAt?.toIso8601String(),
          }, where: 'id = ?', whereArgs: [species.id]);
        }
      }
      
      // Save professional hunter licenses if available
      if (activity.professionalHunterLicenses != null) {
        for (var license in activity.professionalHunterLicenses!) {
          await _dbHelper.insert('professional_hunter_licenses', {
            'id': license.id,
            'hunting_activity_id': license.huntingActivityId,
            'name': license.name,
            'license_number': license.licenseNumber,
            'created_at': license.createdAt?.toIso8601String(),
            'updated_at': license.updatedAt?.toIso8601String(),
          }, where: 'id = ?', whereArgs: [license.id]);
        }
      }
    }
  }

  // Get a specific hunting activity
  Future<HuntingActivity> getHuntingActivity(int activityId) async {
    try {
      final response = await _apiService.get('/hunting-activities/$activityId');
      
      if (response['status'] == 'success') {
        return HuntingActivity.fromJson(response['data']['hunting_activity']);
      } else {
        throw AppException(response['message'] ?? 'Failed to fetch hunting activity');
      }
    } catch (e) {
      // If API call fails, try to get from local database
      try {
        final activityData = await _dbHelper.query(
          'hunting_activities',
          where: 'id = ? OR remote_id = ?',
          whereArgs: [activityId, activityId],
        );
        
        if (activityData.isEmpty) {
          throw NotFoundException('Hunting activity not found');
        }
        
        final activity = HuntingActivity.fromJson(activityData.first);
        
        // Get species
        final speciesData = await _dbHelper.query(
          'hunting_activity_species',
          where: 'hunting_activity_id = ?',
          whereArgs: [activity.id],
        );
        
        final speciesToReturn = await Future.wait(
          speciesData.map((data) async {
            final speciesInfo = await _dbHelper.query(
              'species',
              where: 'id = ?',
              whereArgs: [data['species_id']],
            );
            
            return HuntingActivitySpecies(
              id: data['id'],
              huntingActivityId: data['hunting_activity_id'],
              speciesId: data['species_id'],
              quantity: data['quantity'],
              quotaAllocationBalanceId: data['quota_allocation_balance_id'],
              createdAt: data['created_at'] != null ? DateTime.parse(data['created_at']) : null,
              updatedAt: data['updated_at'] != null ? DateTime.parse(data['updated_at']) : null,
              species: speciesInfo.isNotEmpty ? Species.fromApiJson(speciesInfo.first) : null,
            );
          }).toList(),
        );
        
        // Get licenses
        final licensesData = await _dbHelper.query(
          'professional_hunter_licenses',
          where: 'hunting_activity_id = ?',
          whereArgs: [activity.id],
        );
        
        final licenses = licensesData
            .map((data) => ProfessionalHunterLicense(
                  id: data['id'],
                  huntingActivityId: data['hunting_activity_id'],
                  name: data['name'],
                  licenseNumber: data['license_number'],
                  createdAt: data['created_at'] != null ? DateTime.parse(data['created_at']) : null,
                  updatedAt: data['updated_at'] != null ? DateTime.parse(data['updated_at']) : null,
                ))
            .toList();
        
        // Get hunting concession
        final concessionData = await _dbHelper.query(
          'hunting_concessions',
          where: 'id = ?',
          whereArgs: [activity.huntingConcessionId],
        );
        
        final concession = concessionData.isNotEmpty
            ? HuntingConcession.fromJson(concessionData.first)
            : null;
        
        return activity.copyWith(
          species: speciesToReturn,
          professionalHunterLicenses: licenses,
          huntingConcession: concession,
        );
      } catch (dbException) {
        // If both API and DB fail, rethrow the original exception
        if (e is AppException) rethrow;
        throw AppException('Failed to fetch hunting activity: ${e.toString()}');
      }
    }
  }

  // Create a new hunting activity
  Future<HuntingActivity> createHuntingActivity(
    HuntingActivity activity, {
    Map<String, dynamic>? additionalData,
  }) async {
    try {
      // Use additionalData if provided, otherwise use standard API JSON
      final data = additionalData ?? activity.toApiJson();
      
      // Log the request data for debugging
      print('Creating hunting activity with data: $data');
      
      final response = await _apiService.post(
        '/hunting-activities',
        data: data,
      );
      
      if (response['status'] == 'success') {
        final createdActivity = HuntingActivity.fromJson(response['data']['hunting_activity']);
        
        // Save to local database
        await _dbHelper.insert('hunting_activities', {
          'id': createdActivity.id,
          'organisation_id': createdActivity.organisationId,
          'hunting_concession_id': createdActivity.huntingConcessionId,
          'safari_operator_id': createdActivity.safariOperatorId,
          'period': createdActivity.period,
          'start_date': createdActivity.startDate.toIso8601String(),
          'end_date': createdActivity.endDate.toIso8601String(),
          'client_name': createdActivity.clientName,
          'client_nationality': createdActivity.clientNationality,
          'client_country_of_residence': createdActivity.clientCountryOfResidence,
          'created_at': createdActivity.createdAt?.toIso8601String(),
          'updated_at': createdActivity.updatedAt?.toIso8601String(),
          'sync_status': 'synced',
          'remote_id': createdActivity.id,
        });
        
        return createdActivity;
      } else {
        throw AppException(response['message'] ?? 'Failed to create hunting activity');
      }
    } catch (e) {
      // If API call fails, save to local database with pending sync status
      if (e is AppException && !(e is NoInternetException || e is TimeoutException)) rethrow;
      
      // Save to local database with pending sync status
      final currentDateTime = DateTime.now().toIso8601String();
      final localId = await _dbHelper.insert('hunting_activities', {
        'organisation_id': activity.organisationId,
        'hunting_concession_id': activity.huntingConcessionId,
        'safari_operator_id': activity.safariOperatorId,
        'period': activity.period,
        'start_date': activity.startDate.toIso8601String(),
        'end_date': activity.endDate.toIso8601String(),
        'client_name': activity.clientName,
        'client_nationality': activity.clientNationality,
        'client_country_of_residence': activity.clientCountryOfResidence,
        'created_at': currentDateTime,
        'updated_at': currentDateTime,
        'sync_status': 'pending',
        'remote_id': null,
      });
      
      // Add to sync queue
      final syncData = additionalData ?? activity.toApiJson();
      await _dbHelper.addToSyncQueue(
        'hunting_activities',
        localId,
        'create',
        jsonEncode(syncData),
      );
      
      // Return the locally created activity
      final activityData = await _dbHelper.query(
        'hunting_activities',
        where: 'id = ?',
        whereArgs: [localId],
      );
      
      return HuntingActivity.fromJson(activityData.first);
    }
  }

  // Get all hunting concessions
  Future<List<HuntingConcession>> getHuntingConcessions(int organisationId) async {
    try {
      // Try to fetch from API first
      final response = await _apiService.get('/organisations/$organisationId/hunting-concessions');
      
      if (response['status'] == 'success') {
        // Check if response data contains paginated results
        if (response['data'] is Map && response['data'].containsKey('data')) {
          final concessions = (response['data']['data'] as List)
              .map((concession) => HuntingConcession.fromJson(concession))
              .toList();
          
          // Cache concessions in local database
          for (var concession in concessions) {
            await _dbHelper.insert('hunting_concessions', {
              'id': concession.id,
              'organisation_id': concession.organisationId,
              'safari_id': concession.safariId,
              'name': concession.name,
              'hectarage': concession.hectarage,
              'description': concession.description,
              'latitude': concession.latitude,
              'longitude': concession.longitude,
              'created_at': concession.createdAt?.toIso8601String(),
              'updated_at': concession.updatedAt?.toIso8601String(),
            }, where: 'id = ?', whereArgs: [concession.id]);
          }
          
          return concessions;
        } else if (response['data'] is List) {
          // If data is directly a list (non-paginated)
          final concessions = (response['data'] as List)
              .map((concession) => HuntingConcession.fromJson(concession))
              .toList();
          
          // Cache concessions in local database
          for (var concession in concessions) {
            await _dbHelper.insert('hunting_concessions', {
              'id': concession.id,
              'organisation_id': concession.organisationId,
              'safari_id': concession.safariId,
              'name': concession.name,
              'hectarage': concession.hectarage,
              'description': concession.description,
              'latitude': concession.latitude,
              'longitude': concession.longitude,
              'created_at': concession.createdAt?.toIso8601String(),
              'updated_at': concession.updatedAt?.toIso8601String(),
            }, where: 'id = ?', whereArgs: [concession.id]);
          }
          
          return concessions;
        } else {
          print('Unexpected data format: ${response['data']}');
          return [];
        }
      } else {
        throw AppException(response['message'] ?? 'Failed to fetch hunting concessions');
      }
    } catch (e) {
      // If API call fails, try to get from local database
      try {
        final concessionsData = await _dbHelper.query(
          'hunting_concessions',
          where: 'organisation_id = ?',
          whereArgs: [organisationId],
          orderBy: 'name ASC',
        );
        
        return concessionsData.map((data) => HuntingConcession.fromJson(data)).toList();
      } catch (dbException) {
        // If both API and DB fail, rethrow the original exception
        if (e is AppException) rethrow;
        throw AppException('Failed to fetch hunting concessions: ${e.toString()}');
      }
    }
  }

  // Create a new hunting concession
  Future<HuntingConcession> createHuntingConcession(
    int organisationId,
    String name,
    double? hectarage,
    String? description,
    double? latitude,
    double? longitude,
    int? safariId,
  ) async {
    try {
      final data = {
        'organisation_id': organisationId,
        'name': name,
        'hectarage': hectarage,
        'description': description,
        'latitude': latitude,
        'longitude': longitude,
        'safari_id': safariId,
      };
      
      final response = await _apiService.post(
        '/hunting-concessions',
        data: data,
      );
      
      if (response['status'] == 'success') {
        final createdConcession = HuntingConcession.fromJson(response['data']['hunting_concession']);
        
        // Save to local database
        await _dbHelper.insert('hunting_concessions', {
          'id': createdConcession.id,
          'organisation_id': createdConcession.organisationId,
          'safari_id': createdConcession.safariId,
          'name': createdConcession.name,
          'hectarage': createdConcession.hectarage,
          'description': createdConcession.description,
          'latitude': createdConcession.latitude,
          'longitude': createdConcession.longitude,
          'created_at': createdConcession.createdAt?.toIso8601String(),
          'updated_at': createdConcession.updatedAt?.toIso8601String(),
        });
        
        return createdConcession;
      } else {
        throw AppException(response['message'] ?? 'Failed to create hunting concession');
      }
    } catch (e) {
      // If API call fails, save to local database with pending sync status
      if (e is AppException && !(e is NoInternetException || e is TimeoutException)) rethrow;
      
      // Save to local database with pending sync status
      final currentDateTime = DateTime.now().toIso8601String();
      final localId = await _dbHelper.insert('hunting_concessions', {
        'organisation_id': organisationId,
        'safari_id': safariId,
        'name': name,
        'hectarage': hectarage,
        'description': description,
        'latitude': latitude,
        'longitude': longitude,
        'created_at': currentDateTime,
        'updated_at': currentDateTime,
      });
      
      // Add to sync queue
      final data = {
        'organisation_id': organisationId,
        'name': name,
        'hectarage': hectarage,
        'description': description,
        'latitude': latitude,
        'longitude': longitude,
        'safari_id': safariId,
      };
      
      await _dbHelper.addToSyncQueue(
        'hunting_concessions',
        localId,
        'create',
        jsonEncode(data),
      );
      
      // Return the locally created concession
      final concessionData = await _dbHelper.query(
        'hunting_concessions',
        where: 'id = ?',
        whereArgs: [localId],
      );
      
      return HuntingConcession.fromJson(concessionData.first);
    }
  }

  // Get all quota allocations
  Future<List<QuotaAllocation>> getQuotaAllocations(int organisationId, {String? period}) async {
    try {
      // Try to fetch from API first
      String endpoint = '/organisations/$organisationId/quota-allocations';
      if (period != null) {
        endpoint += '?period=$period';
      }
      
      final response = await _apiService.get(endpoint);
      
      if (response['status'] == 'success') {
        // Check if response data contains paginated results
        if (response['data'] is Map && response['data'].containsKey('data')) {
          final allocations = (response['data']['data'] as List)
              .map((allocation) => QuotaAllocation.fromJson(allocation))
              .toList();
          
          // Cache allocations in local database
          for (var allocation in allocations) {
            await _dbHelper.insert('quota_allocations', {
              'id': allocation.id,
              'organisation_id': allocation.organisationId,
              'species_id': allocation.speciesId,
              'hunting_quota': allocation.huntingQuota,
              'rational_killing_quota': allocation.rationalKillingQuota,
              'period': allocation.period,
              'start_date': allocation.startDate.toIso8601String(),
              'end_date': allocation.endDate.toIso8601String(),
              'created_at': allocation.createdAt?.toIso8601String(),
              'updated_at': allocation.updatedAt?.toIso8601String(),
            }, where: 'id = ?', whereArgs: [allocation.id]);
            
            // Save quota balance if available
            if (allocation.quotaAllocationBalance != null) {
              await _dbHelper.insert('quota_allocation_balances', {
                'id': allocation.quotaAllocationBalance!.id,
                'quota_allocation_id': allocation.quotaAllocationBalance!.quotaAllocationId,
                'allocated': allocation.quotaAllocationBalance!.allocated,
                'off_take': allocation.quotaAllocationBalance!.offTake,
                'remaining': allocation.quotaAllocationBalance!.remaining,
                'created_at': allocation.quotaAllocationBalance!.createdAt?.toIso8601String(),
                'updated_at': allocation.quotaAllocationBalance!.updatedAt?.toIso8601String(),
              }, where: 'id = ?', whereArgs: [allocation.quotaAllocationBalance!.id]);
            }
          }
          
          return allocations;
        } else if (response['data'] is List) {
          // If data is directly a list (non-paginated)
          final allocations = (response['data'] as List)
              .map((allocation) => QuotaAllocation.fromJson(allocation))
              .toList();
          
          // Cache allocations in local database
          for (var allocation in allocations) {
            await _dbHelper.insert('quota_allocations', {
              'id': allocation.id,
              'organisation_id': allocation.organisationId,
              'species_id': allocation.speciesId,
              'hunting_quota': allocation.huntingQuota,
              'rational_killing_quota': allocation.rationalKillingQuota,
              'period': allocation.period,
              'start_date': allocation.startDate.toIso8601String(),
              'end_date': allocation.endDate.toIso8601String(),
              'created_at': allocation.createdAt?.toIso8601String(),
              'updated_at': allocation.updatedAt?.toIso8601String(),
            }, where: 'id = ?', whereArgs: [allocation.id]);
            
            // Save quota balance if available
            if (allocation.quotaAllocationBalance != null) {
              await _dbHelper.insert('quota_allocation_balances', {
                'id': allocation.quotaAllocationBalance!.id,
                'quota_allocation_id': allocation.quotaAllocationBalance!.quotaAllocationId,
                'allocated': allocation.quotaAllocationBalance!.allocated,
                'off_take': allocation.quotaAllocationBalance!.offTake,
                'remaining': allocation.quotaAllocationBalance!.remaining,
                'created_at': allocation.quotaAllocationBalance!.createdAt?.toIso8601String(),
                'updated_at': allocation.quotaAllocationBalance!.updatedAt?.toIso8601String(),
              }, where: 'id = ?', whereArgs: [allocation.quotaAllocationBalance!.id]);
            }
          }
          
          return allocations;
        } else {
          print('Unexpected data format: ${response['data']}');
          return [];
        }
      } else {
        throw AppException(response['message'] ?? 'Failed to fetch quota allocations');
      }
    } catch (e) {
      // If API call fails, try to get from local database
      try {
        String query = 'organisation_id = ?';
        List<dynamic> args = [organisationId];
        
        if (period != null) {
          query += ' AND period = ?';
          args.add(period);
        }
        
        final allocationsData = await _dbHelper.query(
          'quota_allocations',
          where: query,
          whereArgs: args,
          orderBy: 'species_id ASC',
        );
        
        return await Future.wait(allocationsData.map((data) async {
          final allocation = QuotaAllocation.fromJson(data);
          
          // Get balance
          final balanceData = await _dbHelper.query(
            'quota_allocation_balances',
            where: 'quota_allocation_id = ?',
            whereArgs: [allocation.id],
          );
          
          final balance = balanceData.isNotEmpty
              ? QuotaAllocationBalance.fromJson(balanceData.first)
              : null;
          
          // Get species
          final speciesData = await _dbHelper.query(
            'species',
            where: 'id = ?',
            whereArgs: [allocation.speciesId],
          );
          
          final species = speciesData.isNotEmpty ? Species.fromApiJson(speciesData.first) : null;
          
          return allocation.copyWith(
            quotaAllocationBalance: balance,
            species: species,
          );
        }).toList());
      } catch (dbException) {
        // If both API and DB fail, rethrow the original exception
        if (e is AppException) rethrow;
        throw AppException('Failed to fetch quota allocations: ${e.toString()}');
      }
    }
  }

  // Create a new quota allocation
  Future<QuotaAllocation> createQuotaAllocation(
    int organisationId,
    int speciesId,
    int huntingQuota,
    int rationalKillingQuota,
    DateTime startDate,
    DateTime endDate,
    String? notes,
  ) async {
    try {
      final period = startDate.year.toString();
      
      final data = {
        'organisation_id': organisationId,
        'species_id': speciesId,
        'hunting_quota': huntingQuota,
        'rational_killing_quota': rationalKillingQuota,
        'period': period,
        'start_date': startDate.toIso8601String().split('T')[0],
        'end_date': endDate.toIso8601String().split('T')[0],
        'notes': notes,
      };
      
      final response = await _apiService.post(
        '/quota-allocations',
        data: data,
      );
      
      if (response['status'] == 'success') {
        final createdAllocation = QuotaAllocation.fromJson(response['data']['quota_allocation']);
        
        // Save to local database
        await _dbHelper.insert('quota_allocations', {
          'id': createdAllocation.id,
          'organisation_id': createdAllocation.organisationId,
          'species_id': createdAllocation.speciesId,
          'hunting_quota': createdAllocation.huntingQuota,
          'rational_killing_quota': createdAllocation.rationalKillingQuota,
          'period': createdAllocation.period,
          'start_date': createdAllocation.startDate.toIso8601String(),
          'end_date': createdAllocation.endDate.toIso8601String(),
          'created_at': createdAllocation.createdAt?.toIso8601String(),
          'updated_at': createdAllocation.updatedAt?.toIso8601String(),
        });
        
        // Save quota balance if available
        if (createdAllocation.quotaAllocationBalance != null) {
          await _dbHelper.insert('quota_allocation_balances', {
            'id': createdAllocation.quotaAllocationBalance!.id,
            'quota_allocation_id': createdAllocation.quotaAllocationBalance!.quotaAllocationId,
            'allocated': createdAllocation.quotaAllocationBalance!.allocated,
            'off_take': createdAllocation.quotaAllocationBalance!.offTake,
            'remaining': createdAllocation.quotaAllocationBalance!.remaining,
            'created_at': createdAllocation.quotaAllocationBalance!.createdAt?.toIso8601String(),
            'updated_at': createdAllocation.quotaAllocationBalance!.updatedAt?.toIso8601String(),
          });
        }
        
        return createdAllocation;
      } else {
        throw AppException(response['message'] ?? 'Failed to create quota allocation');
      }
    } catch (e) {
      // If API call fails, save to local database with pending sync status
      if (e is AppException && !(e is NoInternetException || e is TimeoutException)) rethrow;
      
      // Save to local database with pending sync status
      final period = startDate.year.toString();
      final currentDateTime = DateTime.now().toIso8601String();
      final localId = await _dbHelper.insert('quota_allocations', {
        'organisation_id': organisationId,
        'species_id': speciesId,
        'hunting_quota': huntingQuota,
        'rational_killing_quota': rationalKillingQuota,
        'period': period,
        'start_date': startDate.toIso8601String(),
        'end_date': endDate.toIso8601String(),
        'created_at': currentDateTime,
        'updated_at': currentDateTime,
      });
      
      // Add to sync queue
      final data = {
        'organisation_id': organisationId,
        'species_id': speciesId,
        'hunting_quota': huntingQuota,
        'rational_killing_quota': rationalKillingQuota,
        'period': period,
        'start_date': startDate.toIso8601String().split('T')[0],
        'end_date': endDate.toIso8601String().split('T')[0],
        'notes': notes,
      };
      
      await _dbHelper.addToSyncQueue(
        'quota_allocations',
        localId,
        'create',
        jsonEncode(data),
      );
      
      // Return the locally created allocation
      final allocationData = await _dbHelper.query(
        'quota_allocations',
        where: 'id = ?',
        whereArgs: [localId],
      );
      
      return QuotaAllocation.fromJson(allocationData.first);
    }
  }
}