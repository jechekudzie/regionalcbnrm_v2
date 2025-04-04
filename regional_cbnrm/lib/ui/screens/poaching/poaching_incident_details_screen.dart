import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:intl/intl.dart';
import 'package:regional_cbnrm/models/poaching_model.dart';
import 'package:regional_cbnrm/repositories/poaching_repository.dart';
import 'package:regional_cbnrm/ui/widgets/location_map_view.dart';
import 'package:regional_cbnrm/ui/widgets/sync_status_indicator.dart';
import 'package:regional_cbnrm/utils/app_routes.dart';

class PoachingIncidentDetailsScreen extends StatefulWidget {
  const PoachingIncidentDetailsScreen({Key? key}) : super(key: key);

  @override
  State<PoachingIncidentDetailsScreen> createState() => _PoachingIncidentDetailsScreenState();
}

class _PoachingIncidentDetailsScreenState extends State<PoachingIncidentDetailsScreen> with SingleTickerProviderStateMixin {
  final PoachingRepository _repository = PoachingRepository();
  final int _incidentId = int.parse(Get.parameters['id'] ?? '0');
  late TabController _tabController;
  
  final RxBool _isLoading = true.obs;
  final Rx<PoachingIncident?> _incident = Rx<PoachingIncident?>(null);
  final RxString _error = ''.obs;
  
  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 3, vsync: this);
    _loadData();
  }
  
  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }
  
  Future<void> _loadData() async {
    _isLoading.value = true;
    _error.value = '';
    
    try {
      final incident = await _repository.getIncident(_incidentId);
      _incident.value = incident;
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
        title: Obx(() => Text(_incident.value?.title ?? 'Poaching Incident')),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _loadData,
          ),
        ],
        bottom: TabBar(
          controller: _tabController,
          tabs: const [
            Tab(text: 'Details'),
            Tab(text: 'Species'),
            Tab(text: 'Poachers'),
          ],
        ),
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
        
        final incident = _incident.value;
        if (incident == null) {
          return const Center(child: Text('No incident data found'));
        }
        
        return TabBarView(
          controller: _tabController,
          children: [
            _buildDetailsTab(incident),
            _buildSpeciesTab(incident),
            _buildPoachersTab(incident),
          ],
        );
      }),
      floatingActionButton: FloatingActionButton(
        child: const Icon(Icons.person_add),
        onPressed: () => _incident.value != null 
          ? Get.toNamed('${AppRoutes.poachingAddPoacher}/${_incident.value!.id}')
          : null,
      ),
    );
  }
  
  Widget _buildDetailsTab(PoachingIncident incident) {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(16.0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Card(
            child: Padding(
              padding: const EdgeInsets.all(16.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      const Text(
                        'Incident Information',
                        style: TextStyle(
                          fontSize: 18, 
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      SyncStatusIndicator(syncStatus: incident.syncStatus),
                    ],
                  ),
                  const Divider(),
                  _buildInfoRow('Title', incident.title),
                  _buildInfoRow('Date', DateFormat('dd MMMM yyyy').format(incident.date)),
                  _buildInfoRow('Time', incident.time),
                  _buildInfoRow('Description', incident.description),
                  if (incident.docketNumber != null) 
                    _buildInfoRow('Docket Number', incident.docketNumber!),
                  if (incident.docketStatus != null) 
                    _buildInfoRow('Docket Status', incident.docketStatus!),
                ],
              ),
            ),
          ),
          const SizedBox(height: 16),
          const Text(
            'Location',
            style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
          ),
          const SizedBox(height: 8),
          Container(
            height: 250,
            decoration: BoxDecoration(
              border: Border.all(color: Colors.grey.shade300),
              borderRadius: BorderRadius.circular(8),
            ),
            child: ClipRRect(
              borderRadius: BorderRadius.circular(8),
              child: LocationMapView(
                latitude: incident.latitude,
                longitude: incident.longitude,
                title: incident.title,
                readOnly: true,
              ),
            ),
          ),
          const SizedBox(height: 16),
          if (incident.methods != null && incident.methods!.isNotEmpty) ...[
            const Text(
              'Poaching Methods',
              style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
            ),
            const SizedBox(height: 8),
            Card(
              child: Padding(
                padding: const EdgeInsets.all(16.0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: incident.methods!.map((method) {
                    return Padding(
                      padding: const EdgeInsets.only(bottom: 8.0),
                      child: Row(
                        children: [
                          Icon(
                            Icons.circle,
                            size: 8,
                            color: Colors.grey.shade700,
                          ),
                          const SizedBox(width: 8),
                          Text(method.poachingMethod?.name ?? 'Unknown Method'),
                        ],
                      ),
                    );
                  }).toList(),
                ),
              ),
            ),
          ],
        ],
      ),
    );
  }
  
  Widget _buildSpeciesTab(PoachingIncident incident) {
    final species = incident.species;
    
    if (species == null || species.isEmpty) {
      return const Center(
        child: Text('No species information available'),
      );
    }
    
    return ListView.builder(
      itemCount: species.length,
      padding: const EdgeInsets.all(16.0),
      itemBuilder: (context, index) {
        final speciesItem = species[index];
        return Card(
          margin: const EdgeInsets.only(bottom: 16.0),
          child: Padding(
            padding: const EdgeInsets.all(16.0),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  speciesItem.species?.name ?? 'Unknown Species',
                  style: const TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                const SizedBox(height: 8),
                _buildInfoRow('Quantity', speciesItem.quantity.toString()),
                // Species details
                _buildInfoRow('Species ID', speciesItem.speciesId.toString()),
              ],
            ),
          ),
        );
      },
    );
  }
  
  Widget _buildPoachersTab(PoachingIncident incident) {
    final poachers = incident.poachers;
    
    if (poachers == null || poachers.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            const Text('No poachers recorded for this incident'),
            const SizedBox(height: 16),
            ElevatedButton.icon(
              icon: const Icon(Icons.person_add),
              label: const Text('Add Poacher'),
              onPressed: () => Get.toNamed('${AppRoutes.poachingAddPoacher}/${incident.id}'),
            ),
          ],
        ),
      );
    }
    
    return ListView.builder(
      itemCount: poachers.length,
      padding: const EdgeInsets.all(16.0),
      itemBuilder: (context, index) {
        final poacher = poachers[index];
        return Card(
          margin: const EdgeInsets.only(bottom: 16.0),
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
                        poacher.name,
                        style: const TextStyle(
                          fontSize: 18,
                          fontWeight: FontWeight.bold,
                        ),
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                      ),
                    ),
                    Chip(
                      label: Text(poacher.status),
                      backgroundColor: poacher.status == 'apprehended'
                          ? Colors.green.shade100
                          : Colors.orange.shade100,
                    ),
                  ],
                ),
                const SizedBox(height: 8),
                if (poacher.idNumber != null) 
                  _buildInfoRow('ID Number', poacher.idNumber!),
                if (poacher.gender != null) 
                  _buildInfoRow('Gender', poacher.gender!),
                if (poacher.age != null) 
                  _buildInfoRow('Age', poacher.age.toString()),
                if (poacher.nationality != null) 
                  _buildInfoRow('Nationality', poacher.nationality!),
                const SizedBox(height: 8),
                SyncStatusIndicator(syncStatus: poacher.syncStatus),
              ],
            ),
          ),
        );
      },
    );
  }
  
  Widget _buildInfoRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 8.0),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          SizedBox(
            width: 120,
            child: Text(
              '$label:',
              style: TextStyle(
                fontWeight: FontWeight.bold,
                color: Colors.grey.shade700,
              ),
            ),
          ),
          Expanded(
            child: Text(value),
          ),
        ],
      ),
    );
  }
}