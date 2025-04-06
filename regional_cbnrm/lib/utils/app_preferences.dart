import 'dart:convert';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:regional_cbnrm/models/user_model.dart';
// Added import
import 'package:regional_cbnrm/utils/app_constants.dart';

class AppPreferences {
  static final AppPreferences _instance = AppPreferences._internal();
  factory AppPreferences() => _instance;
  AppPreferences._internal();

  final FlutterSecureStorage _secureStorage = const FlutterSecureStorage();

  // Authentication token
  Future<String?> getAuthToken() async {
    return await _secureStorage.read(key: AppConstants.authTokenKey);
  }

  Future<void> setAuthToken(String token) async {
    await _secureStorage.write(key: AppConstants.authTokenKey, value: token);
  }

  Future<void> clearAuthToken() async {
    await _secureStorage.delete(key: AppConstants.authTokenKey);
  }

  // User data
  Future<User?> getUser() async {
    final prefs = await SharedPreferences.getInstance();
    final userJson = prefs.getString(AppConstants.userKey);
    if (userJson == null) return null;
    return User.fromJson(jsonDecode(userJson));
  }

  Future<void> setUser(User user) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(AppConstants.userKey, jsonEncode(user.toJson()));
  }

  Future<void> clearUser() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove(AppConstants.userKey);
  }

  // Selected organisation
  Future<Organisation?> getSelectedOrganisation() async {
    final prefs = await SharedPreferences.getInstance();
    final orgJson = prefs.getString(AppConstants.selectedOrganisationKey);
    if (orgJson == null) return null;
    return Organisation.fromJson(jsonDecode(orgJson));
  }

  Future<void> setSelectedOrganisation(Organisation organisation) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(AppConstants.selectedOrganisationKey, jsonEncode(organisation.toJson()));
  }

  Future<void> clearSelectedOrganisation() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove(AppConstants.selectedOrganisationKey);
  }

  // Clear all app data
  Future<void> clearAllData() async {
    await clearAuthToken();
    
    final prefs = await SharedPreferences.getInstance();
    await prefs.clear();
  }
}