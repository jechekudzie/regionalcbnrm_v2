import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:intl/intl.dart';

import 'package:resource_africa/models/hunting_model.dart';
import 'package:resource_africa/repositories/hunting_activity_repository.dart';
import 'package:resource_africa/repositories/organisation_repository.dart';
import 'package:resource_africa/services/notification_service.dart';
import 'package:resource_africa/ui/screens/quota_allocation/quota_allocation_create_screen.dart';
import 'package:resource_africa/utils/app_constants.dart';

class QuotaAllocationListScreen extends StatefulWidget {
  const QuotaAllocationListScreen({super.key});

  @override
  State<QuotaAllocationListScreen> createState() => _QuotaAllocationListScreenState();
}

class _QuotaAllocationListScreenState extends State<QuotaAllocationListScreen> {
  final HuntingActivityRepository _repository = HuntingActivityRepository();
  final OrganisationRepository _organisationRepository = OrganisationRepository();
  final NotificationService _notificationService = Get.find<NotificationService>();
  
  final RxBool _isLoading = true.obs;
  final RxList<QuotaAllocation> _quotaAllocations = RxList<QuotaAllocation>([]);
  final RxInt _organisationId = 0.obs;
  final RxString _currentPeriod = DateTime.now().year.toString().obs;
  
  @override
  void initState() {
    super.initState();
    _loadData();
  }
  
  Future<void> _loadData() async {
    _isLoading.value = true;
    
    try {
      // Get selected organisation
      final organisation = await _organisationRepository.getSelectedOrganisation();
      if (organisation != null) {
        _organisationId.value = organisation.id;
        await _fetchQuotaAllocations();
      } else {
        _notificationService.showSnackBar(
          'No organisation selected. Please select an organisation from your profile.',
          type: SnackBarType.warning,
        );
      }
    } catch (e) {
      _notificationService.showSnackBar(
        'Failed to load quota allocations. Please try again.',
        type: SnackBarType.error,
      );
    } finally {
      _isLoading.value = false;
    }
  }
  
  Future<void> _fetchQuotaAllocations() async {
    try {
      final allocations = await _repository.getQuotaAllocations(
        _organisationId.value,
        period: _currentPeriod.value,
      );
      
      _quotaAllocations.value = allocations;
    } catch (e) {
      print('Error fetching quota allocations: $e');
      _notificationService.showSnackBar(
        'Failed to fetch quota allocations: ${e.toString()}',
        type: SnackBarType.error,
      );
    }
  }
  
  Future<void> _changePeriod(String period) async {
    _currentPeriod.value = period;
    _isLoading.value = true;
    await _fetchQuotaAllocations();
    _isLoading.value = false;
  }
  
  void _navigateToCreateScreen() async {
    final result = await Get.to(() => const QuotaAllocationCreateScreen());
    if (result != null) {
      // Refresh the list
      _loadData();
    }
  }
  
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Quota Allocations'),
        actions: [
          // Period selector
          Obx(() => DropdownButton<String>(
            value: _currentPeriod.value,
            onChanged: (value) {
              if (value != null) {
                _changePeriod(value);
              }
            },
            items: List.generate(5, (index) {
              final year = DateTime.now().year + index - 2;
              return DropdownMenuItem(
                value: year.toString(),
                child: Text(year.toString()),
              );
            }),
            underline: Container(),
            icon: const Icon(Icons.calendar_today),
            padding: const EdgeInsets.symmetric(horizontal: 16),
          )),
        ],
      ),
      body: Obx(() {
        if (_isLoading.value) {
          return const Center(child: CircularProgressIndicator());
        }
        
        if (_quotaAllocations.isEmpty) {
          return Center(
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Icon(
                  Icons.assignment_outlined,
                  size: 80,
                  color: Colors.grey.shade400,
                ),
                const SizedBox(height: 16),
                Text(
                  'No quota allocations found for ${_currentPeriod.value}',
                  style: TextStyle(
                    fontSize: 16,
                    color: Colors.grey.shade600,
                  ),
                ),
                const SizedBox(height: 24),
                ElevatedButton.icon(
                  onPressed: _navigateToCreateScreen,
                  icon: const Icon(Icons.add),
                  label: const Text('Create New Quota Allocation'),
                  style: ElevatedButton.styleFrom(
                    padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
                  ),
                ),
              ],
            ),
          );
        }
        
        return RefreshIndicator(
          onRefresh: _loadData,
          child: ListView.builder(
            padding: const EdgeInsets.all(16),
            itemCount: _quotaAllocations.length,
            itemBuilder: (context, index) {
              final allocation = _quotaAllocations[index];
              final speciesName = allocation.species?.name ?? 'Unknown Species';
              
              return Card(
                margin: const EdgeInsets.only(bottom: 16),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Padding(
                  padding: const EdgeInsets.all(16),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        children: [
                          Expanded(
                            child: Text(
                              speciesName,
                              style: const TextStyle(
                                fontSize: 18,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                          ),
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                            decoration: BoxDecoration(
                              color: Colors.blue.shade50,
                              borderRadius: BorderRadius.circular(20),
                              border: Border.all(color: Colors.blue.shade200),
                            ),
                            child: Text(
                              allocation.period,
                              style: TextStyle(
                                color: Colors.blue.shade700,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 16),
                      // Quota information
                      Row(
                        children: [
                          Expanded(
                            child: _buildQuotaCard(
                              'Hunting Quota',
                              allocation.huntingQuota.toString(),
                              Colors.green,
                            ),
                          ),
                          const SizedBox(width: 8),
                          Expanded(
                            child: _buildQuotaCard(
                              'PAC Quota',
                              allocation.rationalKillingQuota.toString(),
                              Colors.orange,
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 12),
                      // Balance information
                      if (allocation.quotaAllocationBalance != null)
                        Row(
                          children: [
                            Expanded(
                              child: _buildQuotaCard(
                                'Used',
                                allocation.quotaAllocationBalance!.offTake.toString(),
                                Colors.red,
                              ),
                            ),
                            const SizedBox(width: 8),
                            Expanded(
                              child: _buildQuotaCard(
                                'Remaining',
                                allocation.quotaAllocationBalance!.remaining.toString(),
                                Colors.blue,
                              ),
                            ),
                          ],
                        ),
                      const SizedBox(height: 12),
                      // Date range
                      Text(
                        'Valid: ${DateFormat('dd MMM yyyy').format(allocation.startDate)} - ${DateFormat('dd MMM yyyy').format(allocation.endDate)}',
                        style: TextStyle(
                          color: Colors.grey.shade600,
                          fontSize: 14,
                        ),
                      ),
                    ],
                  ),
                ),
              );
            },
          ),
        );
      }),
      floatingActionButton: FloatingActionButton(
        onPressed: _navigateToCreateScreen,
        child: const Icon(Icons.add),
      ),
    );
  }
  
  Widget _buildQuotaCard(String label, String value, MaterialColor color) {
    return Container(
      padding: const EdgeInsets.symmetric(vertical: 12, horizontal: 16),
      decoration: BoxDecoration(
        color: color.shade50,
        borderRadius: BorderRadius.circular(8),
        border: Border.all(color: color.shade200),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            label,
            style: TextStyle(
              color: color.shade700,
              fontSize: 12,
            ),
          ),
          const SizedBox(height: 4),
          Text(
            value,
            style: TextStyle(
              color: color.shade900,
              fontSize: 18,
              fontWeight: FontWeight.bold,
            ),
          ),
        ],
      ),
    );
  }
}