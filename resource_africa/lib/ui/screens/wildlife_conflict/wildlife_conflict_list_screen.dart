import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:resource_africa/models/wildlife_conflict_model.dart';
import 'package:resource_africa/repositories/organisation_repository.dart';
import 'package:resource_africa/repositories/wildlife_conflict_repository.dart';
import 'package:resource_africa/ui/widgets/empty_state.dart';
import 'package:resource_africa/utils/app_routes.dart';
import 'package:intl/intl.dart';

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
    final formattedDate = DateFormat('dd/MM/yyyy').format(incident.date);
    final formattedTime = DateFormat('h:mm a').format(DateTime.parse(incident.time));
    
    // Get appropriate icon based on conflict type
    IconData conflictIcon = _getConflictTypeIcon(incident.conflictType?.name);
    
    return Card(
      margin: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(12),
        side: const BorderSide(color: Colors.black12, width: 0.5),
      ),
      elevation: 2,
      shadowColor: Colors.black26,
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
              padding: const EdgeInsets.fromLTRB(12, 12, 12, 8),
              child: Row(
                children: [
                  // Status indicator
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                    decoration: BoxDecoration(
                      color: _getSyncStatusColor(incident.syncStatus),
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: Text(
                      incident.syncStatus ?? 'unknown',
                      style: const TextStyle(
                        color: Colors.white,
                        fontSize: 10,
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                  ),
                  const SizedBox(width: 8),
                  // Title
                  Expanded(
                    child: Text(
                      incident.title ?? 'No Title',
                      style: const TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.bold,
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
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
              color: Colors.red.shade50,
              child: Row(
                children: [
                  Icon(conflictIcon, size: 16, color: Colors.red.shade800),
                  const SizedBox(width: 8),
                  Expanded(
                    child: Text(
                      incident.conflictType?.name ?? 'Unknown Conflict Type',
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
              padding: const EdgeInsets.fromLTRB(12, 8, 12, 8),
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
                  
                  const SizedBox(height: 8),
                  
                  // Show species chips - multiple species support
                  if (incident.speciesList != null && incident.speciesList!.isNotEmpty)
                    SizedBox(
                      height: 30,
                      child: ListView.builder(
                        scrollDirection: Axis.horizontal,
                        itemCount: incident.speciesList!.length,
                        itemBuilder: (context, speciesIndex) {
                          final species = incident.speciesList![speciesIndex];
                          return Padding(
                            padding: const EdgeInsets.only(right: 4),
                            child: Chip(
                              materialTapTargetSize: MaterialTapTargetSize.shrinkWrap,
                              padding: const EdgeInsets.symmetric(horizontal: 4, vertical: 0),
                              label: Text(
                                species.name ?? 'Unknown Species',
                                style: TextStyle(
                                  fontSize: 11,
                                  color: Colors.amber.shade900,
                                  fontWeight: FontWeight.w500,
                                ),
                              ),
                              avatar: Icon(
                                _getSpeciesIcon(species.name),
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
                    )
                  // Fallback to single species for backward compatibility
                  else if (incident.species != null)
                    Chip(
                      materialTapTargetSize: MaterialTapTargetSize.shrinkWrap,
                      padding: const EdgeInsets.symmetric(horizontal: 4, vertical: 0),
                      label: Text(
                        incident.species!.name ?? 'Unknown Species',
                        style: TextStyle(
                          fontSize: 11,
                          color: Colors.amber.shade900,
                          fontWeight: FontWeight.w500,
                        ),
                      ),
                      avatar: Icon(
                        _getSpeciesIcon(incident.species!.name),
                        size: 14,
                        color: Colors.amber.shade800,
                      ),
                      backgroundColor: Colors.amber.shade50,
                      side: BorderSide(color: Colors.amber.shade100),
                      visualDensity: VisualDensity.compact,
                    ),
                  
                  const SizedBox(height: 8),
                  
                  // Description - full width
                  if (incident.description.isNotEmpty)
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
                            incident.description,
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
  IconData _getConflictTypeIcon(String? conflictType) {
    if (conflictType == null) return Icons.warning_amber_rounded;
    
    conflictType = conflictType.toLowerCase();
    
    if (conflictType.contains('attack')) return Icons.dangerous;
    if (conflictType.contains('damage')) return Icons.home_work;
    if (conflictType.contains('crop')) return Icons.grass;
    if (conflictType.contains('livestock')) return Icons.agriculture;
    if (conflictType.contains('sighting')) return Icons.visibility;
    
    return Icons.warning_amber_rounded;
  }

  // Helper function to determine species icon
  IconData _getSpeciesIcon(String? species) {
    if (species == null) return Icons.pets;
    
    species = species.toLowerCase();
    
    if (species.contains('elephant')) return Icons.pets;
    if (species.contains('leopard') || species.contains('tiger') || species.contains('lion')) return Icons.catching_pokemon;
    if (species.contains('snake') || species.contains('reptile')) return Icons.pest_control;
    if (species.contains('monkey') || species.contains('primate')) return Icons.emoji_nature;
    if (species.contains('bird')) return Icons.flight;
    
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