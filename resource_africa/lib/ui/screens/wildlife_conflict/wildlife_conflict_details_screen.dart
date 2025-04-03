import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:intl/intl.dart';
import 'package:resource_africa/models/wildlife_conflict_model.dart';
import 'package:resource_africa/repositories/wildlife_conflict_repository.dart';
import 'package:resource_africa/services/notification_service.dart';
import 'package:resource_africa/ui/widgets/location_map_view.dart';
import 'package:resource_africa/utils/app_routes.dart';

class WildlifeConflictDetailsScreen extends StatefulWidget {
  const WildlifeConflictDetailsScreen({Key? key, required this.repository}) : super(key: key);

  final WildlifeConflictRepository repository;

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
        'Error',
        'Invalid incident ID provided.',
        SnackBarType.error,
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
          'Error',
          'Failed to load incident details: ${e.toString()}',
          SnackBarType.error,
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
        'Success',
        'Conflict outcome added successfully',
        SnackBarType.success,
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
                        incident.syncStatus ?? 'unknown',
                        style: const TextStyle(
                          color: Colors.white,
                          fontSize: 12,
                        ),
                      ),
                    ),
                    const SizedBox(width: 8),
                    Expanded(
                      child: Text(
                        incident.title ?? 'No Title',
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
                          incident.date != null ? DateFormat('dd/MM/yyyy').format(incident.date!) : 'N/A',
                          Icons.calendar_today,
                        ),
                        const Divider(),
                        _buildInfoRow(
                          'Time',
                          incident.time ?? 'N/A',
                          Icons.access_time,
                        ),
                        const Divider(),
                        _buildSpeciesInfoRow(
                          'Species Involved',
                          incident.species ?? [],
                          Icons.pets,
                        ),
                        const Divider(),
                        _buildInfoRow(
                          'Conflict Type',
                          incident.conflictType?.name ?? 'N/A',
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
                    child: Text(incident.description?.isNotEmpty ?? false ? incident.description! : 'No Description Provided'),
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
                          'Lat: ${incident.latitude?.toStringAsFixed(6) ?? 'N/A'}, Lng: ${incident.longitude?.toStringAsFixed(6) ?? 'N/A'}',
                          style: const TextStyle(
                            fontSize: 14,
                            fontFamily: 'monospace',
                          ),
                        ),
                        const SizedBox(height: 8),
                        if (incident.latitude != null && incident.longitude != null)
                          SizedBox(
                            height: 200,
                            child: LocationMapView(
                              latitude: incident.latitude!,
                              longitude: incident.longitude!,
                            ),
                          )
                        else
                          const Text('Location data not available for map view.'),
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
                _buildOutcomesList(incident.outcomes),
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
                  ? DateFormat('h:mm a').format(DateFormat('HH:mm').parse(value))
                  : 'N/A',
              style: Theme.of(context).textTheme.bodyMedium,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSpeciesInfoRow(String label, List<Species> species, IconData icon) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8.0),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(icon, size: 18, color: Colors.grey[600]),
          const SizedBox(width: 12),
          Text(
            '$label:',
            style: const TextStyle(fontWeight: FontWeight.bold),
          ),
          const SizedBox(width: 8),
          Expanded(
            child: species.isEmpty
                ? Text('N/A', style: Theme.of(context).textTheme.bodyMedium)
                : Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: species.map((s) => Padding(
                      padding: const EdgeInsets.only(bottom: 4.0),
                      child: Text(
                        s.name ?? 'Unknown Species',
                        style: Theme.of(context).textTheme.bodyMedium,
                      ),
                    )).toList(),
                  ),
          ),
        ],
      ),
    );
  }

  Widget _buildOutcomesList(List<WildlifeConflictOutcome>? outcomes) {
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
        final outcomeDate = DateFormat('dd/MM/yyyy').format(outcome.date);
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
                        outcome.syncStatus ?? 'unknown',
                        style: const TextStyle(color: Colors.white, fontSize: 12),
                      ),
                    ),
                    const SizedBox(width: 8),
                    Expanded(
                      child: Text(
                        outcome.conflictOutcome?.name ?? 'Unknown Outcome',
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
                if (outcome.notes != null && outcome.notes!.isNotEmpty)
                  Padding(
                    padding: const EdgeInsets.only(top: 8),
                    child: Text(outcome.notes!),
                  ),
                if (outcome.dynamicValues != null && outcome.dynamicValues!.isNotEmpty)
                  Padding(
                    padding: const EdgeInsets.only(top: 8),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: outcome.dynamicValues!.map((value) {
                        return Padding(
                          padding: const EdgeInsets.only(bottom: 4),
                          child: Row(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                '${value.dynamicField?.label ?? 'Field'}: ',
                                style: const TextStyle(fontWeight: FontWeight.bold),
                              ),
                              Expanded(child: Text(value.value)),
                            ],
                          ),
                        );
                      }).toList(),
                    ),
                  ),
              ],
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
        'Success',
        'Incident deleted successfully',
        SnackBarType.success,
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
          'Error',
          'Failed to delete incident: ${e.toString()}',
          SnackBarType.error,
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