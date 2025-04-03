import 'dart:io';

class AppConstants {
  // API URLs
  // For debugging, use local IP instead of localhost for Android Emulator
  static final String baseUrl = Platform.isAndroid 
      ? 'http://10.0.2.2/regionalcbnrm_v2/api/v1'  // Android emulator
      : 'http://localhost/regionalcbnrm_v2/api/v1'; // iOS simulator
  
  // App configuration
  static const bool isProduction = false;
  static const String appVersion = '1.0.0';
  
  // Shared preferences keys
  static const String authTokenKey = 'auth_token';
  static const String userKey = 'user';
  static const String selectedOrganisationKey = 'selected_organisation';
  static const String offlineDataKey = 'offline_data';
  static const String appSettingsKey = 'app_settings';
  static const String lastSyncKey = 'last_sync_timestamp';
  
  // Database
  static const String databaseName = 'resource_africa.db';
  static const int databaseVersion = 1;
  
  // App themes and styles
  static const String appName = 'Resource Africa';
  static const String appLogoPath = 'assets/images/logo.png';
  
  // Error messages
  static const String connectionError = 'No internet connection. Data will be saved locally and synced when connection is restored.';
  static const String serverError = 'Server error occurred. Please try again later.';
  static const String unauthorizedError = 'Your session has expired. Please login again.';
  static const String notFoundError = 'The requested information could not be found.';
  static const String validationError = 'Please check your input and try again.';
  static const String timeoutError = 'The server is taking too long to respond. Please try again later.';
  static const String genericError = 'Something went wrong. Please try again.';
  static const String dbError = 'Database error occurred. Please restart the app.';
  
  // Success messages
  static const String loginSuccess = 'Login successful';
  static const String logoutSuccess = 'Logout successful';
  static const String createSuccess = 'Created successfully';
  static const String updateSuccess = 'Updated successfully';
  static const String deleteSuccess = 'Deleted successfully';
  static const String syncSuccess = 'Synchronization successful';
  
  // Timeout configurations
  static const Duration connectionTimeout = Duration(seconds: 30);
  static const Duration receiveTimeout = Duration(seconds: 30);
  static const Duration autoSyncInterval = Duration(minutes: 15);
  
  // Location settings
  static const double defaultZoomLevel = 15.0;
  static const double defaultLatitude = -18.3825; // Zimbabwe center
  static const double defaultLongitude = 30.0000; // Zimbabwe center
  
  // Local notification channel
  static const String notificationChannelId = 'resource_africa_channel';
  static const String notificationChannelName = 'Resource Africa Notifications';
  static const String notificationChannelDescription = 'Notifications for the Resource Africa app';
}