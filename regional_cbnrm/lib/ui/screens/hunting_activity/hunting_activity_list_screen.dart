import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:regional_cbnrm/utils/app_routes.dart';

class HuntingActivityListScreen extends StatelessWidget {
  const HuntingActivityListScreen({Key? key}) : super(key: key);

  void _navigateToCreateScreen(BuildContext context) {
    Get.toNamed(AppRoutes.createHuntingActivity);
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Hunting Activities'),
      ),
      body: Center(
        child: Padding(
          padding: const EdgeInsets.all(24.0),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(
                Icons.sports,
                size: 80,
                color: Colors.grey[400],
              ),
              const SizedBox(height: 24),
              const Text(
                'No Hunting Activities',
                style: TextStyle(
                  fontSize: 18,
                  fontWeight: FontWeight.bold,
                ),
              ),
              const SizedBox(height: 12),
              const Text(
                "You haven't recorded any hunting activities yet.",
                textAlign: TextAlign.center,
                style: TextStyle(
                  fontSize: 14,
                  color: Colors.grey,
                ),
              ),
              const SizedBox(height: 24),
              ElevatedButton.icon(
                onPressed: () => _navigateToCreateScreen(context),
                icon: const Icon(Icons.add),
                label: const Text('Add Hunting Activity'),
                style: ElevatedButton.styleFrom(
                  backgroundColor: Colors.green[800],
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(8),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: () => _navigateToCreateScreen(context),
        backgroundColor: Colors.green[800],
        child: const Icon(Icons.add),
      ),
    );
  }
}
