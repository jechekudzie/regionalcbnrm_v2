import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:intl/intl.dart';
import 'package:regional_cbnrm/models/conflict_outcome.dart'; // Added import
// Added import
import 'package:regional_cbnrm/models/wildlife_conflict_model.dart';
import 'package:regional_cbnrm/repositories/wildlife_conflict_repository.dart';
import 'package:regional_cbnrm/services/notification_service.dart';
import 'package:regional_cbnrm/ui/widgets/location_map_view.dart';
import 'package:regional_cbnrm/utils/app_routes.dart';

class WildlifeConflictDetailsScreen extends StatefulWidget {
  WildlifeConflictDetailsScreen({Key? key}) : super(key: key);

  // Creating repository instance here to avoid passing it in constructor
  final WildlifeConflictRepository repository = WildlifeConflictRepository();

  @override
  State<WildlifeConflictDetailsScreen> createState() => _WildlifeConflictDetailsScreenState();
}

class _WildlifeConflictDetailsScreenState extends State<WildlifeConflictDetailsScreen> {
  // final WildlifeConflictRepository _repository = WildlifeConflictRepository();
  final NotificationService _notificationService = Get.find<NotificationService>();

  final RxBool _isLoading = true.obs;
  final RxBool _hasError = false.obs;
  final Rx<WildlifeConflictIncident?> _incident = Rx<WildlifeConflictIncident?>(null);
  late int _incidentId;

  // Helper method to safely format time strings
  String _formatTimeString(String timeString) {
    try {
      // For debugging
      print('Trying to format time string: $timeString');

      // Handle date-time formats that might include the date
      if (timeString.contains('T')) {
        // ISO format like "2025-04-03T15:30:00.000000Z"
        final dateTime = DateTime.parse(timeString);
        return DateFormat('h:mm a').format(dateTime);
      }
      // Handle time-only formats with colons
      else if (timeString.contains(':')) {
        try {
          // Try to parse as HH:mm
          return DateFormat('h:mm a').format(DateFormat('HH:mm').parse(timeString));
        } catch (e) {
          // If that fails, try other common formats
          try {
            // Try with seconds: HH:mm:ss
            return DateFormat('h:mm a').format(DateFormat('HH:mm:ss').parse(timeString));
          } catch (e2) {
            // Just return the original if all parsing attempts fail
            return timeString;
          }
        }
      }
      // Handle date strings like "03/04/2025"
      else if (timeString.contains('/')) {
        // This is likely a date, not a time - return as is
        return timeString;
      }
      // Any other format, return as is
      return timeString;
    } catch (e) {
      print('Error formatting time string: $timeString - $e');
      // Return the original string if parsing fails
      return timeString;
    }
  }

  @override
  void initState() {
    super.initState();
    // Safely get arguments
    final args = Get.arguments;
    if (args is Map && args.containsKey('id') && args['id'] is int) {
      _incidentId = args['id'];
      _loadIncident();
    } else {
      // Handle error: Invalid arguments
      _isLoading.value = false;
      _hasError.value = true;
      // Optionally show an error message or navigate back
      Get.back();
      _notificationService.showSnackBar(
        'Invalid incident ID provided.',
        type: SnackBarType.error,
      );
    }
  }

  Future<void> _loadIncident() async {
    _isLoading.value = true;
    _hasError.value = false;

    try {
      final incident = await widget.repository.getIncident(_incidentId);
      if (mounted) {
        _incident.value = incident;
        _isLoading.value = false;
      }
    } catch (e) {
      if (mounted) {
        _isLoading.value = false;
        _hasError.value = true;
        _notificationService.showSnackBar(
          'Failed to load incident details: ${e.toString()}',
          type: SnackBarType.error,
        );
      }
    }
  }

  Future<void> _addOutcome() async {
    final result = await Get.toNamed(
      AppRoutes.addConflictOutcome,
      arguments: {'incidentId': _incidentId},
    );

    if (result == true && mounted) { // Check if mounted after async gap
      await _loadIncident(); // Reload to show the new outcome
      _notificationService.showSnackBar(
        'Conflict outcome added successfully',
        type: SnackBarType.success,
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Conflict Details'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _loadIncident,
            tooltip: 'Refresh',
          ),
          IconButton(
            icon: const Icon(Icons.delete),
            onPressed: () => _showDeleteConfirmationDialog(context),
            tooltip: 'Delete',
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
                  'Failed to load conflict details',
                  style: TextStyle(fontSize: 16),
                ),
                const SizedBox(height: 16),
                ElevatedButton(
                  onPressed: _loadIncident,
                  child: const Text('Retry'),
                ),
              ],
            ),
          );
        }

        final incident = _incident.value;
        if (incident == null) {
          // This case might happen if loading failed but _hasError is false, or initial state
          return const Center(child: Text('Incident not found or failed to load.'));
        }

        return RefreshIndicator(
          onRefresh: _loadIncident,
          child: SingleChildScrollView(
            physics: const AlwaysScrollableScrollPhysics(), // Ensure scrollable even if content fits
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Title and status
                Row(
                  children: [
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                      decoration: BoxDecoration(
                        color: _getSyncStatusColor(incident.syncStatus),
                        borderRadius: BorderRadius.circular(4),
                      ),
                      child: Text(
                        incident.syncStatus,
                        style: const TextStyle(
                          color: Colors.white,
                          fontSize: 12,
                        ),
                      ),
                    ),
                    const SizedBox(width: 8),
                    Expanded(
                      child: Text(
                        incident.title,
                        style: Theme.of(context).textTheme.headlineSmall?.copyWith(fontWeight: FontWeight.bold),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 24),

                // Incident Information Card
                _buildSectionTitle('Incident Information'),
                Card(
                  elevation: 2,
                  child: Padding(
                    padding: const EdgeInsets.all(16),
                    child: Column(
                      children: [
                        _buildInfoRow(
                          'Date',
                          DateFormat('dd/MM/yyyy').format(incident.incidentDate), // Use incidentDate
                          Icons.calendar_today,
                        ),
                        const Divider(),
                        _buildInfoRow(
                          'Time',
                          incident.incidentTime, // Use incidentTime
                          Icons.access_time,
                        ),
                        const Divider(),
                        _buildSpeciesInfoRow( // Pass speciesIds instead of Species object
                          'Species Involved',
                          incident.speciesIds,
                          Icons.pets,
                        ),
                        const Divider(),
                        _buildInfoRow(
                          'Conflict Type',
                          incident.conflictTypeId?.toString() ?? 'N/A', // Use conflictTypeId
                          Icons.warning_amber_rounded,
                        ),
                      ],
                    ),
                  ),
                ),
                const SizedBox(height: 24),

                // Description Card
                _buildSectionTitle('Description'),
                Card(
                  elevation: 2,
                  child: Padding(
                    padding: const EdgeInsets.all(16),
                    child: Text(incident.description?.isNotEmpty == true ? incident.description! : 'No Description Provided'), // Add null check
                  ),
                ),
                const SizedBox(height: 24),

                // Location Card
                _buildSectionTitle('Location'),
                Card(
                  elevation: 2,
                  child: Padding(
                    padding: const EdgeInsets.all(16),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          'Lat: ${incident.latitude?.toStringAsFixed(6) ?? 'N/A'}, Lng: ${incident.longitude?.toStringAsFixed(6) ?? 'N/A'}', // Add null checks
                          style: const TextStyle(
                            fontSize: 14,
                            fontFamily: 'monospace',
                          ),
                        ),
                        const SizedBox(height: 8),
                        SizedBox(
                          height: 200,
                          child: LocationMapView(
                            latitude: incident.latitude ?? 0.0, // Provide default value
                            longitude: incident.longitude ?? 0.0, // Provide default value
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
                const SizedBox(height: 24),

                // Outcomes Section
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    _buildSectionTitle('Outcomes'),
                    TextButton.icon(
                      icon: const Icon(Icons.add),
                      label: const Text('Add Outcome'),
                      onPressed: _addOutcome,
                    ),
                  ],
                ),
                const SizedBox(height: 8),
                // _buildOutcomesList(incident.outcomes), // Removed: Outcomes are not directly on the incident model
                const Card( // Placeholder for outcomes
                  elevation: 2,
                  child: Padding(
                    padding: EdgeInsets.all(16),
                    child: Center(child: Text('Outcomes loading/display not implemented yet.')),
                  ),
                ),
                const SizedBox(height: 80), // Space for FAB
              ],
            ),
          ),
        );
      }),
      floatingActionButton: FloatingActionButton.extended(
        onPressed: () => Get.toNamed(
          AppRoutes.createProblemAnimalControl,
          arguments: {'wildlifeConflictIncidentId': _incidentId},
        ),
        icon: const Icon(Icons.pets), // Consider a more relevant icon like Healing or Security
        label: const Text('Add Control Measure'),
      ),
    );
  }

  Widget _buildSectionTitle(String title) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 8.0),
      child: Text(
        title,
        style: Theme.of(context).textTheme.titleLarge,
      ),
    );
  }

  Widget _buildInfoRow(String label, String? value, IconData icon) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8.0),
      child: Row(
        children: [
          Icon(icon, size: 18, color: Colors.grey[600]),
          const SizedBox(width: 12),
          Text(
            '$label:',
            style: const TextStyle(fontWeight: FontWeight.bold),
          ),
          const SizedBox(width: 8),
          Expanded(
            child: Text(
              value != null
                  ? (label == 'Time' ? _formatTimeString(value) : value)
                  : 'N/A',
              style: Theme.of(context).textTheme.bodyMedium,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSpeciesInfoRow(String label, List<int>? speciesIds, IconData icon) { // Changed parameter type
    final incident = _incident.value;

    // Display list of species IDs
    if (speciesIds != null && speciesIds.isNotEmpty) {
      return Padding(
        padding: const EdgeInsets.symmetric(vertical: 8.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Icon(icon, size: 18, color: Colors.grey[600]),
                const SizedBox(width: 12),
                Text(
                  '$label:',
                  style: const TextStyle(fontWeight: FontWeight.bold),
                ),
              ],
            ),
            const SizedBox(height: 8),
            // TODO: Fetch species names from repository based on IDs for better display
            Padding(
              padding: const EdgeInsets.only(left: 30),
              child: Text(
                'IDs: ${speciesIds.join(', ')}',
                style: Theme.of(context).textTheme.bodyMedium,
              ),
            ),
          ],
        ),
      );
    }

    // Display if no species IDs are associated
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8.0),
      child: Row(
        children: [
          Icon(icon, size: 18, color: Colors.grey[600]),
          const SizedBox(width: 12),
          Text(
            '$label:',
            style: const TextStyle(fontWeight: FontWeight.bold),
          ),
          const SizedBox(width: 8),
          Expanded(
            child: Text(
              'N/A', // No species IDs provided
              style: Theme.of(context).textTheme.bodyMedium,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildOutcomesList(List<ConflictOutcome>? outcomes) { // Renamed type
    if (outcomes == null || outcomes.isEmpty) {
      return const Card(
        elevation: 2,
        child: Padding(
          padding: EdgeInsets.all(16),
          child: Center(child: Text('No outcomes recorded yet.')),
        ),
      );
    }

    return ListView.builder(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      itemCount: outcomes.length,
      itemBuilder: (context, index) {
        final outcome = outcomes[index];
        final outcomeDate = outcome.createdAt != null
            ? DateFormat('dd/MM/yyyy').format(outcome.createdAt!)
            : 'N/A'; // Use createdAt and add null check
        return Card(
          elevation: 2,
          margin: const EdgeInsets.only(bottom: 12),
          child: Padding(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                      decoration: BoxDecoration(
                        color: _getSyncStatusColor(outcome.syncStatus),
                        borderRadius: BorderRadius.circular(4),
                      ),
                      child: Text(
                        outcome.syncStatus, // syncStatus is not nullable
                        style: const TextStyle(color: Colors.white, fontSize: 12),
                      ),
                    ),
                    const SizedBox(width: 8),
                    Expanded(
                      child: Text(
                        outcome.name, // Use name directly
                        style: Theme.of(context).textTheme.titleMedium?.copyWith(fontWeight: FontWeight.bold),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 8),
                Row(
                  children: [
                    Icon(Icons.calendar_today, size: 14, color: Colors.grey[600]),
                    const SizedBox(width: 4),
                    Text(
                      outcomeDate,
                      style: Theme.of(context).textTheme.bodySmall,
                    ),
                  ],
                ),
                if (outcome.description?.isNotEmpty == true) // Use description
                  Padding(
                    padding: const EdgeInsets.only(top: 8),
                    child: Text(outcome.description!), // Use description
                  ),
                // Display Dynamic Field Definitions (Labels) associated with this outcome type
                if (outcome.dynamicFields?.isNotEmpty == true)
                  Padding(
                    padding: const EdgeInsets.only(top: 8),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Text('Associated Fields:', style: TextStyle(fontWeight: FontWeight.bold)),
                        ...outcome.dynamicFields!.map((field) { // Use dynamicFields
                          return Padding(
                            padding: const EdgeInsets.only(left: 8.0, top: 4.0),
                            child: Text('• ${field.label ?? field.fieldName} (${field.fieldType})'), // Display field label/name and type
                          );
                        }).toList(),
                      ],
                    ),
                  ),
              ], // Keep this closing bracket
            ),
          ),
        );
      },
    );
  }

  Color _getSyncStatusColor(String? status) {
    switch (status) {
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

  // Method to handle the deletion logic
  Future<void> _deleteIncident(bool deleteFromServer) async {
    try {
      // Use setState from the State class to update the screen's state
      setState(() {
        _isLoading.value = true; // Show loading indicator on the main screen
        _hasError.value = false;
      });
      await widget.repository.deleteIncident(_incidentId, deleteFromServer);
      _notificationService.showSnackBar(
        'Incident deleted successfully',
        type: SnackBarType.success,
      );
      Get.back(); // Navigate back after successful deletion
    } catch (e) {
      if (mounted) { // Check if the widget is still mounted
        // Use setState from the State class
        setState(() {
          _isLoading.value = false; // Hide loading indicator
          _hasError.value = true; // Optionally show an error state on the main screen
        });
        _notificationService.showSnackBar(
          'Failed to delete incident: ${e.toString()}',
          type: SnackBarType.error,
        );
      }
    } finally {
      if (mounted) { // Check if the widget is still mounted
        // Use setState from the State class
        setState(() {
          _isLoading.value = false; // Ensure loading indicator is hidden
        });
      }
    }
  }

  // Method to show the confirmation dialog
  Future<void> _showDeleteConfirmationDialog(BuildContext context) async {
    bool deleteFromServer = false; // Local variable for the dialog state

    return showDialog<void>(
      context: context,
      barrierDismissible: false, // user must tap button!
      builder: (BuildContext dialogContext) { // Use dialogContext to avoid shadowing outer context
        return StatefulBuilder( // Use StatefulBuilder for the dialog's own state (checkbox)
          builder: (BuildContext context, StateSetter setStateDialog) { // Use setStateDialog for dialog state
            return AlertDialog(
              title: const Text('Confirm Delete'),
              content: SingleChildScrollView(
                child: ListBody(
                  children: <Widget>[
                    const Text('Are you sure you want to delete this incident?'),
                    const SizedBox(height: 16),
                    Row(
                      children: [
                        Checkbox(
                          value: deleteFromServer,
                          onChanged: (bool? value) {
                            setStateDialog(() { // Use the dialog's setState
                              deleteFromServer = value ?? false;
                            });
                          },
                        ),
                        const Text('Delete from server'),
                      ],
                    ),
                  ],
                ),
              ),
              actions: <Widget>[
                TextButton(
                  child: const Text('Cancel'),
                  onPressed: () {
                    Navigator.of(dialogContext).pop(); // Use dialog's context
                  },
                ),
                ElevatedButton(
                  style: ElevatedButton.styleFrom(backgroundColor: Colors.red),
                  child: const Text('Delete', style: TextStyle(color: Colors.white)),
                  onPressed: () async {
                    Navigator.of(dialogContext).pop(); // Close the dialog
                    // Call the State's _deleteIncident method, passing the dialog's state
                    await _deleteIncident(deleteFromServer);
                  },
                ),
              ],
            );
          },
        );
      },
    );
  }
} // This closing brace belongs to the _WildlifeConflictDetailsScreenState class
