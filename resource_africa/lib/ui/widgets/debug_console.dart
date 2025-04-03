import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:resource_africa/core/database_helper.dart';
import 'package:resource_africa/services/auth_service.dart';
import 'package:resource_africa/services/connectivity_service.dart';
import 'package:resource_africa/utils/logger.dart';

// A debug console widget that shows useful debugging information
// This should only be used during development
class DebugConsole extends StatelessWidget {
  final bool expanded;
  final VoidCallback onToggle;
  final AppLogger _logger = AppLogger();

  DebugConsole({
    super.key,
    required this.expanded,
    required this.onToggle,
  });

  @override
  Widget build(BuildContext context) {
    if (!expanded) {
      return Positioned(
        right: 16,
        bottom: 80,
        child: FloatingActionButton(
          backgroundColor: Colors.black.withOpacity(0.7),
          mini: true,
          heroTag: 'debugConsole',
          onPressed: onToggle,
          child: const Icon(
            Icons.developer_mode,
            color: Colors.white,
          ),
        ),
      );
    }

    return Positioned(
      right: 0,
      bottom: 0,
      left: 0,
      child: Container(
        height: 300,
        color: Colors.black.withOpacity(0.9),
        child: Column(
          children: [
            _buildHeader(),
            Expanded(
              child: _buildConsoleContent(),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildHeader() {
    return Container(
      color: Colors.grey.shade900,
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          const Text(
            'Debug Console',
            style: TextStyle(
              color: Colors.white,
              fontWeight: FontWeight.bold,
            ),
          ),
          Row(
            children: [
              _buildDebugButton(
                label: 'Reset DB',
                icon: Icons.delete_forever,
                onPressed: _resetDatabase,
                color: Colors.red,
              ),
              const SizedBox(width: 8),
              _buildDebugButton(
                label: 'Logout',
                icon: Icons.logout,
                onPressed: _forceLogout,
                color: Colors.orange,
              ),
              const SizedBox(width: 8),
              IconButton(
                icon: const Icon(Icons.close, color: Colors.white),
                onPressed: onToggle,
                tooltip: 'Close',
              ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildConsoleContent() {
    final authService = Get.find<AuthService>();
    final connectivityService = Get.find<ConnectivityService>();
    final dbHelper = DatabaseHelper();

    return DefaultTabController(
      length: 3,
      child: Column(
        children: [
          TabBar(
            tabs: const [
              Tab(text: 'Auth'),
              Tab(text: 'Network'),
              Tab(text: 'Database'),
            ],
            labelColor: Colors.white,
            unselectedLabelColor: Colors.grey,
            indicatorColor: Colors.green,
          ),
          Expanded(
            child: TabBarView(
              children: [
                // Auth Tab
                SingleChildScrollView(
                  padding: const EdgeInsets.all(16),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      _buildInfoItem('Is Logged In:', '${authService.isLoggedIn}'),
                      _buildInfoItem('Current User:', '${authService.currentUser?.name ?? "None"}'),
                      _buildInfoItem('User Roles:', '${authService.currentUser?.roles.map((r) => r.name).join(", ") ?? "None"}'),
                      FutureBuilder<bool>(
                        future: authService.isAuthenticated(),
                        builder: (context, snapshot) {
                          return _buildInfoItem(
                            'Token Valid:',
                            snapshot.hasData ? '${snapshot.data}' : 'Loading...',
                          );
                        },
                      ),
                    ],
                  ),
                ),
                
                // Network Tab
                SingleChildScrollView(
                  padding: const EdgeInsets.all(16),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Obx(() => _buildInfoItem(
                        'Connected:',
                        '${connectivityService.isConnected.value}',
                        connectivityService.isConnected.value ? Colors.green : Colors.red,
                      )),
                      _buildDebugButton(
                        label: 'Test API',
                        icon: Icons.cloud,
                        onPressed: _testApiConnection,
                        color: Colors.blue,
                      ),
                    ],
                  ),
                ),
                
                // Database Tab
                SingleChildScrollView(
                  padding: const EdgeInsets.all(16),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      FutureBuilder<bool>(
                        future: dbHelper.databaseExists(),
                        builder: (context, snapshot) {
                          return _buildInfoItem(
                            'DB Exists:',
                            snapshot.hasData ? '${snapshot.data}' : 'Checking...',
                            snapshot.hasData && snapshot.data == true ? Colors.green : Colors.red,
                          );
                        },
                      ),
                      FutureBuilder<String>(
                        future: dbHelper.getDatabasePath(),
                        builder: (context, snapshot) {
                          return _buildInfoItem(
                            'DB Path:',
                            snapshot.hasData ? snapshot.data! : 'Loading...',
                          );
                        },
                      ),
                      const SizedBox(height: 16),
                      _buildDebugButton(
                        label: 'Check Tables',
                        icon: Icons.table_chart,
                        onPressed: _checkDatabaseTables,
                        color: Colors.purple,
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildInfoItem(String label, String value, [Color? valueColor]) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 8),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          SizedBox(
            width: 120,
            child: Text(
              label,
              style: const TextStyle(
                color: Colors.grey,
                fontWeight: FontWeight.bold,
              ),
            ),
          ),
          Expanded(
            child: Text(
              value,
              style: TextStyle(
                color: valueColor ?? Colors.white,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildDebugButton({
    required String label,
    required IconData icon,
    required VoidCallback onPressed,
    required Color color,
  }) {
    return ElevatedButton.icon(
      onPressed: onPressed,
      icon: Icon(icon, size: 16),
      label: Text(label),
      style: ElevatedButton.styleFrom(
        backgroundColor: color,
        foregroundColor: Colors.white,
        textStyle: const TextStyle(fontSize: 12),
        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      ),
    );
  }

  // Debug Actions
  void _resetDatabase() async {
    try {
      _logger.w('Debug console: Resetting database');
      
      final dbHelper = DatabaseHelper();
      await dbHelper.resetDatabase();
      
      Get.snackbar(
        'Debug',
        'Database reset successfully',
        backgroundColor: Colors.green,
        colorText: Colors.white,
        snackPosition: SnackPosition.BOTTOM,
      );
    } catch (e) {
      _logger.e('Debug console: Error resetting database', e);
      
      Get.snackbar(
        'Debug Error',
        'Failed to reset database: $e',
        backgroundColor: Colors.red,
        colorText: Colors.white,
        snackPosition: SnackPosition.BOTTOM,
      );
    }
  }

  void _forceLogout() async {
    try {
      _logger.w('Debug console: Force logout');
      
      final authService = Get.find<AuthService>();
      await authService.logout();
      
      Get.snackbar(
        'Debug',
        'Forced logout successful',
        backgroundColor: Colors.green,
        colorText: Colors.white,
        snackPosition: SnackPosition.BOTTOM,
      );
    } catch (e) {
      _logger.e('Debug console: Error during forced logout', e);
      
      Get.snackbar(
        'Debug Error',
        'Failed to force logout: $e',
        backgroundColor: Colors.red,
        colorText: Colors.white,
        snackPosition: SnackPosition.BOTTOM,
      );
    }
  }

  void _testApiConnection() async {
    try {
      _logger.d('Debug console: Testing API connection');
      
      Get.snackbar(
        'Debug',
        'API connection test not implemented yet',
        backgroundColor: Colors.blue,
        colorText: Colors.white,
        snackPosition: SnackPosition.BOTTOM,
      );
      
      // TODO: Implement API connection test
    } catch (e) {
      _logger.e('Debug console: Error testing API connection', e);
      
      Get.snackbar(
        'Debug Error',
        'API connection test failed: $e',
        backgroundColor: Colors.red,
        colorText: Colors.white,
        snackPosition: SnackPosition.BOTTOM,
      );
    }
  }

  void _checkDatabaseTables() async {
    try {
      _logger.d('Debug console: Checking database tables');
      
      final dbHelper = DatabaseHelper();
      final db = await dbHelper.database;
      
      // Get list of all tables
      final tables = await db.rawQuery(
        "SELECT name FROM sqlite_master WHERE type='table' ORDER BY name;"
      );
      
      final tableNames = tables.map((t) => t['name'].toString()).toList();
      
      Get.snackbar(
        'Database Tables',
        'Found ${tableNames.length} tables',
        backgroundColor: Colors.purple,
        colorText: Colors.white,
        snackPosition: SnackPosition.BOTTOM,
        duration: const Duration(seconds: 5),
      );
      
      // Show dialog with table names
      Get.dialog(
        AlertDialog(
          title: const Text('Database Tables'),
          content: SizedBox(
            width: double.maxFinite,
            height: 300,
            child: ListView.builder(
              itemCount: tableNames.length,
              itemBuilder: (context, index) {
                return ListTile(
                  title: Text(tableNames[index]),
                  onTap: () => _showTableDetails(tableNames[index]),
                );
              },
            ),
          ),
          actions: [
            TextButton(
              onPressed: () => Get.back(),
              child: const Text('Close'),
            ),
          ],
        ),
      );
    } catch (e) {
      _logger.e('Debug console: Error checking database tables', e);
      
      Get.snackbar(
        'Debug Error',
        'Failed to check database tables: $e',
        backgroundColor: Colors.red,
        colorText: Colors.white,
        snackPosition: SnackPosition.BOTTOM,
      );
    }
  }
  
  void _showTableDetails(String tableName) async {
    try {
      _logger.d('Debug console: Showing details for table $tableName');
      
      final dbHelper = DatabaseHelper();
      final db = await dbHelper.database;
      
      // Get table structure
      final structure = await db.rawQuery("PRAGMA table_info($tableName);");
      
      // Get row count
      final countResult = await db.rawQuery("SELECT COUNT(*) as count FROM $tableName");
      final rowCount = countResult.first['count'];
      
      // Show dialog with table details
      Get.dialog(
        AlertDialog(
          title: Text('Table: $tableName'),
          content: SizedBox(
            width: double.maxFinite,
            height: 400,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('Row count: $rowCount', style: const TextStyle(fontWeight: FontWeight.bold)),
                const SizedBox(height: 16),
                const Text('Columns:', style: TextStyle(fontWeight: FontWeight.bold)),
                Expanded(
                  child: ListView.builder(
                    itemCount: structure.length,
                    itemBuilder: (context, index) {
                      final column = structure[index];
                      return ListTile(
                        dense: true,
                        title: Text(column['name'].toString()),
                        subtitle: Text('Type: ${column['type']} | Primary Key: ${column['pk'] == 1 ? 'Yes' : 'No'}'),
                      );
                    },
                  ),
                ),
              ],
            ),
          ),
          actions: [
            TextButton(
              onPressed: () => Get.back(),
              child: const Text('Close'),
            ),
          ],
        ),
      );
    } catch (e) {
      _logger.e('Debug console: Error showing table details for $tableName', e);
      
      Get.snackbar(
        'Debug Error',
        'Failed to show table details: $e',
        backgroundColor: Colors.red,
        colorText: Colors.white,
        snackPosition: SnackPosition.BOTTOM,
      );
    }
  }
}