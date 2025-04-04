import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:regional_cbnrm/repositories/organisation_repository.dart';
import 'package:regional_cbnrm/services/auth_service.dart';
import 'package:regional_cbnrm/services/connectivity_service.dart';
import 'package:regional_cbnrm/services/error_handler_service.dart';
import 'package:regional_cbnrm/services/notification_service.dart';
import 'package:regional_cbnrm/ui/screens/auth/login_screen.dart';
import 'package:regional_cbnrm/ui/screens/dashboard/dashboard_screen.dart';
import 'package:regional_cbnrm/ui/screens/debug/debug_settings_screen.dart';
import 'package:regional_cbnrm/ui/screens/poaching/poaching_add_poacher_screen.dart';
import 'package:regional_cbnrm/ui/screens/poaching/poaching_incident_create_screen.dart';
import 'package:regional_cbnrm/ui/screens/poaching/poaching_incident_details_screen.dart';
import 'package:regional_cbnrm/ui/screens/poaching/poaching_incident_list_screen.dart';
import 'package:regional_cbnrm/ui/screens/problem_animal_control/problem_animal_control_create_screen.dart';
import 'package:regional_cbnrm/ui/screens/problem_animal_control/problem_animal_control_details_screen.dart';
import 'package:regional_cbnrm/ui/screens/problem_animal_control/problem_animal_control_list_screen.dart';
import 'package:regional_cbnrm/ui/screens/splash_screen.dart';
import 'package:regional_cbnrm/ui/screens/wildlife_conflict/wildlife_conflict_add_outcome_screen.dart';
import 'package:regional_cbnrm/ui/screens/wildlife_conflict/wildlife_conflict_create_screen.dart';
import 'package:regional_cbnrm/ui/screens/wildlife_conflict/wildlife_conflict_details_screen.dart';
import 'package:regional_cbnrm/ui/screens/wildlife_conflict/wildlife_conflict_list_screen.dart';
import 'package:regional_cbnrm/ui/theme/app_theme.dart';
import 'package:regional_cbnrm/utils/app_constants.dart';
import 'package:regional_cbnrm/utils/app_routes.dart';
import 'package:regional_cbnrm/utils/logger.dart';

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

    runApp(const RegionalCbnrmApp());
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

    // Register repositories as singletons
    Get.put(OrganisationRepository(), permanent: true);

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

class RegionalCbnrmApp extends StatelessWidget {
  const RegionalCbnrmApp({super.key});

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
        // Core routes
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
          name: AppRoutes.profile,
          page: () => const DashboardScreen(), // Placeholder - needs to be implemented
        ),
        GetPage(
          name: AppRoutes.syncData,
          page: () => const DashboardScreen(), // Placeholder - needs to be implemented
        ),

        // Wildlife conflict routes
        GetPage(
          name: AppRoutes.wildlifeConflicts,
          page: () => const WildlifeConflictListScreen(),
        ),
        GetPage(
          name: AppRoutes.wildlifeConflictDetails,
          page: () => WildlifeConflictDetailsScreen(),
        ),
        GetPage(
          name: AppRoutes.createWildlifeConflict,
          page: () => const WildlifeConflictCreateScreen(),
        ),
        GetPage(
          name: AppRoutes.addConflictOutcome,
          page: () => const WildlifeConflictAddOutcomeScreen(),
        ),

        // Problem animal control routes
        GetPage(
          name: AppRoutes.problemAnimalControls,
          page: () => const ProblemAnimalControlListScreen(),
        ),
        GetPage(
          name: AppRoutes.problemAnimalControlDetails,
          page: () => const ProblemAnimalControlDetailsScreen(),
        ),
        GetPage(
          name: AppRoutes.createProblemAnimalControl,
          page: () => const ProblemAnimalControlCreateScreen(),
        ),
        // Poaching routes
        GetPage(
          name: AppRoutes.poachingIncidents,
          page: () => const PoachingIncidentListScreen(),
        ),
        GetPage(
          name: AppRoutes.poachingIncidentDetails,
          page: () => const PoachingIncidentDetailsScreen(),
        ),
        GetPage(
          name: AppRoutes.createPoachingIncident,
          page: () => const PoachingIncidentCreateScreen(),
        ),
        GetPage(
          name: AppRoutes.poachingAddPoacher,
          page: () => const PoachingAddPoacherScreen(),
        ),

        // Hunting routes (placeholders - need to be implemented)
        GetPage(
          name: AppRoutes.huntingActivities,
          page: () => const DashboardScreen(), // Placeholder
        ),
        GetPage(
          name: AppRoutes.createHuntingActivity,
          page: () => const DashboardScreen(), // Placeholder
        ),
        GetPage(
          name: AppRoutes.huntingConcessions,
          page: () => const DashboardScreen(), // Placeholder
        ),
        GetPage(
          name: AppRoutes.quotaAllocations,
          page: () => const DashboardScreen(), // Placeholder
        ),

        // Debug routes
        GetPage(
          name: AppRoutes.debugSettings,
          page: () => const DebugSettingsScreen(),
        ),
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
