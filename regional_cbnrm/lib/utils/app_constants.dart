import 'dart:io';

class AppConstants {
  // API URLs
  // For debugging, use local IP instead of localhost for Android Emulator
  static final String baseUrl = Platform.isAndroid
      ? 'http://192.168.10.114:8000/api/v1'  // Android emulator
      : 'http://192.168.10.114:8000/api/v1'; // iOS simulator

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
  static const String databaseName = 'regional_cbnrm.db';
  static const int databaseVersion = 2;

  // App themes and styles
  static const String appName = 'Regional CBNRM';
  static const String appLogoPath = 'assets/images/logo.png';

  // Permission constants
  // Wildlife Conflict
  static const String viewWildlifeConflicts = 'view-wildlife-conflicts';
  static const String createWildlifeConflict = 'create-wildlife-conflict';
  static const String editWildlifeConflict = 'edit-wildlife-conflict';
  static const String deleteWildlifeConflict = 'delete-wildlife-conflict';

  // Problem Animal Control
  static const String viewProblemAnimalControls = 'view-problem-animal-controls';
  static const String createProblemAnimalControl = 'create-problem-animal-control';
  static const String editProblemAnimalControl = 'edit-problem-animal-control';
  static const String deleteProblemAnimalControl = 'delete-problem-animal-control';

  // Poaching Incidents
  static const String viewPoachingIncidents = 'view-poaching-incidents';
  static const String createPoachingIncident = 'create-poaching-incident';
  static const String editPoachingIncident = 'edit-poaching-incident';
  static const String deletePoachingIncident = 'delete-poaching-incident';

  // Hunting Activities
  static const String viewHuntingActivities = 'view-hunting-activities';
  static const String createHuntingActivity = 'create-hunting-activity';
  static const String editHuntingActivity = 'edit-hunting-activity';
  static const String deleteHuntingActivity = 'delete-hunting-activity';

  // Hunting Concessions
  static const String viewHuntingConcessions = 'view-hunting-concessions';
  static const String createHuntingConcession = 'create-hunting-concession';
  static const String editHuntingConcession = 'edit-hunting-concession';
  static const String deleteHuntingConcession = 'delete-hunting-concession';

  // Quota Allocations
  static const String viewQuotaAllocations = 'view-quota-allocations';
  static const String createQuotaAllocation = 'create-quota-allocation';
  static const String editQuotaAllocation = 'edit-quota-allocation';
  static const String deleteQuotaAllocation = 'delete-quota-allocation';

  // Role constants
  static const String roleAdmin = 'admin';
  static const String roleManager = 'manager';
  static const String roleFieldOfficer = 'field-officer';
  static const String roleViewer = 'viewer';

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
  static const String notificationChannelId = 'regional_cbnrm_channel';
  static const String notificationChannelName = 'Regional CBNRM Notifications';
  static const String notificationChannelDescription = 'Notifications for the Regional CBNRM app';
}
