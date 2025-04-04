import 'package:logger/logger.dart';
import 'package:resource_africa/utils/app_constants.dart';

class AppLogger {
  static final AppLogger _instance = AppLogger._internal();
  factory AppLogger() => _instance;

  late Logger _logger;

  AppLogger._internal() {
    _logger = Logger(
      printer: PrettyPrinter(
        methodCount: 2,
        errorMethodCount: 8,
        lineLength: 120,
        colors: true,
        printEmojis: true,
        printTime: true,
      ),
      level: AppConstants.isProduction ? Level.warning : Level.verbose,
    );
  }

  void v(dynamic message, [dynamic error, StackTrace? stackTrace]) {
    if (error != null || stackTrace != null) {
      _logger.v('$message\nError: $error', error: error, stackTrace: stackTrace);
    } else {
      _logger.v(message);
    }
  }

  void d(dynamic message, [dynamic error, StackTrace? stackTrace]) {
    if (error != null || stackTrace != null) {
      _logger.d('$message\nError: $error', error: error, stackTrace: stackTrace);
    } else {
      _logger.d(message);
    }
  }

  void i(dynamic message, [dynamic error, StackTrace? stackTrace]) {
    if (error != null || stackTrace != null) {
      _logger.i('$message\nError: $error', error: error, stackTrace: stackTrace);
    } else {
      _logger.i(message);
    }
  }

  void w(dynamic message, [dynamic error, StackTrace? stackTrace]) {
    if (error != null || stackTrace != null) {
      _logger.w('$message\nError: $error', error: error, stackTrace: stackTrace);
    } else {
      _logger.w(message);
    }
  }

  void e(dynamic message, [dynamic error, StackTrace? stackTrace]) {
    if (error != null || stackTrace != null) {
      _logger.e('$message\nError: $error', error: error, stackTrace: stackTrace);
    } else {
      _logger.e(message);
    }
  }

  void wtf(dynamic message, [dynamic error, StackTrace? stackTrace]) {
    if (error != null || stackTrace != null) {
      _logger.f('$message\nError: $error', error: error, stackTrace: stackTrace);
    } else {
      _logger.f(message);
    }
  }
}
