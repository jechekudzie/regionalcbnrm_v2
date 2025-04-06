import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:intl/intl.dart';
import 'package:regional_cbnrm/models/problem_animal_control_model.dart';
import 'package:regional_cbnrm/repositories/problem_animal_control_repository.dart';
import 'package:regional_cbnrm/ui/widgets/location_map_view.dart';
import 'package:regional_cbnrm/utils/app_routes.dart';

class ProblemAnimalControlDetailsScreen extends StatefulWidget {
  const ProblemAnimalControlDetailsScreen({super.key});

  @override
  State<ProblemAnimalControlDetailsScreen> createState() => _ProblemAnimalControlDetailsScreenState();
}

class _ProblemAnimalControlDetailsScreenState extends State<ProblemAnimalControlDetailsScreen> {
  final ProblemAnimalControlRepository _repository = ProblemAnimalControlRepository();

  final RxBool _isLoading = true.obs;
  final RxBool _hasError = false.obs;
  final Rx<ProblemAnimalControl?> _control = Rx<ProblemAnimalControl?>(null);
  late int _controlId;

  @override
  void initState() {
    super.initState();
    _controlId = Get.arguments['id'];
    _loadControl();
  }

  Future<void> _loadControl() async {
    _isLoading.value = true;
    _hasError.value = false;

    try {
      final control = await _repository.getControl(_controlId);
      _control.value = control;
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
        title: const Text('Control Measure Details'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _loadControl,
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
                  'Failed to load control measure details',
                  style: TextStyle(fontSize: 16),
                ),
                const SizedBox(height: 16),
                ElevatedButton(
                  onPressed: _loadControl,
                  child: const Text('Retry'),
                ),
              ],
            ),
          );
        }

        final control = _control.value;
        if (control == null) {
          return const Center(child: Text('Control measure not found'));
        }

        final formattedDate = DateFormat('dd MMM yyyy').format(control.date); // Ensure control.date is not null

        return SingleChildScrollView(
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
                      color: _getSyncStatusColor(control.syncStatus),
                      borderRadius: BorderRadius.circular(4),
                    ),
                    child: Text(
                      control.syncStatus ?? 'unknown',
                      style: const TextStyle(
                        color: Colors.white,
                        fontSize: 12,
                      ),
                    ),
                  ),
                  const SizedBox(width: 8),
                  Expanded(
                    child: Text(
                      control.controlMeasure?.name ?? 'Unknown Control Measure',
                      style: const TextStyle(
                        fontSize: 24,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 24),

              // Control information
              const Text(
                'Control Measure Information',
                style: TextStyle(
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                ),
              ),
              const SizedBox(height: 8),
              Card(
                child: Padding(
                  padding: const EdgeInsets.all(16),
                  child: Column(
                    children: [
                      _buildInfoRow(
                        'Date',
                        formattedDate,
                        Icons.calendar_today,
                      ),
                      const Divider(),
                      _buildInfoRow(
                        'Time',
                        control.time,
                        Icons.access_time,
                      ),
                      const Divider(),
                      _buildInfoRow(
                        'Number of Animals',
                        control.estimatedNumber.toString(),
                        Icons.numbers,
                      ),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 24),

              // Related incident
              if (control.wildlifeConflictIncident != null) ...[
                const Text(
                  'Related Wildlife Conflict',
                  style: TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                const SizedBox(height: 8),
                Card(
                  child: InkWell(
                    onTap: () => Get.toNamed(
                      AppRoutes.wildlifeConflictDetails,
                      arguments: {'id': control.wildlifeConflictIncidentId},
                    ),
                    child: Padding(
                      padding: const EdgeInsets.all(16),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            control.wildlifeConflictIncident?.title ?? 'Unknown Incident',
                            style: const TextStyle(
                              fontSize: 16,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                          const SizedBox(height: 8),
                          Row(
                            children: [
                              const Icon(Icons.calendar_today, size: 16, color: Colors.grey),
                              const SizedBox(width: 4),
                              Text(
                                control.wildlifeConflictIncident?.incidentDate != null ? DateFormat('dd MMM yyyy').format(control.wildlifeConflictIncident!.incidentDate) : 'Unknown Date',
                                style: const TextStyle(color: Colors.grey),
                              ),
                            ],
                          ),
                          const SizedBox(height: 8),
                          Text(
                            control.wildlifeConflictIncident?.description ?? 'No Description',
                            style: const TextStyle(fontSize: 14),
                            maxLines: 2,
                            overflow: TextOverflow.ellipsis,
                          ),
                          const SizedBox(height: 8),
                          Align(
                            alignment: Alignment.centerRight,
                            child: TextButton.icon(
                              icon: const Icon(Icons.visibility),
                              label: const Text('View Incident'),
                              onPressed: () => Get.toNamed(
                                AppRoutes.wildlifeConflictDetails,
                                arguments: {'id': control.wildlifeConflictIncidentId},
                              ),
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ),
                const SizedBox(height: 24),
              ],

              // Description
              const Text(
                'Description',
                style: TextStyle(
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                ),
              ),
              const SizedBox(height: 8),
              Card(
                child: Padding(
                  padding: const EdgeInsets.all(16),
                  child: Text(control.description),
                ),
              ),
              const SizedBox(height: 24),

              // Location
              const Text(
                'Location',
                style: TextStyle(
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                ),
              ),
              const SizedBox(height: 8),
              Card(
                child: Padding(
                  padding: const EdgeInsets.all(16),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        'Lat: ${control.latitude.toStringAsFixed(6)}, Lng: ${control.longitude.toStringAsFixed(6)}',
                        style: const TextStyle(
                          fontSize: 14,
                          fontFamily: 'monospace',
                        ),
                      ),
                      const SizedBox(height: 8),
                      SizedBox(
                        height: 200,
                        child: LocationMapView(
                          latitude: control.latitude,
                          longitude: control.longitude,
                        ),
                      ),
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 32),
            ],
          ),
        );
      }),
    );
  }

  Widget _buildInfoRow(String label, String value, IconData icon) {
    return Row(
      children: [
        Icon(icon, size: 18, color: Colors.grey),
        const SizedBox(width: 8),
        Text(
          '$label:',
          style: const TextStyle(
            fontWeight: FontWeight.bold,
          ),
        ),
        const SizedBox(width: 8),
        Expanded(
          child: Text(
            value,
            style: const TextStyle(fontSize: 16),
          ),
        ),
      ],
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
}
