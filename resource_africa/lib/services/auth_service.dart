import 'package:get/get.dart';
import 'package:resource_africa/models/user_model.dart';
import 'package:resource_africa/repositories/auth_repository.dart';
import 'package:resource_africa/utils/app_routes.dart';
import 'package:device_info_plus/device_info_plus.dart';
import 'dart:io';

class AuthService extends GetxService {
  final AuthRepository _authRepository = AuthRepository();

  final Rx<User?> _currentUser = Rx<User?>(null);

  User? get currentUser => _currentUser.value;

  bool get isAuthenticated => _currentUser.value != null;

  // For reactive UI updates
  RxBool get isLoggedIn => RxBool(_currentUser.value != null);

  Future<AuthService> init() async {
    // Check if user is already logged in
    await _loadCurrentUser();
    return this;
  }

  Future<void> _loadCurrentUser() async {
    final user = await _authRepository.getCurrentUser();
    _currentUser.value = user;
  }

  Future<bool> login(String email, String password) async {
    try {
      // Get device name for token identification
      final deviceName = await _getDeviceName();

      final user = await _authRepository.login(email, password, deviceName);
      _currentUser.value = user;
      return true;
    } catch (e) {
      return false;
    }
  }

  Future<void> logout() async {
    await _authRepository.logout();
    _currentUser.value = null;
    Get.offAllNamed(AppRoutes.login);
  }

  Future<bool> refreshUser() async {
    try {
      final user = await _authRepository.refreshCurrentUser();
      _currentUser.value = user;
      return true;
    } catch (e) {
      return false;
    }
  }

  Future<String> _getDeviceName() async {
    final deviceInfo = DeviceInfoPlugin();
    String deviceName = 'Unknown Device';

    try {
      if (Platform.isAndroid) {
        final androidInfo = await deviceInfo.androidInfo;
        deviceName = '${androidInfo.brand} ${androidInfo.model}';
      } else if (Platform.isIOS) {
        final iosInfo = await deviceInfo.iosInfo;
        deviceName = '${iosInfo.name} ${iosInfo.model}';
      }
    } catch (e) {
      // If we can't get device info, just use a generic name
      deviceName = 'Mobile Device';
    }

    return deviceName;
  }

  // Check user permissions
  bool hasPermission(String permission) {
    // No user, no permissions
    if (_currentUser.value == null) return false;

    // For development: grant all permissions to authenticated users
    return true;

    /* Original permission logic - would be uncommented for production
    // Super admin has all permissions
    if (_hasRole(AppConstants.roleAdmin)) return true;

    // Check specific permission logic
    if (permission.startsWith('view-')) {
      // Viewers can view most resources
      if (_hasRole(AppConstants.roleViewer)) {
        // Except for sensitive ones (requiring specific permission checks)
        if (permission == AppConstants.viewPoachingIncidents ||
            permission == AppConstants.viewHuntingActivities ||
            permission == AppConstants.viewQuotaAllocations) {
          return _hasSpecificPermission(permission);
        }
        return true;
      }
    }

    // Create permissions
    if (permission.startsWith('create-')) {
      // Field officers and managers can create records
      if (_hasRole(AppConstants.roleFieldOfficer) || _hasRole(AppConstants.roleManager)) {
        return true;
      }
      return _hasSpecificPermission(permission);
    }

    // Edit permissions
    if (permission.startsWith('edit-')) {
      // Managers can edit records
      if (_hasRole(AppConstants.roleManager)) {
        return true;
      }
      return _hasSpecificPermission(permission);
    }

    // Delete permissions - generally restricted to managers
    if (permission.startsWith('delete-')) {
      return _hasRole(AppConstants.roleManager) || _hasSpecificPermission(permission);
    }

    // For any other permissions, check specific permissions
    return _hasSpecificPermission(permission);
    */
  }

  // Check if user has a specific role
  bool _hasRole(String roleName) {
    if (_currentUser.value == null) return false;

    for (final role in _currentUser.value!.roles) {
      if (role.name.toLowerCase() == roleName.toLowerCase()) {
        return true;
      }
    }

    return false;
  }

  // Check if user has specific permission in their permissions list
  bool _hasSpecificPermission(String permission) {
    if (_currentUser.value == null) return false;

    // This would ideally check against a permissions list from the user model
    // For now, we're just checking roles as a placeholder

    for (final role in _currentUser.value!.roles) {
      // This is a placeholder - would need to be replaced with actual permission checks
      // based on the permissions structure in your API
      if (role.name.toLowerCase() == 'admin') return true;
      if (role.name.toLowerCase() == 'manager') return true;
    }

    return false;
  }
}
