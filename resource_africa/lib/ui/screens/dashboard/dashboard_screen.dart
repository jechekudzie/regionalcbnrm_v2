import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:resource_africa/models/user_model.dart';
import 'package:resource_africa/repositories/organisation_repository.dart';
import 'package:resource_africa/services/auth_service.dart';
import 'package:resource_africa/services/connectivity_service.dart';
import 'package:resource_africa/services/sync_service.dart';
import 'package:resource_africa/ui/theme/app_theme.dart';
import 'package:resource_africa/ui/widgets/app_drawer.dart';
import 'package:resource_africa/ui/widgets/dashboard/dashboard_card.dart';
import 'package:resource_africa/ui/widgets/debug_console.dart';
import 'package:resource_africa/ui/widgets/organisation_selector.dart';
import 'package:resource_africa/ui/widgets/sync_indicator.dart';
import 'package:resource_africa/utils/app_constants.dart';
import 'package:resource_africa/utils/app_routes.dart';
import 'package:resource_africa/utils/logger.dart';
import 'package:shared_preferences/shared_preferences.dart';

class DashboardScreen extends StatefulWidget {
  const DashboardScreen({super.key});

  @override
  State<DashboardScreen> createState() => _DashboardScreenState();
}

class _DashboardScreenState extends State<DashboardScreen> {
  final AuthService _authService = Get.find<AuthService>();
  final ConnectivityService _connectivityService = Get.find<ConnectivityService>();
  final SyncService _syncService = Get.put(SyncService());
  final OrganisationRepository _organisationRepository = OrganisationRepository();
  final AppLogger _logger = AppLogger();

  final Rx<Organisation?> _selectedOrganisation = Rx<Organisation?>(null);

  // Debug console state
  bool _showDebugConsole = false;
  bool _debugModeEnabled = false;

  @override
  void initState() {
    super.initState();
    _loadSelectedOrganisation();
    _checkDebugSettings();
  }

  Future<void> _loadSelectedOrganisation() async {
    try {
      _logger.d('Loading selected organisation');
      _selectedOrganisation.value = await _organisationRepository.getSelectedOrganisation();
      _logger.d('Selected organisation: ${_selectedOrganisation.value?.name}');
    } catch (e) {
      _logger.e('Error loading selected organisation', e);
    }
  }

  Future<void> _checkDebugSettings() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final enabled = prefs.getBool('debug_console_enabled') ?? false;

      setState(() {
        _debugModeEnabled = enabled;
      });

      _logger.d('Debug mode enabled: $_debugModeEnabled');
    } catch (e) {
      _logger.e('Error checking debug settings', e);
    }
  }

  void _toggleDebugConsole() {
    setState(() {
      _showDebugConsole = !_showDebugConsole;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Dashboard'),
        actions: [
          Obx(() => SyncIndicator(
                isSyncing: _syncService.isSyncing.value,
                pendingCount: _syncService.pendingSyncCount.value,
                onSync: () => _syncService.syncData(),
              )),
          PopupMenuButton<String>(
            onSelected: (value) {
              if (value == 'logout') {
                _handleLogout();
              } else if (value == 'debug') {
                Get.toNamed(AppRoutes.debugSettings);
              }
            },
            itemBuilder: (context) => [
              if (_debugModeEnabled)
                const PopupMenuItem(
                  value: 'debug',
                  child: Row(
                    children: [
                      Icon(Icons.developer_mode),
                      SizedBox(width: 8),
                      Text('Debug Settings'),
                    ],
                  ),
                ),
              const PopupMenuItem(
                value: 'logout',
                child: Row(
                  children: [
                    Icon(Icons.logout, color: Colors.red),
                    SizedBox(width: 8),
                    Text('Logout', style: TextStyle(color: Colors.red)),
                  ],
                ),
              ),
            ],
          ),
        ],
      ),
      drawer: Obx(() => AppDrawer(selectedOrganisation: _selectedOrganisation.value)),
      body: Stack(
        children: [
          Column(
            children: [
              // Offline indicator
              Obx(() => _connectivityService.isConnected.value
                  ? const SizedBox.shrink()
                  : Container(
                      width: double.infinity,
                      color: Colors.red,
                      padding: const EdgeInsets.symmetric(vertical: 4, horizontal: 16),
                      child: const Row(
                        children: [
                          Icon(Icons.wifi_off, color: Colors.white, size: 16),
                          SizedBox(width: 8),
                          Text(
                            'You are offline. Data will be synced when connection is restored.',
                            style: TextStyle(color: Colors.white, fontSize: 12),
                          ),
                        ],
                      ),
                    )),

              // Organisation selector
              Obx(() => OrganisationSelector(
                    selectedOrganisation: _selectedOrganisation.value,
                    onOrganisationChanged: (organisation) {
                      _selectedOrganisation.value = organisation;
                      _organisationRepository.setSelectedOrganisation(organisation);
                    },
                  )),

              // Main content
              Expanded(
                child: Obx(() => _selectedOrganisation.value == null
                    ? const Center(
                        child: Text('Please select an organisation to continue'),
                      )
                    : _buildDashboardContent()),
              ),
            ],
          ),

          // Debug console
          if (_debugModeEnabled)
            DebugConsole(
              expanded: _showDebugConsole,
              onToggle: _toggleDebugConsole,
            ),
        ],
      ),
    );
  }

  Widget _buildDashboardContent() {
    return RefreshIndicator(
      onRefresh: () async {
        // Refresh data
        await _syncService.syncData();
      },
      child: SingleChildScrollView(
        physics: const AlwaysScrollableScrollPhysics(),
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              'Welcome, ${_authService.currentUser?.name ?? 'User'}',
              style: AppTheme.headingStyle,
            ),
            const SizedBox(height: 8),
            const Text(
              'What would you like to manage today?',
              style: AppTheme.captionStyle,
            ),
            const SizedBox(height: 24),

            // Dashboard grid
            GridView.count(
              crossAxisCount: 2,
              shrinkWrap: true,
              physics: const NeverScrollableScrollPhysics(),
              crossAxisSpacing: 16,
              mainAxisSpacing: 16,
              children: _buildDashboardCards(),
            ),

            const SizedBox(height: 24),

            // Sync section
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(12),
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withOpacity(0.05),
                    blurRadius: 10,
                    offset: const Offset(0, 2),
                  ),
                ],
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  const Text(
                    'Data Synchronization',
                    style: AppTheme.subheadingStyle,
                  ),
                  const SizedBox(height: 8),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Obx(() => Text(
                            'Pending items: ${_syncService.pendingSyncCount.value}',
                            style: AppTheme.captionStyle,
                          )),
                      ElevatedButton(
                        onPressed: () => _syncService.syncData(),
                        child: Obx(() => _syncService.isSyncing.value
                            ? const SizedBox(
                                width: 16,
                                height: 16,
                                child: CircularProgressIndicator(
                                  strokeWidth: 2,
                                  valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                                ),
                              )
                            : const Text('Sync Now')),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  // Build dashboard cards based on permissions
  List<Widget> _buildDashboardCards() {
    final List<Widget> cards = [];

    // Wildlife conflict incidents (accessible to all users)
    if (_authService.hasPermission(AppConstants.viewWildlifeConflicts)) {
      cards.add(
        DashboardCard(
          title: 'Wildlife Conflicts',
          icon: Icons.warning_amber_rounded,
          color: Colors.orange,
          onTap: () => Get.toNamed(AppRoutes.wildlifeConflicts),
        ),
      );
    }

    // Problem animal control (accessible to all users)
    if (_authService.hasPermission(AppConstants.viewProblemAnimalControls)) {
      cards.add(
        DashboardCard(
          title: 'Problem Animal Control',
          icon: Icons.pets_rounded,
          color: Colors.red,
          onTap: () => Get.toNamed(AppRoutes.problemAnimalControls),
        ),
      );
    }

    // Poaching incidents (restricted to certain roles)
    if (_authService.hasPermission(AppConstants.viewPoachingIncidents)) {
      cards.add(
        DashboardCard(
          title: 'Poaching Incidents',
          icon: Icons.gavel_rounded,
          color: Colors.purple,
          onTap: () => Get.toNamed(AppRoutes.poachingIncidents),
        ),
      );
    }

    // Hunting activities (restricted to certain roles)
    if (_authService.hasPermission(AppConstants.viewHuntingActivities)) {
      cards.add(
        DashboardCard(
          title: 'Hunting Activities',
          icon: Icons.track_changes_rounded,
          color: Colors.blue,
          onTap: () => Get.toNamed(AppRoutes.huntingActivities),
        ),
      );
    }

    // Hunting concessions (restricted to certain roles)
    if (_authService.hasPermission(AppConstants.viewHuntingConcessions)) {
      cards.add(
        DashboardCard(
          title: 'Hunting Concessions',
          icon: Icons.map_rounded,
          color: Colors.green,
          onTap: () => Get.toNamed(AppRoutes.huntingConcessions),
        ),
      );
    }

    // Quota allocations (restricted to certain roles)
    if (_authService.hasPermission(AppConstants.viewQuotaAllocations)) {
      cards.add(
        DashboardCard(
          title: 'Quota Allocations',
          icon: Icons.format_list_numbered_rounded,
          color: Colors.teal,
          onTap: () => Get.toNamed(AppRoutes.quotaAllocations),
        ),
      );
    }

    // If no cards are available (unlikely), show a message
    if (cards.isEmpty) {
      return [
        const SizedBox(
          height: 200,
          child: Center(
            child: Text(
              'No modules available. Please contact administrator.',
              style: AppTheme.captionStyle,
              textAlign: TextAlign.center,
            ),
          ),
        ),
      ];
    }

    return cards;
  }

  void _handleLogout() {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Logout'),
        content: const Text('Are you sure you want to logout?'),
        actions: [
          TextButton(
            child: const Text('Cancel'),
            onPressed: () => Navigator.of(context).pop(),
          ),
          TextButton(
            style: TextButton.styleFrom(
              foregroundColor: Colors.red,
            ),
            child: const Text('Logout'),
            onPressed: () {
              Navigator.of(context).pop();
              _authService.logout();
            },
          ),
        ],
      ),
    );
  }
}
