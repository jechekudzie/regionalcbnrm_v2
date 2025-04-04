import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:regional_cbnrm/models/user_model.dart';
import 'package:regional_cbnrm/services/auth_service.dart';
import 'package:regional_cbnrm/utils/app_constants.dart';
import 'package:regional_cbnrm/utils/app_routes.dart';

class AppDrawer extends StatelessWidget {
  final Organisation? selectedOrganisation;
  
  const AppDrawer({
    Key? key,
    required this.selectedOrganisation,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    final authService = Get.find<AuthService>();
    final user = authService.currentUser;
    
    return Drawer(
      child: Column(
        children: [
          // Custom drawer header with user info and organization
          Container(
            color: Theme.of(context).primaryColor,
            padding: const EdgeInsets.only(bottom: 16),
            height: 150,
            width: double.infinity,
            child: SafeArea(
              bottom: false,
              child: Stack(
                children: [
                  // User and organization info
                  Padding(
                    padding: const EdgeInsets.only(left: 16, top: 16, right: 56), // Add padding for content
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        // User name
                        Text(
                          user?.name ?? 'User',
                          style: const TextStyle(
                            fontSize: 18,
                            fontWeight: FontWeight.bold,
                            color: Colors.white,
                          ),
                          overflow: TextOverflow.ellipsis,
                        ),
                        const SizedBox(height: 4),
                        
                        // User email
                        Text(
                          user?.email ?? 'Not logged in',
                          style: const TextStyle(
                            fontSize: 14,
                            color: Colors.white,
                          ),
                          overflow: TextOverflow.ellipsis,
                        ),
                        const SizedBox(height: 8),
                        
                        // Organization name
                        if (selectedOrganisation != null)
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                            decoration: BoxDecoration(
                              color: Colors.white.withOpacity(0.15),
                              borderRadius: BorderRadius.circular(4),
                            ),
                            child: Text(
                              selectedOrganisation!.name,
                              style: const TextStyle(
                                fontSize: 12,
                                fontWeight: FontWeight.bold,
                                color: Colors.white,
                              ),
                              overflow: TextOverflow.ellipsis,
                            ),
                          ),
                      ],
                    ),
                  ),
                  
                  // Avatar positioned at top right
                  Positioned(
                    top: 16,
                    right: 16,
                    child: CircleAvatar(
                      radius: 24,
                      backgroundColor: Colors.white,
                      child: Text(
                        user?.name.isNotEmpty == true 
                            ? user!.name.substring(0, 1).toUpperCase() 
                            : 'U',
                        style: const TextStyle(
                          fontSize: 24,
                          fontWeight: FontWeight.bold,
                          color: Colors.blueGrey,
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),
          
          // Drawer body with menu items
          Expanded(
            child: ListView(
              padding: EdgeInsets.zero,
              children: [
                // Dashboard
                _buildMenuItem(
                  context,
                  title: 'Dashboard',
                  icon: Icons.dashboard,
                  onTap: () => Get.offAllNamed(AppRoutes.dashboard),
                ),
                
                // Divider for wildlife management section
                const Divider(),
                _buildSectionHeader(context, 'Wildlife Management'),
                
                // Wildlife Conflict module with sub-items
                _buildExpandableMenuItem(
                  context,
                  title: 'Wildlife Conflicts',
                  icon: Icons.warning_amber_rounded,
                  initiallyExpanded: false,
                  children: [
                    _buildSubMenuItem(
                      context,
                      title: 'All Conflicts',
                      route: AppRoutes.wildlifeConflicts,
                    ),
                    _buildSubMenuItem(
                      context,
                      title: 'Report New Conflict',
                      route: AppRoutes.createWildlifeConflict,
                      hasPermission: _hasPermission(authService, AppConstants.createWildlifeConflict),
                    ),
                  ],
                  hasPermission: _hasPermission(authService, AppConstants.viewWildlifeConflicts),
                ),
                
                // Problem Animal Control module with sub-items
                _buildExpandableMenuItem(
                  context,
                  title: 'Problem Animal Control',
                  icon: Icons.pets_rounded,
                  initiallyExpanded: false,
                  children: [
                    _buildSubMenuItem(
                      context,
                      title: 'All PAC Records',
                      route: AppRoutes.problemAnimalControls,
                    ),
                    _buildSubMenuItem(
                      context,
                      title: 'Add PAC Record',
                      route: AppRoutes.createProblemAnimalControl,
                      hasPermission: _hasPermission(authService, AppConstants.createProblemAnimalControl),
                    ),
                  ],
                  hasPermission: _hasPermission(authService, AppConstants.viewProblemAnimalControls),
                ),
                
                // Divider for anti-poaching section
                const Divider(),
                _buildSectionHeader(context, 'Anti-Poaching'),
                
                // Poaching Incidents module with sub-items
                _buildExpandableMenuItem(
                  context,
                  title: 'Poaching Incidents',
                  icon: Icons.gavel_rounded,
                  initiallyExpanded: false,
                  children: [
                    _buildSubMenuItem(
                      context,
                      title: 'All Incidents',
                      route: AppRoutes.poachingIncidents,
                    ),
                    _buildSubMenuItem(
                      context,
                      title: 'Report New Incident',
                      route: AppRoutes.createPoachingIncident,
                      hasPermission: _hasPermission(authService, AppConstants.createPoachingIncident),
                    ),
                  ],
                  hasPermission: _hasPermission(authService, AppConstants.viewPoachingIncidents),
                ),
                
                // Divider for hunting section
                const Divider(),
                _buildSectionHeader(context, 'Hunting Management'),
                
                // Hunting Activities module with sub-items
                _buildExpandableMenuItem(
                  context,
                  title: 'Hunting Activities',
                  icon: Icons.track_changes_rounded,
                  initiallyExpanded: false,
                  children: [
                    _buildSubMenuItem(
                      context,
                      title: 'All Activities',
                      route: AppRoutes.huntingActivities,
                    ),
                    _buildSubMenuItem(
                      context,
                      title: 'Record New Activity',
                      route: AppRoutes.createHuntingActivity,
                      hasPermission: _hasPermission(authService, AppConstants.createHuntingActivity),
                    ),
                  ],
                  hasPermission: _hasPermission(authService, AppConstants.viewHuntingActivities),
                ),
                
                // Hunting Concessions module with sub-items
                _buildExpandableMenuItem(
                  context,
                  title: 'Hunting Concessions',
                  icon: Icons.map_rounded,
                  initiallyExpanded: false,
                  children: [
                    _buildSubMenuItem(
                      context,
                      title: 'All Concessions',
                      route: AppRoutes.huntingConcessions,
                    ),
                    _buildSubMenuItem(
                      context,
                      title: 'Add New Concession',
                      route: AppRoutes.createHuntingConcession,
                      hasPermission: _hasPermission(authService, AppConstants.createHuntingConcession),
                    ),
                  ],
                  hasPermission: _hasPermission(authService, AppConstants.viewHuntingConcessions),
                ),
                
                // Quota Allocations module with sub-items
                _buildExpandableMenuItem(
                  context,
                  title: 'Quota Allocations',
                  icon: Icons.format_list_numbered_rounded,
                  initiallyExpanded: false,
                  children: [
                    _buildSubMenuItem(
                      context,
                      title: 'All Quota Allocations',
                      route: AppRoutes.quotaAllocations,
                    ),
                    _buildSubMenuItem(
                      context,
                      title: 'Add New Allocation',
                      route: AppRoutes.createQuotaAllocation,
                      hasPermission: _hasPermission(authService, AppConstants.createQuotaAllocation),
                    ),
                  ],
                  hasPermission: _hasPermission(authService, AppConstants.viewQuotaAllocations),
                ),
                
                // Divider for settings section
                const Divider(),
                _buildSectionHeader(context, 'Settings'),
                
                // User Profile
                _buildMenuItem(
                  context,
                  title: 'My Profile',
                  icon: Icons.person,
                  route: AppRoutes.profile,
                ),
                
                // Sync Data
                _buildMenuItem(
                  context,
                  title: 'Sync Data',
                  icon: Icons.sync,
                  route: AppRoutes.syncData,
                ),
                
                // Logout
                _buildMenuItem(
                  context,
                  title: 'Logout',
                  icon: Icons.logout,
                  onTap: () => _showLogoutDialog(context),
                  textColor: Colors.red,
                  iconColor: Colors.red,
                ),
              ],
            ),
          ),
          
          // Drawer footer with version info
          Container(
            padding: const EdgeInsets.symmetric(vertical: 12, horizontal: 16),
            color: Colors.grey.shade100,
            child: Row(
              children: [
                Text(
                  'v${AppConstants.appVersion}',
                  style: TextStyle(
                    color: Colors.grey.shade600,
                    fontSize: 12,
                  ),
                ),
                const Spacer(),
                Text(
                  AppConstants.appName,
                  style: TextStyle(
                    color: Colors.grey.shade600,
                    fontSize: 12,
                    fontWeight: FontWeight.bold,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
  
  // Build a section header
  Widget _buildSectionHeader(BuildContext context, String title) {
    return Padding(
      padding: const EdgeInsets.fromLTRB(16, 8, 16, 4),
      child: Text(
        title,
        style: TextStyle(
          color: Theme.of(context).primaryColor,
          fontSize: 12,
          fontWeight: FontWeight.bold,
          letterSpacing: 0.5,
        ),
      ),
    );
  }
  
  // Build a regular menu item
  Widget _buildMenuItem(
    BuildContext context, {
    required String title,
    required IconData icon,
    String? route,
    VoidCallback? onTap,
    Color? textColor,
    Color? iconColor,
    bool hasPermission = true,
  }) {
    if (!hasPermission) return const SizedBox.shrink();
    
    return ListTile(
      title: Text(
        title,
        style: TextStyle(
          color: textColor ?? Colors.black87,
          fontWeight: FontWeight.w500,
        ),
      ),
      leading: Icon(
        icon,
        color: iconColor ?? Theme.of(context).primaryColor,
      ),
      onTap: onTap ?? (route != null ? () => Get.toNamed(route) : null),
      dense: true,
    );
  }
  
  // Build a sub-menu item
  Widget _buildSubMenuItem(
    BuildContext context, {
    required String title,
    required String route,
    bool hasPermission = true,
  }) {
    if (!hasPermission) return const SizedBox.shrink();
    
    return ListTile(
      title: Text(
        title,
        style: const TextStyle(
          fontSize: 14,
        ),
      ),
      leading: const SizedBox(width: 16),
      contentPadding: const EdgeInsets.only(left: 32),
      dense: true,
      onTap: () => Get.toNamed(route),
    );
  }
  
  // Build an expandable menu item with children
  Widget _buildExpandableMenuItem(
    BuildContext context, {
    required String title,
    required IconData icon,
    required List<Widget> children,
    bool initiallyExpanded = false,
    bool hasPermission = true,
  }) {
    if (!hasPermission) return const SizedBox.shrink();
    
    if (children.isEmpty) {
      return _buildMenuItem(
        context,
        title: title,
        icon: icon,
      );
    }
    
    return Theme(
      data: Theme.of(context).copyWith(dividerColor: Colors.transparent),
      child: ExpansionTile(
        title: Text(
          title,
          style: const TextStyle(
            fontWeight: FontWeight.w500,
            color: Colors.black87,
          ),
        ),
        leading: Icon(
          icon,
          color: Theme.of(context).primaryColor,
        ),
        initiallyExpanded: initiallyExpanded,
        childrenPadding: EdgeInsets.zero,
        iconColor: Theme.of(context).primaryColor,
        collapsedIconColor: Colors.grey,
        children: children,
      ),
    );
  }
  
  // Show logout confirmation dialog
  void _showLogoutDialog(BuildContext context) {
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
              Get.find<AuthService>().logout();
            },
          ),
        ],
      ),
    );
  }
  
  // Check if user has a specific permission
  bool _hasPermission(AuthService authService, String permission) {
    // If no organization is selected, hide restricted items
    if (selectedOrganisation == null) return false;
    
    // For development/testing: grant all permissions
    return true;
    
    // The code below would be used in production to properly check permissions
    /*
    // Basic view permissions are always available to authenticated users with a selected organization
    if (permission.startsWith('view-')) {
      // Check if user has role-based view permissions for sensitive modules
      if (permission == 'view-poaching-incidents' || 
          permission == 'view-hunting-activities' || 
          permission == 'view-quota-allocations') {
        // These modules require actual permission checks
        return authService.hasPermission(permission);
      }
      // Other view permissions are granted by default
      return true;
    }
    
    // Create/edit permissions always require explicit permission checks
    return authService.hasPermission(permission);
    */
  }
}