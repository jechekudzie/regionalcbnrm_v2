import 'package:flutter/material.dart';
import 'package:get/get.dart';
// Ensure Material is imported
// Added import
import 'package:regional_cbnrm/models/wildlife_conflict_model.dart';
import 'package:regional_cbnrm/repositories/organisation_repository.dart';
import 'package:regional_cbnrm/repositories/wildlife_conflict_repository.dart';
import 'package:regional_cbnrm/ui/widgets/empty_state.dart';
import 'package:regional_cbnrm/utils/app_routes.dart';
import 'package:intl/intl.dart';
import 'package:regional_cbnrm/utils/logger.dart';

class WildlifeConflictListScreen extends StatefulWidget {
  const WildlifeConflictListScreen({super.key});

  @override
  State<WildlifeConflictListScreen> createState() => _WildlifeConflictListScreenState();
}

class _WildlifeConflictListScreenState extends State<WildlifeConflictListScreen> {
  final WildlifeConflictRepository _repository = WildlifeConflictRepository();
  final RxList<WildlifeConflictIncident> _incidents = RxList<WildlifeConflictIncident>([]);
  final RxBool _isLoading = true.obs;
  final RxBool _hasError = false.obs;
  final RxInt _organisationId = 0.obs;

  @override
  void initState() {
    super.initState();
    _loadOrganisationId();
  }

  Future<void> _loadOrganisationId() async {
    try {
      // Get the current organisation ID from shared preferences
      OrganisationRepository repository;
      
      try {
        repository = Get.find<OrganisationRepository>();
      } catch (e) {
        // If repository is not registered, register it now
        repository = OrganisationRepository();
        Get.put(repository, permanent: true);
      }
      
      final organisation = await repository.getSelectedOrganisation();

      if (organisation != null) {
        _organisationId.value = organisation.id;
        await _loadIncidents();
      } else {
        _isLoading.value = false;
        _hasError.value = true;
      }
    } catch (e) {
      // Error occurred when loading organisation
      
      _isLoading.value = false;
      _hasError.value = true;
    }
  }

  Future<void> _loadIncidents() async {
    if (_organisationId.value == 0) return;
    
    _isLoading.value = true;
    _hasError.value = false;
    
    try {
      final incidents = await _repository.getIncidents(_organisationId.value);
      _incidents.value = incidents;
      _isLoading.value = false;
    } catch (e) {
               //log the number of incidents parsed
          AppLogger().e('Error $e incidents from API response ');
      _isLoading.value = false;
      _hasError.value = true;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Wildlife Conflicts'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _loadIncidents,
            tooltip: 'Refresh',
          ),
        ],
      ),
      body: Obx(() {
        if (_isLoading.value) {
          return const Center(child: CircularProgressIndicator());
        }

        if (_hasError.value) {
          return Center(
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                const Icon(Icons.error_outline, size: 48, color: Colors.red),
                const SizedBox(height: 16),
                const Text(
                  'Failed to load wildlife conflicts',
                  style: TextStyle(fontSize: 16),
                ),
                const SizedBox(height: 16),
                ElevatedButton(
                  onPressed: _loadIncidents,
                  child: const Text('Retry'),
                ),
              ],
            ),
          );
        }

        if (_incidents.isEmpty) {
          return EmptyState(
            icon: Icons.warning_amber_rounded,
            title: 'No Wildlife Conflicts',
            message: 'You haven\'t recorded any wildlife conflicts yet.',
            buttonLabel: 'Report Conflict',
            onButtonPressed: () => Get.toNamed(AppRoutes.createWildlifeConflict),
          );
        }

        return RefreshIndicator(
          onRefresh: _loadIncidents,
          child: ListView.builder(
            itemCount: _incidents.length,
            padding: const EdgeInsets.all(16),
            itemBuilder: (context, index) {
              final incident = _incidents[index];
              return _buildIncidentCard(incident);
            },
          ),
        );
      }),
      floatingActionButton: FloatingActionButton(
        onPressed: () => Get.toNamed(AppRoutes.createWildlifeConflict),
        tooltip: 'Report Conflict',
        child: const Icon(Icons.add),
      ),
    );
  }

  Widget _buildIncidentCard(WildlifeConflictIncident incident) {
    final formattedDate = DateFormat('dd/MM/yyyy').format(incident.incidentDate);
    // Attempt to parse time safely
    String formattedTime = incident.incidentTime;
    try {
      // Assuming time is stored as HH:mm or similar parsable format
      formattedTime = DateFormat('h:mm a').format(DateFormat('HH:mm').parse(incident.incidentTime));
    } catch (e) {
      print('Error parsing time in list screen: ${incident.incidentTime} - $e');
      // Keep original string if parsing fails
    }
    
    // Get appropriate icon based on conflict type ID (using default for now)
    IconData conflictIcon = _getConflictTypeIcon(incident.conflictTypeId);
    
    return Card(
      margin: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(16),
        side: BorderSide(color: Colors.blueGrey.shade100, width: 1),
      ),
      elevation: 4,
      shadowColor: Colors.black38,
      child: InkWell(
        onTap: () => Get.toNamed(
          AppRoutes.wildlifeConflictDetails,
          arguments: {'id': incident.id},
        ),
        borderRadius: BorderRadius.circular(12),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Header with status and title
            Padding(
              padding: const EdgeInsets.fromLTRB(16, 16, 16, 10),
              child: Row(
                children: [
                  // Status indicator
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                    decoration: BoxDecoration(
                      color: _getSyncStatusColor(incident.syncStatus),
                      borderRadius: BorderRadius.circular(20),
                    ),
                    child: Text(
                      incident.syncStatus.toUpperCase(),
                      style: const TextStyle(
                        color: Colors.white,
                        fontSize: 11,
                        fontWeight: FontWeight.bold,
                        letterSpacing: 0.5,
                      ),
                    ),
                  ),
                  const SizedBox(width: 10),
                  // Title
                  Expanded(
                    child: Text(
                      incident.title,
                      style: const TextStyle(
                        fontSize: 17,
                        fontWeight: FontWeight.w700,
                        color: Colors.black87,
                      ),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                  ),
                ],
              ),
            ),
            
            // Conflict type - prominently displayed
            Container(
              width: double.infinity,
              padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 8),
              decoration: BoxDecoration(
                color: Colors.red.shade50,
                border: Border(
                  bottom: BorderSide(color: Colors.red.shade100, width: 0.5),
                ),
              ),
              child: Row(
                children: [
                  Icon(conflictIcon, size: 16, color: Colors.red.shade800),
                  const SizedBox(width: 8),
                  Expanded(
                    child: Text(
                      incident.conflictTypeId?.toString() ?? 'Unknown Conflict Type', // Display ID
                      style: TextStyle(
                        color: Colors.red.shade900,
                        fontWeight: FontWeight.w600,
                        fontSize: 13,
                      ),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                  ),
                ],
              ),
            ),
            
            // Content
            Padding(
              padding: const EdgeInsets.fromLTRB(16, 10, 16, 12),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Date and Time row - date on left, time on right
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      // Date on left
                      Row(
                        children: [
                          Icon(Icons.calendar_today, size: 14, color: Colors.grey.shade600),
                          const SizedBox(width: 4),
                          Text(
                            formattedDate,
                            style: TextStyle(
                              color: Colors.grey.shade700,
                              fontSize: 12,
                              fontWeight: FontWeight.w500,
                            ),
                          ),
                        ],
                      ),
                      
                      // Time on right
                      Row(
                        children: [
                          Icon(Icons.access_time, size: 14, color: Colors.grey.shade600),
                          const SizedBox(width: 4),
                          Text(
                            formattedTime,
                            style: TextStyle(
                              color: Colors.grey.shade700,
                              fontSize: 12,
                              fontWeight: FontWeight.w500,
                            ),
                          ),
                        ],
                      ),
                    ],
                  ),
                  
                  const SizedBox(height: 12),
                  Divider(color: Colors.grey.shade300, height: 1),
                  const SizedBox(height: 12),
                  
                  // Show species chips - multiple species support
                  // Use speciesIds - Display chips if IDs exist
                  if (incident.speciesIds != null && incident.speciesIds!.isNotEmpty)
                    SizedBox(
                      height: 30,
                      child: ListView.builder(
                        scrollDirection: Axis.horizontal,
                        itemCount: incident.speciesIds!.length,
                        itemBuilder: (context, speciesIndex) {
                          final speciesId = incident.speciesIds![speciesIndex];
                          // TODO: Fetch species name from ID for better display
                          final speciesName = 'ID: $speciesId';
                          return Padding(
                            padding: const EdgeInsets.only(right: 4),
                            child: Chip(
                              materialTapTargetSize: MaterialTapTargetSize.shrinkWrap,
                              padding: const EdgeInsets.symmetric(horizontal: 4, vertical: 0),
                              label: Text(
                                speciesName, // Display ID for now
                                style: TextStyle(
                                  fontSize: 11,
                                  color: Colors.amber.shade900,
                                  fontWeight: FontWeight.w500,
                                ),
                              ),
                              avatar: Icon(
                                _getSpeciesIcon(speciesId), // Pass ID
                                size: 14,
                                color: Colors.amber.shade800,
                              ),
                              backgroundColor: Colors.amber.shade50,
                              side: BorderSide(color: Colors.amber.shade100),
                              visualDensity: VisualDensity.compact,
                            ),
                          );
                        },
                      ),
                    ), // Added comma here
                  // Removed fallback for incident.species as it doesn't exist
                  
                  const SizedBox(height: 8),
                  
                  // Description - full width
                  if (incident.description?.isNotEmpty == true) // Add null check
                    Container(
                      width: double.infinity,
                      padding: const EdgeInsets.all(8),
                      decoration: BoxDecoration(
                        color: Colors.grey.shade50,
                        borderRadius: BorderRadius.circular(8),
                        border: Border.all(color: Colors.grey.shade200),
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            incident.description!, // Use ! after null check
                            style: const TextStyle(fontSize: 13),
                            maxLines: 2,
                            overflow: TextOverflow.ellipsis,
                          ),
                        ],
                      ),
                    ),
                ],
              ),
            ),
            
            // Footer with view details button - more compact
            Container(
              padding: const EdgeInsets.fromLTRB(12, 0, 12, 8),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.end,
                children: [
                  TextButton.icon(
                    onPressed: () => Get.toNamed(
                      AppRoutes.wildlifeConflictDetails,
                      arguments: {'id': incident.id},
                    ),
                    icon: const Icon(Icons.arrow_forward, size: 14),
                    label: const Text('Details', style: TextStyle(fontSize: 12)),
                    style: TextButton.styleFrom(
                      foregroundColor: Colors.indigo,
                      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
                      minimumSize: Size.zero,
                      tapTargetSize: MaterialTapTargetSize.shrinkWrap,
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  // Helper function to determine conflict type icon
  IconData _getConflictTypeIcon(int? conflictTypeId) {
    // TODO: Implement logic based on ID if possible, or fetch type name
    // Returning default for now
    // Example: if (conflictTypeId == 1) return Icons.dangerous;
    return Icons.warning_amber_rounded;
  }

  // Helper function to determine species icon
  IconData _getSpeciesIcon(int? speciesId) {
    // TODO: Implement logic based on ID if possible, or fetch species name
    // Returning default for now
    // Example: if (speciesId == 1) return Icons.pets; // Assuming Elephant ID is 1
    return Icons.pets;
  }

  Color _getSyncStatusColor(String? status) {
    if (status == null) return Colors.grey;
    
    switch (status.toLowerCase()) {
      case 'synced':
        return Colors.green;
      case 'pending':
        return Colors.orange;
      case 'failed':
        return Colors.red;
      default:
        return Colors.grey;
    }
  }
}