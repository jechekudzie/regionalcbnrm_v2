import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:regional_cbnrm/core/database_helper.dart';
import 'package:regional_cbnrm/services/auth_service.dart';
import 'package:regional_cbnrm/utils/app_constants.dart';
import 'package:regional_cbnrm/utils/app_routes.dart';
import 'package:regional_cbnrm/utils/logger.dart';

class SplashScreen extends StatefulWidget {
  const SplashScreen({super.key});

  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen> with SingleTickerProviderStateMixin {
  final AuthService _authService = Get.find<AuthService>();
  final AppLogger _logger = AppLogger();
  late AnimationController _animationController;
  late Animation<double> _fadeAnimation;
  
  // Debug counter for tap to enter debug mode
  int _debugTapCount = 0;
  final int _requiredTapsForDebug = 7;
  DateTime? _lastTapTime;
  
  @override
  void initState() {
    super.initState();
    
    // Set up animations
    _animationController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1500),
    );
    
    _fadeAnimation = Tween<double>(begin: 0.0, end: 1.0).animate(
      CurvedAnimation(
        parent: _animationController,
        curve: Curves.easeIn,
      ),
    );
    
    _animationController.forward();
    
    // Start navigation timer after animations
    _navigateToNextScreen();
  }
  
  @override
  void dispose() {
    _animationController.dispose();
    super.dispose();
  }
  
  Future<void> _navigateToNextScreen() async {
    _logger.i('Splash screen: Preparing to navigate to next screen');
    
    try {
      // Check database initialization
      final dbHelper = DatabaseHelper();
      final dbExists = await dbHelper.databaseExists();
      _logger.d('Splash screen: Database exists: $dbExists');
      
      if (!dbExists) {
        _logger.d('Splash screen: Initializing database for first time');
        await dbHelper.database; // This will create the database
      }
      
      // Simulate splash screen delay and check authentication
      await Future.delayed(const Duration(seconds: 2));
      
      final bool isAuthenticated = _authService.isAuthenticated;
      _logger.d('Splash screen: User is authenticated: $isAuthenticated');
      
      if (isAuthenticated) {
        final success = await _authService.refreshUser();
        if (success) {
          _logger.d('Splash screen: User refreshed: ${_authService.currentUser?.name}');
        } else {
          _logger.w('Splash screen: User refresh failed');
        }
        Get.offAllNamed(AppRoutes.dashboard);
      } else {
        _logger.d('Splash screen: Navigating to login screen');
        Get.offAllNamed(AppRoutes.login);
      }
    } catch (e, stackTrace) {
      _logger.e('Splash screen: Error during initialization', e, stackTrace);
      // Show error message or retry option
      _showErrorDialog(e.toString());
    }
  }
  
  void _showErrorDialog(String errorMessage) {
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) => AlertDialog(
        title: const Text('Initialization Error'),
        content: SingleChildScrollView(
          child: Text('Could not initialize the app: $errorMessage'),
        ),
        actions: [
          TextButton(
            onPressed: () {
              Navigator.of(context).pop();
              _navigateToNextScreen(); // Retry
            },
            child: const Text('Retry'),
          ),
        ],
      ),
    );
  }
  
  void _handleLogoTap() {
    final now = DateTime.now();
    
    // Reset counter if more than 2 seconds since last tap
    if (_lastTapTime != null && now.difference(_lastTapTime!).inSeconds > 2) {
      _debugTapCount = 0;
    }
    
    _lastTapTime = now;
    _debugTapCount++;
    
    if (_debugTapCount >= _requiredTapsForDebug) {
      _debugTapCount = 0;
      _showDebugModeDialog();
    }
  }
  
  void _showDebugModeDialog() {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Debug Mode'),
        content: const Text('Would you like to enter debug mode?'),
        actions: [
          TextButton(
            onPressed: () {
              Navigator.of(context).pop();
            },
            child: const Text('Cancel'),
          ),
          TextButton(
            onPressed: () {
              Navigator.of(context).pop();
              
              // Set debug flag in app settings
              // This will be used to show debug console in the app
              Get.toNamed(AppRoutes.debugSettings);
            },
            child: const Text('Enter Debug Mode'),
          ),
        ],
      ),
    );
  }
  
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: GestureDetector(
        onTap: _handleLogoTap,
        child: Container(
          width: double.infinity,
          decoration: const BoxDecoration(
            gradient: LinearGradient(
              begin: Alignment.topCenter,
              end: Alignment.bottomCenter,
              colors: [Color(0xFF006241), Color(0xFF004C33)],
            ),
          ),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              // App logo with fade-in animation
              FadeTransition(
                opacity: _fadeAnimation,
                child: Image.asset(
                  AppConstants.appLogoPath,
                  width: 180,
                  height: 180,
                  errorBuilder: (context, error, stackTrace) {
                    _logger.w('Splash screen: Failed to load logo', error, stackTrace);
                    return Container(
                      width: 180,
                      height: 180,
                      decoration: BoxDecoration(
                        color: Colors.white,
                        borderRadius: BorderRadius.circular(20),
                      ),
                      child: const Center(
                        child: Text(
                          'RA',
                          style: TextStyle(
                            color: Color(0xFF006241),
                            fontSize: 64,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ),
                    );
                  },
                ),
              ),
              const SizedBox(height: 24),
              // App name with fade-in animation
              FadeTransition(
                opacity: _fadeAnimation,
                child: const Text(
                  AppConstants.appName,
                  style: TextStyle(
                    color: Colors.white,
                    fontSize: 28,
                    fontWeight: FontWeight.bold,
                  ),
                ),
              ),
              const SizedBox(height: 12),
              // Tagline with fade-in animation
              FadeTransition(
                opacity: _fadeAnimation,
                child: const Text(
                  'Wildlife Management System',
                  style: TextStyle(
                    color: Colors.white70,
                    fontSize: 16,
                  ),
                ),
              ),
              const SizedBox(height: 50),
              // Loading indicator
              const CircularProgressIndicator(
                valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
              ),
              const SizedBox(height: 24),
              // Version info
              FadeTransition(
                opacity: _fadeAnimation,
                child: const Text(
                  'Version ${AppConstants.appVersion}',
                  style: TextStyle(
                    color: Colors.white54,
                    fontSize: 12,
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}