
import 'package:resource_africa/core/api_service.dart';
import 'package:resource_africa/core/app_exceptions.dart';
import 'package:resource_africa/models/user_model.dart';
import 'package:resource_africa/utils/app_preferences.dart';

class AuthRepository {
  final ApiService _apiService = ApiService();
  final AppPreferences _preferences = AppPreferences();

  Future<User> login(String email, String password, String deviceName) async {
    try {
      final response = await _apiService.post('/login', data: {
        'email': email,
        'password': password,
        'device_name': deviceName,
      });

      if (response['status'] == 'success') {
        final user = User.fromJson(response['data']['user']);
        final token = response['data']['token'];

        // Save auth token and user data
        await _preferences.setAuthToken(token);
        await _preferences.setUser(user);

        return user;
      } else {
        throw AppException(response['message'] ?? 'Login failed');
      }
    } catch (e) {
      if (e is AppException) rethrow;
      throw AppException('Login failed: ${e.toString()}');
    }
  }

  Future<User?> getCurrentUser() async {
    return await _preferences.getUser();
  }

  Future<User> refreshCurrentUser() async {
    try {
      final response = await _apiService.get('/user');

      if (response['status'] == 'success') {
        final user = User.fromJson(response['data']['user']);
        await _preferences.setUser(user);
        return user;
      } else {
        throw AppException(response['message'] ?? 'Failed to refresh user data');
      }
    } catch (e) {
      if (e is AppException) rethrow;
      throw AppException('Failed to refresh user data: ${e.toString()}');
    }
  }

  Future<void> logout() async {
    try {
      await _apiService.post('/logout');
      await _preferences.clearAuthToken();
      await _preferences.clearUser();
      await _preferences.clearSelectedOrganisation();
    } catch (e) {
      // Still clear local data even if the API call fails
      await _preferences.clearAuthToken();
      await _preferences.clearUser();
      await _preferences.clearSelectedOrganisation();

      if (e is AppException) rethrow;
      throw AppException('Logout failed: ${e.toString()}');
    }
  }

  Future<bool> isAuthenticated() async {
    final token = await _preferences.getAuthToken();
    return token != null && token.isNotEmpty;
  }
}