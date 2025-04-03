import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:resource_africa/core/database_helper.dart';
import 'package:resource_africa/services/auth_service.dart';
import 'package:resource_africa/services/connectivity_service.dart';
import 'package:resource_africa/utils/app_constants.dart';
import 'package:resource_africa/utils/app_routes.dart';
import 'package:resource_africa/utils/logger.dart';
import 'package:shared_preferences/shared_preferences.dart';

class DebugSettingsScreen extends StatefulWidget {
  const DebugSettingsScreen({super.key});

  @override
  State<DebugSettingsScreen> createState() => _DebugSettingsScreenState();
}

class _DebugSettingsScreenState extends State<DebugSettingsScreen> {
  final AppLogger _logger = AppLogger();
  
  // Debug settings
  bool _enableDebugConsole = false;
  bool _enableVerboseLogging = false;
  String _apiUrl = AppConstants.baseUrl;
  final TextEditingController _apiUrlController = TextEditingController();
  
  @override
  void initState() {
    super.initState();
    _loadDebugSettings();
  }
  
  @override
  void dispose() {
    _apiUrlController.dispose();
    super.dispose();
  }
  
  Future<void> _loadDebugSettings() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      
      setState(() {
        _enableDebugConsole = prefs.getBool('debug_console_enabled') ?? false;
        _enableVerboseLogging = prefs.getBool('verbose_logging_enabled') ?? false;
        _apiUrl = prefs.getString('api_base_url') ?? AppConstants.baseUrl;
        _apiUrlController.text = _apiUrl;
      });
      
      _logger.d('Debug settings loaded: debug console=$_enableDebugConsole, verbose logging=$_enableVerboseLogging');
    } catch (e) {
      _logger.e('Failed to load debug settings', e);
    }
  }
  
  Future<void> _saveDebugSettings() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      
      await prefs.setBool('debug_console_enabled', _enableDebugConsole);
      await prefs.setBool('verbose_logging_enabled', _enableVerboseLogging);
      await prefs.setString('api_base_url', _apiUrl);
      
      _logger.d('Debug settings saved');
      
      // Show confirmation
      Get.snackbar(
        'Debug Settings',
        'Settings saved successfully',
        backgroundColor: Colors.green,
        colorText: Colors.white,
        snackPosition: SnackPosition.BOTTOM,
      );
    } catch (e) {
      _logger.e('Failed to save debug settings', e);
      
      // Show error
      Get.snackbar(
        'Debug Settings',
        'Failed to save settings: $e',
        backgroundColor: Colors.red,
        colorText: Colors.white,
        snackPosition: SnackPosition.BOTTOM,
      );
    }
  }
  
  Future<void> _resetDatabase() async {
    try {
      _logger.w('Resetting database from debug settings');
      
      // Show confirmation dialog
      final confirmed = await showDialog<bool>(
        context: context,
        builder: (context) => AlertDialog(
          title: const Text('Reset Database'),
          content: const Text(
            'This will delete all local data and reset the database to its initial state. This operation cannot be undone.',
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.of(context).pop(false),
              child: const Text('Cancel'),
            ),
            TextButton(
              onPressed: () => Navigator.of(context).pop(true),
              child: const Text('Reset', style: TextStyle(color: Colors.red)),
            ),
          ],
        ),
      );
      
      if (confirmed == true) {
        final dbHelper = DatabaseHelper();
        await dbHelper.resetDatabase();
        
        // Show success message
        Get.snackbar(
          'Database Reset',
          'Database has been reset successfully',
          backgroundColor: Colors.green,
          colorText: Colors.white,
          snackPosition: SnackPosition.BOTTOM,
        );
      }
    } catch (e) {
      _logger.e('Failed to reset database', e);
      
      // Show error
      Get.snackbar(
        'Database Reset',
        'Failed to reset database: $e',
        backgroundColor: Colors.red,
        colorText: Colors.white,
        snackPosition: SnackPosition.BOTTOM,
      );
    }
  }
  
  Future<void> _logoutAndClearData() async {
    try {
      _logger.w('Logging out and clearing all data');
      
      // Show confirmation dialog
      final confirmed = await showDialog<bool>(
        context: context,
        builder: (context) => AlertDialog(
          title: const Text('Logout and Clear Data'),
          content: const Text(
            'This will log you out and delete all local data. This operation cannot be undone.',
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.of(context).pop(false),
              child: const Text('Cancel'),
            ),
            TextButton(
              onPressed: () => Navigator.of(context).pop(true),
              child: const Text('Logout & Clear', style: TextStyle(color: Colors.red)),
            ),
          ],
        ),
      );
      
      if (confirmed == true) {
        // Clear database
        final dbHelper = DatabaseHelper();
        await dbHelper.resetDatabase();
        
        // Logout user
        final authService = Get.find<AuthService>();
        await authService.logout();
        
        // Navigate to login screen
        Get.offAllNamed(AppRoutes.login);
      }
    } catch (e) {
      _logger.e('Failed to logout and clear data', e);
      
      // Show error
      Get.snackbar(
        'Logout Error',
        'Failed to logout and clear data: $e',
        backgroundColor: Colors.red,
        colorText: Colors.white,
        snackPosition: SnackPosition.BOTTOM,
      );
    }
  }
  
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Debug Settings'),
        backgroundColor: Colors.grey.shade900,
      ),
      body: ListView(
        padding: const EdgeInsets.all(16),
        children: [
          // Warning card
          Card(
            color: Colors.amber.shade100,
            margin: const EdgeInsets.only(bottom: 16),
            child: Padding(
              padding: const EdgeInsets.all(16),
              child: Row(
                children: [
                  Icon(Icons.warning_amber_rounded, color: Colors.amber.shade800),
                  const SizedBox(width: 16),
                  const Expanded(
                    child: Text(
                      'These settings are for debugging purposes only. Changing them may affect app functionality.',
                      style: TextStyle(color: Colors.black87),
                    ),
                  ),
                ],
              ),
            ),
          ),
          
          // Debug console toggle
          SwitchListTile(
            title: const Text('Enable Debug Console'),
            subtitle: const Text('Shows a debug console in the app for testing'),
            value: _enableDebugConsole,
            onChanged: (value) {
              setState(() {
                _enableDebugConsole = value;
              });
            },
          ),
          
          // Verbose logging toggle
          SwitchListTile(
            title: const Text('Enable Verbose Logging'),
            subtitle: const Text('Logs more detailed information'),
            value: _enableVerboseLogging,
            onChanged: (value) {
              setState(() {
                _enableVerboseLogging = value;
              });
            },
          ),
          
          const Divider(),
          
          // API URL field
          const Padding(
            padding: EdgeInsets.only(left: 16, top: 16, bottom: 8),
            child: Text(
              'API Base URL',
              style: TextStyle(
                fontWeight: FontWeight.bold,
                fontSize: 16,
              ),
            ),
          ),
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16),
            child: TextField(
              controller: _apiUrlController,
              decoration: const InputDecoration(
                hintText: 'Enter API base URL',
                border: OutlineInputBorder(),
              ),
              onChanged: (value) {
                _apiUrl = value;
              },
            ),
          ),
          TextButton(
            onPressed: () {
              setState(() {
                _apiUrl = AppConstants.baseUrl;
                _apiUrlController.text = _apiUrl;
              });
            },
            child: const Text('Reset to Default'),
          ),
          
          const Divider(),
          
          // Network information
          const Padding(
            padding: EdgeInsets.only(left: 16, top: 16, bottom: 8),
            child: Text(
              'Network Status',
              style: TextStyle(
                fontWeight: FontWeight.bold,
                fontSize: 16,
              ),
            ),
          ),
          ListTile(
            title: const Text('Connection Status'),
            subtitle: GetX<ConnectivityService>(
              builder: (service) {
                return Text(
                  service.isConnected.value ? 'Connected' : 'Disconnected',
                  style: TextStyle(
                    color: service.isConnected.value ? Colors.green : Colors.red,
                    fontWeight: FontWeight.bold,
                  ),
                );
              },
            ),
            trailing: IconButton(
              icon: const Icon(Icons.refresh),
              onPressed: () {
                final connectivityService = Get.find<ConnectivityService>();
                
                // Force refresh the connection status display
                connectivityService.isConnected.refresh();
                
                Get.snackbar(
                  'Connection Status',
                  'Status refreshed',
                  backgroundColor: Colors.blue,
                  colorText: Colors.white,
                  snackPosition: SnackPosition.BOTTOM,
                );
              },
            ),
          ),
          
          const Divider(),
          
          // Database operations
          const Padding(
            padding: EdgeInsets.only(left: 16, top: 16, bottom: 8),
            child: Text(
              'Database Operations',
              style: TextStyle(
                fontWeight: FontWeight.bold,
                fontSize: 16,
              ),
            ),
          ),
          ListTile(
            title: const Text('Reset Database'),
            subtitle: const Text('Deletes and recreates the local database'),
            trailing: const Icon(Icons.delete_forever),
            onTap: _resetDatabase,
          ),
          ListTile(
            title: const Text('Show Database Path'),
            subtitle: const Text('Displays the location of the database file'),
            trailing: const Icon(Icons.folder),
            onTap: () async {
              try {
                final dbHelper = DatabaseHelper();
                final path = await dbHelper.getDatabasePath();
                
                if (mounted) {
                  showDialog(
                    context: context,
                    builder: (context) => AlertDialog(
                      title: const Text('Database Path'),
                      content: SingleChildScrollView(
                        child: Text(path),
                      ),
                      actions: [
                        TextButton(
                          onPressed: () => Navigator.of(context).pop(),
                          child: const Text('Close'),
                        ),
                      ],
                    ),
                  );
                }
              } catch (e) {
                _logger.e('Failed to get database path', e);
                
                Get.snackbar(
                  'Database Error',
                  'Failed to get database path: $e',
                  backgroundColor: Colors.red,
                  colorText: Colors.white,
                  snackPosition: SnackPosition.BOTTOM,
                );
              }
            },
          ),
          
          const Divider(),
          
          // Authentication operations
          const Padding(
            padding: EdgeInsets.only(left: 16, top: 16, bottom: 8),
            child: Text(
              'Authentication Operations',
              style: TextStyle(
                fontWeight: FontWeight.bold,
                fontSize: 16,
              ),
            ),
          ),
          ListTile(
            title: const Text('Logout and Clear All Data'),
            subtitle: const Text('Logs out and resets the app to initial state'),
            trailing: const Icon(Icons.logout),
            onTap: _logoutAndClearData,
          ),
          
          const SizedBox(height: 32),
          
          // Save button
          ElevatedButton(
            onPressed: _saveDebugSettings,
            style: ElevatedButton.styleFrom(
              backgroundColor: Colors.blue,
              padding: const EdgeInsets.symmetric(vertical: 12),
            ),
            child: const Text('Save Debug Settings'),
          ),
        ],
      ),
    );
  }
}