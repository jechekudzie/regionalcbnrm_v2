import 'package:flutter/material.dart';

class SyncStatusIndicator extends StatelessWidget {
  final String? syncStatus;

  const SyncStatusIndicator({
    super.key,
    required this.syncStatus,
  });

  @override
  Widget build(BuildContext context) {
    if (syncStatus == null) {
      return const SizedBox.shrink();
    }
    
    Color color;
    IconData icon;
    String tooltip;
    
    switch (syncStatus) {
      case 'synced':
        color = Colors.green;
        icon = Icons.check_circle;
        tooltip = 'Synced';
        break;
      case 'pending':
        color = Colors.orange;
        icon = Icons.sync_problem;
        tooltip = 'Pending sync';
        break;
      case 'failed':
        color = Colors.red;
        icon = Icons.error;
        tooltip = 'Sync failed';
        break;
      default:
        color = Colors.grey;
        icon = Icons.help;
        tooltip = 'Unknown status';
    }
    
    return Tooltip(
      message: tooltip,
      child: Icon(
        icon,
        color: color,
        size: 16,
      ),
    );
  }
}