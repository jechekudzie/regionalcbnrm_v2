import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:intl/intl.dart';
import 'package:resource_africa/models/poaching_model.dart';
import 'package:resource_africa/repositories/poaching_repository.dart';
import 'package:resource_africa/services/connectivity_service.dart';
import 'package:resource_africa/ui/widgets/empty_state.dart';
import 'package:resource_africa/ui/widgets/sync_status_indicator.dart';
import 'package:resource_africa/utils/app_routes.dart';
import 'package:resource_africa/utils/app_preferences.dart';

class PoachingIncidentListScreen extends StatefulWidget {
  const PoachingIncidentListScreen({Key? key}) : super(key: key);

  @override
  State<PoachingIncidentListScreen> createState() => _PoachingIncidentListScreenState();
}

class _PoachingIncidentListScreenState extends State<PoachingIncidentListScreen> {
  final PoachingRepository _repository = PoachingRepository();
  final AppPreferences _preferences = AppPreferences();
  final ConnectivityService _connectivityService = Get.find<ConnectivityService>();
  
  final RxBool _isLoading = true.obs;
  final RxList<PoachingIncident> _incidents = <PoachingIncident>[].obs;
  final RxString _error = ''.obs;
  final RxInt _organisationId = 0.obs;
  
  @override
  void initState() {
    super.initState();
    _loadData();
  }
  
  Future<void> _loadData() async {
    _isLoading.value = true;
    _error.value = '';
    
    try {
      final org = await _preferences.getSelectedOrganisation();
      if (org == null) {
        _error.value = 'No active organisation selected';
        _isLoading.value = false;
        return;
      }
      
      _organisationId.value = org.id;
      final incidents = await _repository.getIncidents(_organisationId.value);
      _incidents.value = incidents;
    } catch (e) {
      _error.value = e.toString();
    } finally {
      _isLoading.value = false;
    }
  }
  
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Poaching Incidents'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _loadData,
          ),
        ],
      ),
      body: Obx(() {
        if (_isLoading.value) {
          return const Center(child: CircularProgressIndicator());
        }
        
        if (_error.value.isNotEmpty) {
          return Center(
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                const Icon(Icons.error_outline, color: Colors.red, size: 48),
                const SizedBox(height: 16),
                Text(_error.value, textAlign: TextAlign.center),
                const SizedBox(height: 16),
                ElevatedButton(
                  onPressed: _loadData,
                  child: const Text('Retry'),
                ),
              ],
            ),
          );
        }
        
        if (_incidents.isEmpty) {
          return EmptyState(
            icon: Icons.report_problem,
            title: 'No Poaching Incidents',
            message: 'There are no poaching incidents recorded for this organisation.',
            buttonLabel: 'Add Incident',
            onButtonPressed: () => Get.toNamed(
              '${AppRoutes.poachingCreate}?organisationId=${_organisationId.value}',
            ),
          );
        }
        
        return RefreshIndicator(
          onRefresh: _loadData,
          child: ListView.builder(
            itemCount: _incidents.length,
            padding: const EdgeInsets.all(16.0),
            itemBuilder: (context, index) {
              final incident = _incidents[index];
              return Card(
                margin: const EdgeInsets.only(bottom: 16.0),
                child: InkWell(
                  onTap: () => Get.toNamed(
                    '${AppRoutes.poachingDetails}/${incident.id}',
                  ),
                  child: Padding(
                    padding: const EdgeInsets.all(16.0),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Expanded(
                              child: Text(
                                incident.title,
                                style: const TextStyle(
                                  fontSize: 18,
                                  fontWeight: FontWeight.bold,
                                ),
                                maxLines: 1,
                                overflow: TextOverflow.ellipsis,
                              ),
                            ),
                            SyncStatusIndicator(syncStatus: incident.syncStatus),
                          ],
                        ),
                        const SizedBox(height: 8),
                        Text(
                          'Date: ${DateFormat('dd MMM yyyy').format(incident.date)}',
                          style: const TextStyle(color: Colors.grey),
                        ),
                        const SizedBox(height: 4),
                        Text(
                          'Time: ${incident.time}',
                          style: const TextStyle(color: Colors.grey),
                        ),
                        const SizedBox(height: 8),
                        Text(
                          incident.description,
                          maxLines: 2,
                          overflow: TextOverflow.ellipsis,
                        ),
                        const SizedBox(height: 12),
                        Wrap(
                          spacing: 8,
                          children: [
                            if (incident.species != null && incident.species!.isNotEmpty)
                              Chip(
                                label: Text('${incident.species!.length} Species'),
                                backgroundColor: Colors.green.shade100,
                              ),
                            if (incident.poachers != null && incident.poachers!.isNotEmpty)
                              Chip(
                                label: Text('${incident.poachers!.length} Poachers'),
                                backgroundColor: Colors.orange.shade100,
                              ),
                            if (incident.docketNumber != null)
                              Chip(
                                label: Text('Docket: ${incident.docketNumber}'),
                                backgroundColor: Colors.blue.shade100,
                              ),
                          ],
                        ),
                        const SizedBox(height: 8),
                        Row(
                          mainAxisAlignment: MainAxisAlignment.end,
                          children: [
                            Obx(() => _connectivityService.isConnected.value 
                              ? const SizedBox.shrink()
                              : const Icon(
                                  Icons.cloud_off,
                                  color: Colors.grey,
                                  size: 16,
                                ),
                            ),
                            const Icon(
                              Icons.arrow_forward_ios,
                              size: 16,
                              color: Colors.grey,
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
                ),
              );
            },
          ),
        );
      }),
      floatingActionButton: FloatingActionButton(
        child: const Icon(Icons.add),
        onPressed: () => Get.toNamed(
          '${AppRoutes.poachingCreate}?organisationId=${_organisationId.value}',
        ),
      ),
    );
  }
}