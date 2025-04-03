import 'package:flutter_local_notifications/flutter_local_notifications.dart';
import 'package:get/get.dart';
import 'package:resource_africa/utils/app_constants.dart';
import 'package:uuid/uuid.dart';

class NotificationService extends GetxService {
  final FlutterLocalNotificationsPlugin _notificationsPlugin = FlutterLocalNotificationsPlugin();
  final Uuid _uuid = const Uuid();
  
  Future<NotificationService> init() async {
    // Initialize notifications
    const AndroidInitializationSettings initializationSettingsAndroid =
        AndroidInitializationSettings('@mipmap/ic_launcher');
    
    const DarwinInitializationSettings initializationSettingsIOS =
        DarwinInitializationSettings(
      requestAlertPermission: true,
      requestBadgePermission: true,
      requestSoundPermission: true,
    );
    
    const InitializationSettings initializationSettings = InitializationSettings(
      android: initializationSettingsAndroid,
      iOS: initializationSettingsIOS,
    );
    
    await _notificationsPlugin.initialize(
      initializationSettings,
      onDidReceiveNotificationResponse: _onSelectNotification,
    );
    
    // Request permissions on iOS
    await _notificationsPlugin
        .resolvePlatformSpecificImplementation<
            IOSFlutterLocalNotificationsPlugin>()
        ?.requestPermissions(
          alert: true,
          badge: true,
          sound: true,
        );
    
    return this;
  }
  
  void _onSelectNotification(NotificationResponse response) {
    // Handle notification taps here
    final payload = response.payload;
    if (payload != null) {
      // Parse payload and navigate to appropriate screen
      _handleNotificationPayload(payload);
    }
  }
  
  void _handleNotificationPayload(String payload) {
    // Parse payload and navigate to the appropriate screen
    // Example:
    // final data = jsonDecode(payload);
    // if (data['type'] == 'incident') {
    //   Get.toNamed('/incidents/${data['id']}');
    // }
  }
  
  // Show a basic notification
  Future<void> showNotification({
    required String title,
    required String body,
    String? payload,
  }) async {
    const AndroidNotificationDetails androidDetails = AndroidNotificationDetails(
      'resource_africa_channel',
      'Resource Africa',
      channelDescription: 'Wildlife management notification channel',
      importance: Importance.high,
      priority: Priority.high,
      ticker: 'Resource Africa',
    );
    
    const DarwinNotificationDetails iosDetails = DarwinNotificationDetails(
      presentAlert: true,
      presentBadge: true,
      presentSound: true,
    );
    
    const NotificationDetails details = NotificationDetails(
      android: androidDetails,
      iOS: iosDetails,
    );
    
    await _notificationsPlugin.show(
      _generateNotificationId(),
      title,
      body,
      details,
      payload: payload,
    );
  }
  
  // Show a notification for a successful sync
  Future<void> showSyncSuccessNotification() async {
    await showNotification(
      title: 'Sync Completed',
      body: AppConstants.syncSuccess,
    );
  }
  
  // Show a notification for a new incident
  Future<void> showNewIncidentNotification(String incidentTitle) async {
    await showNotification(
      title: 'New Incident Reported',
      body: 'A new incident has been reported: $incidentTitle',
      payload: '{"type": "incident"}',
    );
  }
  
  // Helper to generate a unique notification ID
  int _generateNotificationId() {
    final uuid = _uuid.v4();
    return uuid.hashCode;
  }
}