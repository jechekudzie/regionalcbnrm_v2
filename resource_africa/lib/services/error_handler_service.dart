import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:resource_africa/core/app_exceptions.dart';
import 'package:resource_africa/services/auth_service.dart';
import 'package:resource_africa/utils/app_constants.dart';
import 'package:resource_africa/utils/app_routes.dart';
import 'package:resource_africa/utils/logger.dart';

class ErrorHandlerService extends GetxService {
  final AppLogger _logger = AppLogger();

  // Handle API exceptions
  Future<void> handleApiError(dynamic error, {BuildContext? context}) async {
    _logger.e('API Error', error);

    String errorMessage = 'An unexpected error occurred';

    if (error is UnauthorizedException) {
      errorMessage = error.message;
      // Handle unauthorized access - log out user and redirect to login
      await _handleUnauthorized();
    } else if (error is BadRequestException) {
      errorMessage = error.message;
    } else if (error is ServerException) {
      errorMessage = AppConstants.serverError;
    } else if (error is TimeoutException) {
      errorMessage = AppConstants.timeoutError;
    } else if (error is NoInternetException) {
      errorMessage = AppConstants.connectionError;
    } else if (error is AppException) {
      errorMessage = error.message;
    }

    // Show error message using GetX snackbar
    Get.snackbar(
      'Error',
      errorMessage,
      snackPosition: SnackPosition.BOTTOM,
      backgroundColor: Colors.red.shade800,
      colorText: Colors.white,
      duration: const Duration(seconds: 4),
      margin: const EdgeInsets.all(8),
    );
  }

  // Handle database errors
  Future<void> handleDatabaseError(dynamic error, {BuildContext? context}) async {
    _logger.e('Database Error', error);
    
    String errorMessage = 'A database error occurred. Please try again.';

    Get.snackbar(
      'Database Error',
      errorMessage,
      snackPosition: SnackPosition.BOTTOM,
      backgroundColor: Colors.amber.shade800,
      colorText: Colors.white,
      duration: const Duration(seconds: 4),
      margin: const EdgeInsets.all(8),
    );
  }

  // Handle validation errors
  void handleValidationError(Map<String, dynamic> errors, {BuildContext? context}) {
    _logger.w('Validation Error', errors);
    
    // Format error messages
    final List<String> errorList = [];
    errors.forEach((key, value) {
      if (value is List) {
        errorList.addAll(value.map((e) => e.toString()));
      } else {
        errorList.add(value.toString());
      }
    });
    
    final String errorMessage = errorList.join('\n');

    Get.snackbar(
      'Validation Error',
      errorMessage,
      snackPosition: SnackPosition.BOTTOM,
      backgroundColor: Colors.orange.shade800,
      colorText: Colors.white,
      duration: const Duration(seconds: 4),
      margin: const EdgeInsets.all(8),
    );
  }

  // Handle unauthorized access - log out user and redirect to login
  Future<void> _handleUnauthorized() async {
    _logger.w('Handling unauthorized access');
    
    try {
      final AuthService authService = Get.find<AuthService>();
      await authService.logout();
      Get.offAllNamed(AppRoutes.login);
    } catch (e) {
      _logger.e('Error handling unauthorized access', e);
      // Ensure navigation to login happens even if logout fails
      Get.offAllNamed(AppRoutes.login);
    }
  }

  // Show success message
  void showSuccess(String message) {
    Get.snackbar(
      'Success',
      message,
      snackPosition: SnackPosition.BOTTOM,
      backgroundColor: Colors.green.shade800,
      colorText: Colors.white,
      duration: const Duration(seconds: 3),
      margin: const EdgeInsets.all(8),
    );
  }
}