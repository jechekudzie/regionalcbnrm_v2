import 'package:resource_africa/core/api_service.dart';
import 'package:resource_africa/core/app_exceptions.dart';
import 'package:resource_africa/core/database_helper.dart';
import 'package:resource_africa/models/organisation_model.dart';
import 'package:resource_africa/models/user_model.dart';
import 'package:resource_africa/utils/app_preferences.dart';

class OrganisationRepository {
  final ApiService _apiService = ApiService();
  final AppPreferences _preferences = AppPreferences();
  final DatabaseHelper _dbHelper = DatabaseHelper();

  Future<List<Organisation>> getUserOrganisations() async {
    try {
      // Try to fetch from API first
      final response = await _apiService.get('/organisations');
      
      if (response['status'] == 'success') {
        final organisations = (response['data']['organisations'] as List)
            .map((org) => Organisation.fromJson(org))
            .toList();
        
        // Cache organisations in local database
        await _saveOrganisationsToDb(organisations);
        
        return organisations;
      } else {
        throw AppException(response['message'] ?? 'Failed to fetch organisations');
      }
    } catch (e) {
      // If API call fails, try to get from local database
      try {
        final organisationsData = await _dbHelper.query('organisations');
        return organisationsData.map((org) => Organisation.fromJson(org)).toList();
      } catch (dbException) {
        // If both API and DB fail, rethrow the original exception
        if (e is AppException) rethrow;
        throw AppException('Failed to fetch organisations: ${e.toString()}');
      }
    }
  }

  Future<void> _saveOrganisationsToDb(List<Organisation> organisations) async {
    // Clear existing organisations
    await _dbHelper.delete('organisations');
    
    // Insert new organisations
    for (var org in organisations) {
      await _dbHelper.insert('organisations', {
        'id': org.id,
        'name': org.name,
        'type_id': org.organisationTypeId,
        'type_name': org.organisationType?.name,
        'parent_id': org.parentId,
        'created_at': org.createdAt?.toIso8601String(),
        'updated_at': org.updatedAt?.toIso8601String(),
      });
    }
  }

  Future<OrganisationDetail> getOrganisationDetail(int organisationId) async {
    try {
      final response = await _apiService.get('/organisations/$organisationId');
      
      if (response['status'] == 'success') {
        return OrganisationDetail.fromJson(response['data']['organisation']);
      } else {
        throw AppException(response['message'] ?? 'Failed to fetch organisation details');
      }
    } catch (e) {
      if (e is AppException) rethrow;
      throw AppException('Failed to fetch organisation details: ${e.toString()}');
    }
  }

  Future<List<Organisation>> getChildOrganisations(int organisationId) async {
    try {
      final response = await _apiService.get('/organisations/$organisationId/children');
      
      if (response['status'] == 'success') {
        return (response['data']['children'] as List)
            .map((org) => Organisation.fromJson(org))
            .toList();
      } else {
        throw AppException(response['message'] ?? 'Failed to fetch child organisations');
      }
    } catch (e) {
      if (e is AppException) rethrow;
      throw AppException('Failed to fetch child organisations: ${e.toString()}');
    }
  }

  Future<List<Role>> getOrganisationRoles(int organisationId) async {
    try {
      final response = await _apiService.get('/organisations/$organisationId/roles');
      
      if (response['status'] == 'success') {
        return (response['data']['roles'] as List)
            .map((role) => Role.fromJson(role))
            .toList();
      } else {
        throw AppException(response['message'] ?? 'Failed to fetch organisation roles');
      }
    } catch (e) {
      if (e is AppException) rethrow;
      throw AppException('Failed to fetch organisation roles: ${e.toString()}');
    }
  }

  Future<Organisation?> getSelectedOrganisation() async {
    return await _preferences.getSelectedOrganisation();
  }

  Future<void> setSelectedOrganisation(Organisation organisation) async {
    await _preferences.setSelectedOrganisation(organisation);
  }
}