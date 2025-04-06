import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:intl/intl.dart';
import 'package:regional_cbnrm/models/problem_animal_control_model.dart';
import 'package:regional_cbnrm/repositories/organisation_repository.dart';
import 'package:regional_cbnrm/repositories/problem_animal_control_repository.dart';
import 'package:regional_cbnrm/ui/widgets/empty_state.dart';
import 'package:regional_cbnrm/utils/app_routes.dart';

class ProblemAnimalControlListScreen extends StatefulWidget {
  const ProblemAnimalControlListScreen({super.key});

  @override
  State<ProblemAnimalControlListScreen> createState() => _ProblemAnimalControlListScreenState();
}

class _ProblemAnimalControlListScreenState extends State<ProblemAnimalControlListScreen> {
  final ProblemAnimalControlRepository _repository = ProblemAnimalControlRepository();
  final RxList<ProblemAnimalControl> _controls = RxList<ProblemAnimalControl>([]);
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
        await _loadControls();
      } else {
        _isLoading.value = false;
        _hasError.value = true;
      }
    } catch (e) {
      print('Error loading organisation ID: $e');
      _isLoading.value = false;
      _hasError.value = true;
    }
  }

  Future<void> _loadControls() async {
    if (_organisationId.value == 0) return;

    _isLoading.value = true;
    _hasError.value = false;

    try {
      final controls = await _repository.getControls(_organisationId.value);
      _controls.value = controls;
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
        title: const Text('Problem Animal Control'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _loadControls,
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
                  'Failed to load problem animal controls',
                  style: TextStyle(fontSize: 16),
                ),
                const SizedBox(height: 16),
                ElevatedButton(
                  onPressed: _loadControls,
                  child: const Text('Retry'),
                ),
              ],
            ),
          );
        }

        if (_controls.isEmpty) {
          return EmptyState(
            icon: Icons.pets,
            title: 'No Control Measures',
            message: 'You haven\'t recorded any problem animal control measures yet.',
            buttonLabel: 'Add Control Measure',
            onButtonPressed: () => Get.toNamed(AppRoutes.createProblemAnimalControl),
          );
        }

        return RefreshIndicator(
          onRefresh: _loadControls,
          child: ListView.builder(
            itemCount: _controls.length,
            padding: const EdgeInsets.all(16),
            itemBuilder: (context, index) {
              final control = _controls[index];
              return _buildControlCard(control);
            },
          ),
        );
      }),
      floatingActionButton: FloatingActionButton(
        onPressed: () => Get.toNamed(AppRoutes.createProblemAnimalControl),
        tooltip: 'Add Control Measure',
        child: const Icon(Icons.add),
      ),
    );
  }

  Widget _buildControlCard(ProblemAnimalControl control) {
    final formattedDate = DateFormat('dd MMM yyyy').format(control.date);

    return Card(
      margin: const EdgeInsets.only(bottom: 16),
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(12),
      ),
      elevation: 2,
      child: InkWell(
        onTap: () => Get.toNamed(
          AppRoutes.problemAnimalControlDetails,
          arguments: {'id': control.id},
        ),
        borderRadius: BorderRadius.circular(12),
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
                        fontSize: 18,
                        fontWeight: FontWeight.bold,
                      ),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 8),
              Row(
                children: [
                  const Icon(Icons.calendar_today, size: 16, color: Colors.grey),
                  const SizedBox(width: 4),
                  Text(
                    formattedDate,
                    style: const TextStyle(color: Colors.grey),
                  ),
                  const SizedBox(width: 16),
                  const Icon(Icons.access_time, size: 16, color: Colors.grey),
                  const SizedBox(width: 4),
                  Text(
                    control.time,
                    style: const TextStyle(color: Colors.grey),
                  ),
                ],
              ),
              const SizedBox(height: 8),
              Row(
                children: [
                  const Icon(Icons.numbers, size: 16, color: Colors.grey),
                  const SizedBox(width: 4),
                  Text(
                    '${control.estimatedNumber} animal(s)',
                    style: const TextStyle(color: Colors.grey),
                  ),
                ],
              ),
              const SizedBox(height: 8),
              Row(
                children: [
                  const Icon(Icons.description, size: 16, color: Colors.grey),
                  const SizedBox(width: 4),
                  Expanded(
                    child: Text(
                      control.description,
                      style: const TextStyle(fontSize: 14),
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                    ),
                  ),
                ],
              ),
              if (control.wildlifeConflictIncident != null)
                Padding(
                  padding: const EdgeInsets.only(top: 8),
                  child: Row(
                    children: [
                      const Icon(Icons.warning_amber_rounded, size: 16, color: Colors.grey),
                      const SizedBox(width: 4),
                      Expanded(
                        child: Text(
                          'Related to incident: ${control.wildlifeConflictIncident?.title ?? 'Unknown'}',
                          style: const TextStyle(
                            fontSize: 12,
                            fontStyle: FontStyle.italic,
                            color: Colors.grey,
                          ),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                    ],
                  ),
                ),
              const SizedBox(height: 8),
              Row(
                mainAxisAlignment: MainAxisAlignment.end,
                children: [
                  TextButton.icon(
                    icon: const Icon(Icons.arrow_forward),
                    label: const Text('View Details'),
                    onPressed: () => Get.toNamed(
                      AppRoutes.problemAnimalControlDetails,
                      arguments: {'id': control.id},
                    ),
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
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
