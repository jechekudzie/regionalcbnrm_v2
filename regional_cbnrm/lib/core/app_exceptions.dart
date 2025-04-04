class AppException implements Exception {
  final String message;
  final String? prefix;

  AppException([this.message = 'Something went wrong', this.prefix]);

  @override
  String toString() {
    return '$prefix$message';
  }
}

class NoInternetException extends AppException {
  NoInternetException([String message = 'No internet connection']) 
    : super(message, 'Connection Error: ');
}

class TimeoutException extends AppException {
  TimeoutException([String message = 'Connection timed out']) 
    : super(message, 'Timeout: ');
}

class ServerException extends AppException {
  ServerException([String message = 'Server error occurred']) 
    : super(message, 'Server Error: ');
}

class BadRequestException extends AppException {
  BadRequestException([String message = 'Bad request']) 
    : super(message, 'Bad Request: ');
}

class UnauthorizedException extends AppException {
  UnauthorizedException([String message = 'Unauthorized']) 
    : super(message, 'Authorization Error: ');
}

class NotFoundException extends AppException {
  NotFoundException([String message = 'The requested information could not be found']) 
    : super(message, 'Not Found: ');
}

class ValidationException extends AppException {
  final Map<String, dynamic>? errors;
  
  ValidationException([String message = 'Validation failed', this.errors]) 
    : super(message, 'Validation Error: ');
}