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
    if (_currentUser.value == null) return false;
    
    for (final role in _currentUser.value!.roles) {
      // Implement permission check logic based on your app's requirements
      // This is a placeholder
      if (role.name.toLowerCase() == 'admin') return true;
    }
    
    return false;
  }
}