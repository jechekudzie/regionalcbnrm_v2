import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:regional_cbnrm/models/hunting_model.dart';
import 'package:regional_cbnrm/repositories/hunting_activity_repository.dart';
import 'package:regional_cbnrm/services/notification_service.dart';

class HuntingActivityListScreen extends StatefulWidget {
  const HuntingActivityListScreen({Key? key}) : super(key: key);

  @override
  State<HuntingActivityListScreen> createState() => _HuntingActivityListScreenState();
}

class _HuntingActivityListScreenState extends State<HuntingActivityListScreen> {
  final HuntingActivityRepository _repository = HuntingActivityRepository();
  final NotificationService _notificationService = Get.find<NotificationService>();

  final RxBool _isLoading = false.obs;
  final RxList<HuntingActivity> _activities = <HuntingActivity>[].obs;

  @override
  void initState() {
    super.initState();
    _fetchActivities();
  }

  Future<void> _fetchActivities() async {
    _isLoading.value = true;
    try {
      const organisationId = 1; // TODO: Replace with selected org ID
      final activities = await _repository.getHuntingActivities(organisationId);
      _activities.assignAll(activities);
    } catch (e) {
      _notificationService.showSnackBar('Failed to load hunting activities', type: SnackBarType.error);
    } finally {
      _isLoading.value = false;
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Hunting Activities'),
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: _fetchActivities,
          ),
          IconButton(
            icon: const Icon(Icons.add),
            onPressed: () {
              Get.toNamed('/create-hunting-activity')?.then((_) => _fetchActivities());
            },
          ),
        ],
      ),
      body: Obx(() {
        if (_isLoading.value) {
          return const Center(child: CircularProgressIndicator());
        }
        if (_activities.isEmpty) {
          return const Center(child: Text('No hunting activities found.'));
        }
        return ListView.builder(
          itemCount: _activities.length,
          itemBuilder: (context, index) {
            final activity = _activities[index];
            return ListTile(
              title: Text(activity.clientName ?? 'No Client'),
              subtitle: Text('${activity.startDate} - ${activity.endDate}'),
              onTap: () {
                // TODO: Navigate to details screen
              },
            );
          },
        );
      }),
    );
  }
}
