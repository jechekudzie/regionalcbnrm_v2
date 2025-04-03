import 'dart:convert';
import 'package:get/get.dart';
import 'package:resource_africa/core/api_service.dart';
import 'package:resource_africa/core/database_helper.dart';
import 'package:resource_africa/services/connectivity_service.dart';
import 'package:resource_africa/services/notification_service.dart';

class SyncService extends GetxService {
  final ApiService _apiService = ApiService();
  final DatabaseHelper _dbHelper = DatabaseHelper();
  final ConnectivityService _connectivityService = Get.find<ConnectivityService>();
  final NotificationService _notificationService = Get.find<NotificationService>();
  
  final RxBool isSyncing = false.obs;
  final RxInt pendingSyncCount = 0.obs;
  
  Future<SyncService> init() async {
    await _updatePendingSyncCount();
    return this;
  }
  
  Future<void> _updatePendingSyncCount() async {
    final pendingItems = await _dbHelper.getPendingSyncItems();
    pendingSyncCount.value = pendingItems.length;
  }
  
  Future<bool> syncData() async {
    if (!_connectivityService.isConnected.value) {
      return false;
    }
    
    if (isSyncing.value) {
      return false;
    }
    
    isSyncing.value = true;
    bool success = true;
    
    try {
      final pendingItems = await _dbHelper.getPendingSyncItems();
      
      for (final item in pendingItems) {
        final tableName = item['table_name'];
        final recordId = item['record_id'];
        final action = item['action'];
        final data = item['data'];
        
        bool itemSuccess = await _syncItem(item['id'], tableName, recordId, action, data);
        if (!itemSuccess) {
          success = false;
        }
      }
      
      if (success) {
        await _notificationService.showSyncSuccessNotification();
      }
      
      await _updatePendingSyncCount();
    } catch (e) {
      success = false;
    } finally {
      isSyncing.value = false;
    }
    
    return success;
  }
  
  Future<bool> _syncItem(int id, String tableName, int recordId, String action, String data) async {
    try {
      switch (tableName) {
        case 'wildlife_conflict_incidents':
          return await _syncWildlifeConflictIncident(id, recordId, action, data);
        case 'wildlife_conflict_outcomes':
          return await _syncWildlifeConflictOutcome(id, recordId, action, data);
        case 'problem_animal_controls':
          return await _syncProblemAnimalControl(id, recordId, action, data);
        case 'poaching_incidents':
          return await _syncPoachingIncident(id, recordId, action, data);
        case 'poachers':
          return await _syncPoacher(id, recordId, action, data);
        case 'hunting_activities':
          return await _syncHuntingActivity(id, recordId, action, data);
        default:
          return false;
      }
    } catch (e) {
      // Update sync item attempt count
      await _dbHelper.updateSyncItemStatus(id, 'failed');
      return false;
    }
  }
  
  Future<bool> _syncWildlifeConflictIncident(int id, int recordId, String action, String data) async {
    try {
      final decodedData = jsonDecode(data);
      
      if (action == 'create') {
        final response = await _apiService.post('/wildlife-conflicts', data: decodedData);
        
        if (response['status'] == 'success') {
          final remoteId = response['data']['incident']['id'];
          
          // Update local record with remote ID and sync status
          await _dbHelper.update(
            'wildlife_conflict_incidents',
            {
              'sync_status': 'synced',
              'remote_id': remoteId,
            },
            where: 'id = ?',
            whereArgs: [recordId],
          );
          
          // Update sync item status
          await _dbHelper.updateSyncItemStatus(id, 'completed');
          return true;
        }
      } else if (action == 'update') {
        // Get the remote ID for this record
        final recordData = await _dbHelper.query(
          'wildlife_conflict_incidents',
          where: 'id = ?',
          whereArgs: [recordId],
        );
        
        if (recordData.isEmpty) {
          await _dbHelper.updateSyncItemStatus(id, 'failed');
          return false;
        }
        
        final remoteId = recordData.first['remote_id'];
        
        if (remoteId == null) {
          await _dbHelper.updateSyncItemStatus(id, 'failed');
          return false;
        }
        
        final response = await _apiService.put(
          '/wildlife-conflicts/$remoteId',
          data: decodedData,
        );
        
        if (response['status'] == 'success') {
          // Update local record with sync status
          await _dbHelper.update(
            'wildlife_conflict_incidents',
            {'sync_status': 'synced'},
            where: 'id = ?',
            whereArgs: [recordId],
          );
          
          // Update sync item status
          await _dbHelper.updateSyncItemStatus(id, 'completed');
          return true;
        }
      }
      
      await _dbHelper.updateSyncItemStatus(id, 'failed');
      return false;
    } catch (e) {
      await _dbHelper.updateSyncItemStatus(id, 'failed');
      return false;
    }
  }
  
  Future<bool> _syncWildlifeConflictOutcome(int id, int recordId, String action, String data) async {
    try {
      final decodedData = jsonDecode(data);
      
      if (action == 'create') {
        // Get the incident ID
        final outcomeData = await _dbHelper.query(
          'wildlife_conflict_outcomes',
          where: 'id = ?',
          whereArgs: [recordId],
        );
        
        if (outcomeData.isEmpty) {
          await _dbHelper.updateSyncItemStatus(id, 'failed');
          return false;
        }
        
        final incidentId = outcomeData.first['incident_id'];
        
        // Get the remote incident ID
        final incidentData = await _dbHelper.query(
          'wildlife_conflict_incidents',
          where: 'id = ?',
          whereArgs: [incidentId],
        );
        
        if (incidentData.isEmpty) {
          await _dbHelper.updateSyncItemStatus(id, 'failed');
          return false;
        }
        
        final remoteIncidentId = incidentData.first['remote_id'] ?? incidentData.first['id'];
        
        final response = await _apiService.post(
          '/wildlife-conflicts/$remoteIncidentId/outcomes',
          data: decodedData,
        );
        
        if (response['status'] == 'success') {
          final remoteId = response['data']['outcome']['id'];
          
          // Update local record with remote ID and sync status
          await _dbHelper.update(
            'wildlife_conflict_outcomes',
            {
              'sync_status': 'synced',
              'remote_id': remoteId,
            },
            where: 'id = ?',
            whereArgs: [recordId],
          );
          
          // Update sync item status
          await _dbHelper.updateSyncItemStatus(id, 'completed');
          return true;
        }
      }
      
      await _dbHelper.updateSyncItemStatus(id, 'failed');
      return false;
    } catch (e) {
      await _dbHelper.updateSyncItemStatus(id, 'failed');
      return false;
    }
  }
  
  Future<bool> _syncProblemAnimalControl(int id, int recordId, String action, String data) async {
    try {
      final decodedData = jsonDecode(data);
      
      if (action == 'create') {
        final response = await _apiService.post(
          '/problem-animal-controls',
          data: decodedData,
        );
        
        if (response['status'] == 'success') {
          final remoteId = response['data']['record']['id'];
          
          // Update local record with remote ID and sync status
          await _dbHelper.update(
            'problem_animal_controls',
            {
              'sync_status': 'synced',
              'remote_id': remoteId,
            },
            where: 'id = ?',
            whereArgs: [recordId],
          );
          
          // Update sync item status
          await _dbHelper.updateSyncItemStatus(id, 'completed');
          return true;
        }
      } else if (action == 'update') {
        // Get the remote ID for this record
        final recordData = await _dbHelper.query(
          'problem_animal_controls',
          where: 'id = ?',
          whereArgs: [recordId],
        );
        
        if (recordData.isEmpty) {
          await _dbHelper.updateSyncItemStatus(id, 'failed');
          return false;
        }
        
        final remoteId = recordData.first['remote_id'];
        
        if (remoteId == null) {
          await _dbHelper.updateSyncItemStatus(id, 'failed');
          return false;
        }
        
        final response = await _apiService.put(
          '/problem-animal-controls/$remoteId',
          data: decodedData,
        );
        
        if (response['status'] == 'success') {
          // Update local record with sync status
          await _dbHelper.update(
            'problem_animal_controls',
            {'sync_status': 'synced'},
            where: 'id = ?',
            whereArgs: [recordId],
          );
          
          // Update sync item status
          await _dbHelper.updateSyncItemStatus(id, 'completed');
          return true;
        }
      }
      
      await _dbHelper.updateSyncItemStatus(id, 'failed');
      return false;
    } catch (e) {
      await _dbHelper.updateSyncItemStatus(id, 'failed');
      return false;
    }
  }
  
  Future<bool> _syncPoachingIncident(int id, int recordId, String action, String data) async {
    try {
      final decodedData = jsonDecode(data);
      
      if (action == 'create') {
        final response = await _apiService.post(
          '/poaching-incidents',
          data: decodedData,
        );
        
        if (response['status'] == 'success') {
          final remoteId = response['data']['incident']['id'];
          
          // Update local record with remote ID and sync status
          await _dbHelper.update(
            'poaching_incidents',
            {
              'sync_status': 'synced',
              'remote_id': remoteId,
            },
            where: 'id = ?',
            whereArgs: [recordId],
          );
          
          // Update sync item status
          await _dbHelper.updateSyncItemStatus(id, 'completed');
          return true;
        }
      } else if (action == 'update') {
        // Get the remote ID for this record
        final recordData = await _dbHelper.query(
          'poaching_incidents',
          where: 'id = ?',
          whereArgs: [recordId],
        );
        
        if (recordData.isEmpty) {
          await _dbHelper.updateSyncItemStatus(id, 'failed');
          return false;
        }
        
        final remoteId = recordData.first['remote_id'];
        
        if (remoteId == null) {
          await _dbHelper.updateSyncItemStatus(id, 'failed');
          return false;
        }
        
        final response = await _apiService.put(
          '/poaching-incidents/$remoteId',
          data: decodedData,
        );
        
        if (response['status'] == 'success') {
          // Update local record with sync status
          await _dbHelper.update(
            'poaching_incidents',
            {'sync_status': 'synced'},
            where: 'id = ?',
            whereArgs: [recordId],
          );
          
          // Update sync item status
          await _dbHelper.updateSyncItemStatus(id, 'completed');
          return true;
        }
      }
      
      await _dbHelper.updateSyncItemStatus(id, 'failed');
      return false;
    } catch (e) {
      await _dbHelper.updateSyncItemStatus(id, 'failed');
      return false;
    }
  }
  
  Future<bool> _syncPoacher(int id, int recordId, String action, String data) async {
    try {
      final decodedData = jsonDecode(data);
      
      if (action == 'create') {
        // Get the incident ID
        final poacherData = await _dbHelper.query(
          'poachers',
          where: 'id = ?',
          whereArgs: [recordId],
        );
        
        if (poacherData.isEmpty) {
          await _dbHelper.updateSyncItemStatus(id, 'failed');
          return false;
        }
        
        final incidentId = poacherData.first['poaching_incident_id'];
        
        // Get the remote incident ID
        final incidentData = await _dbHelper.query(
          'poaching_incidents',
          where: 'id = ?',
          whereArgs: [incidentId],
        );
        
        if (incidentData.isEmpty) {
          await _dbHelper.updateSyncItemStatus(id, 'failed');
          return false;
        }
        
        final remoteIncidentId = incidentData.first['remote_id'] ?? incidentData.first['id'];
        
        final response = await _apiService.post(
          '/poaching-incidents/$remoteIncidentId/poachers',
          data: decodedData,
        );
        
        if (response['status'] == 'success') {
          final remoteId = response['data']['poacher']['id'];
          
          // Update local record with remote ID and sync status
          await _dbHelper.update(
            'poachers',
            {
              'sync_status': 'synced',
              'remote_id': remoteId,
            },
            where: 'id = ?',
            whereArgs: [recordId],
          );
          
          // Update sync item status
          await _dbHelper.updateSyncItemStatus(id, 'completed');
          return true;
        }
      }
      
      await _dbHelper.updateSyncItemStatus(id, 'failed');
      return false;
    } catch (e) {
      await _dbHelper.updateSyncItemStatus(id, 'failed');
      return false;
    }
  }
  
  Future<bool> _syncHuntingActivity(int id, int recordId, String action, String data) async {
    try {
      final decodedData = jsonDecode(data);
      
      if (action == 'create') {
        final response = await _apiService.post(
          '/hunting-activities',
          data: decodedData,
        );
        
        if (response['status'] == 'success') {
          final remoteId = response['data']['activity']['id'];
          
          // Update local record with remote ID and sync status
          await _dbHelper.update(
            'hunting_activities',
            {
              'sync_status': 'synced',
              'remote_id': remoteId,
            },
            where: 'id = ?',
            whereArgs: [recordId],
          );
          
          // Update sync item status
          await _dbHelper.updateSyncItemStatus(id, 'completed');
          return true;
        }
      }
      
      await _dbHelper.updateSyncItemStatus(id, 'failed');
      return false;
    } catch (e) {
      await _dbHelper.updateSyncItemStatus(id, 'failed');
      return false;
    }
  }
}