import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:resource_africa/services/auth_service.dart';
import 'package:resource_africa/services/connectivity_service.dart';
import 'package:resource_africa/services/error_handler_service.dart';
import 'package:resource_africa/services/notification_service.dart';
import 'package:resource_africa/ui/screens/auth/login_screen.dart';
import 'package:resource_africa/ui/screens/dashboard/dashboard_screen.dart';
import 'package:resource_africa/ui/screens/debug/debug_settings_screen.dart';
import 'package:resource_africa/ui/screens/splash_screen.dart';
import 'package:resource_africa/ui/theme/app_theme.dart';
import 'package:resource_africa/utils/app_constants.dart';
import 'package:resource_africa/utils/app_routes.dart';
import 'package:resource_africa/utils/logger.dart';

void main() async {
  // Add error handling for Flutter framework errors
  FlutterError.onError = (FlutterErrorDetails details) {
    FlutterError.presentError(details);
    AppLogger().e('Flutter Error', details.exception, details.stack);
  };

  // Handle errors that occur during app initialization
  try {
    WidgetsFlutterBinding.ensureInitialized();
    
    // Initialize services
    await initServices();
    
    // Set preferred orientations
    await SystemChrome.setPreferredOrientations([
      DeviceOrientation.portraitUp,
      DeviceOrientation.portraitDown,
    ]);
    
    runApp(const ResourceAfricaApp());
  } catch (e, stackTrace) {
    AppLogger().e('Error during app initialization', e, stackTrace);
    // Show error UI or gracefully handle initialization failure
    runApp(const AppErrorScreen());
  }
}

// Initialize all Get services
Future<void> initServices() async {
  final logger = AppLogger();
  
  try {
    logger.i('Initializing services');
    
    // Register error handler early to handle initialization errors
    await Get.putAsync(() => ErrorHandlerService().init());
    
    // Register other services
    await Get.putAsync(() => ConnectivityService().init());
    await Get.putAsync(() => NotificationService().init());
    await Get.putAsync(() => AuthService().init());
    
    logger.i('All services initialized');
  } catch (e, stackTrace) {
    logger.e('Error initializing services', e, stackTrace);
    rethrow; // Let the main method handle this error
  }
}

// Extension method for GetxService to properly handle initialization
extension ServiceInit on GetxService {
  Future<GetxService> init() async {
    return this;
  }
}

class ResourceAfricaApp extends StatelessWidget {
  const ResourceAfricaApp({super.key});

  @override
  Widget build(BuildContext context) {
    return GetMaterialApp(
      title: AppConstants.appName,
      debugShowCheckedModeBanner: false,
      theme: AppTheme.lightTheme,
      darkTheme: AppTheme.darkTheme,
      themeMode: ThemeMode.light,
      initialRoute: AppRoutes.splash,
      defaultTransition: Transition.fade,
      getPages: [
        GetPage(
          name: AppRoutes.splash,
          page: () => const SplashScreen(),
        ),
        GetPage(
          name: AppRoutes.login,
          page: () => const LoginScreen(),
        ),
        GetPage(
          name: AppRoutes.dashboard,
          page: () => const DashboardScreen(),
        ),
        GetPage(
          name: AppRoutes.debugSettings,
          page: () => const DebugSettingsScreen(),
        ),
        // Other routes will be defined here
      ],
    );
  }
}

// Simple error screen shown if app initialization fails completely
class AppErrorScreen extends StatelessWidget {
  const AppErrorScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      debugShowCheckedModeBanner: false,
      home: Scaffold(
        body: Center(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Icon(
                Icons.error_outline,
                color: Colors.red,
                size: 60,
              ),
              const SizedBox(height: 16),
              const Text(
                'App Initialization Failed',
                style: TextStyle(
                  fontSize: 20,
                  fontWeight: FontWeight.bold,
                ),
              ),
              const SizedBox(height: 8),
              const Text(
                'Please restart the app. If the problem persists, contact support.',
                textAlign: TextAlign.center,
                style: TextStyle(fontSize: 16),
              ),
              const SizedBox(height: 24),
              ElevatedButton(
                onPressed: () {
                  // Attempt to restart the app
                  SystemNavigator.pop();
                },
                child: const Text('Exit App'),
              ),
            ],
          ),
        ),
      ),
    );
  }
}