import 'package:dio/dio.dart';
import 'package:pretty_dio_logger/pretty_dio_logger.dart';
import 'package:resource_africa/core/app_exceptions.dart';
import 'package:resource_africa/utils/app_constants.dart';
import 'package:resource_africa/utils/app_preferences.dart';
import 'package:resource_africa/utils/logger.dart';

class ApiService {
  final Dio _dio = Dio();
  final AppPreferences _preferences = AppPreferences();
  final AppLogger _logger = AppLogger();

  ApiService() {
    _dio.options.baseUrl = AppConstants.baseUrl;
    _dio.options.connectTimeout = const Duration(seconds: 30);
    _dio.options.receiveTimeout = const Duration(seconds: 30);
    _dio.options.headers = {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
    };

    // Add logging interceptor in debug mode
    if (!AppConstants.isProduction) {
      _dio.interceptors.add(
        PrettyDioLogger(
          requestHeader: true,
          requestBody: true,
          responseBody: true,
          responseHeader: false,
          error: true,
          compact: true,
        ),
      );
    }

    // Add authentication interceptor
    _dio.interceptors.add(
      InterceptorsWrapper(
        onRequest: (options, handler) async {
          _logger.d('Starting request: ${options.uri}');
          final token = await _preferences.getAuthToken();
          if (token != null && token.isNotEmpty) {
            options.headers['Authorization'] = 'Bearer $token';
            _logger.d('Added token to request');
          } else {
            _logger.d('No token available for request');
          }
          return handler.next(options);
        },
        onResponse: (response, handler) {
          _logger.d('Response received: ${response.statusCode}');
          return handler.next(response);
        },
        onError: (DioException e, handler) async {
          _logger.e('API Error', e);
          
          if (e.response?.statusCode == 401) {
            _logger.w('Unauthorized request - clearing auth token');
            // Handle token refresh or logout
            await _preferences.clearAuthToken();
            // TODO: Navigate to login screen or refresh token
          }
          return handler.next(e);
        },
      ),
    );
    
    _logger.i('ApiService initialized with base URL: ${_dio.options.baseUrl}');
  }

  Future<dynamic> get(String path, {Map<String, dynamic>? queryParameters}) async {
    try {
      _logger.d('GET request to: $path with params: $queryParameters');
      final response = await _dio.get(
        path,
        queryParameters: queryParameters,
      );
      return _processResponse(response);
    } on DioException catch (e) {
      final error = _handleError(e);
      _logger.e('GET request failed: $path', error);
      throw error;
    } catch (e) {
      _logger.e('Unknown error in GET request: $path', e);
      throw AppException('Unknown error occurred: $e');
    }
  }

  Future<dynamic> post(String path, {dynamic data, Map<String, dynamic>? queryParameters}) async {
    try {
      _logger.d('POST request to: $path with data: $data and params: $queryParameters');
      final response = await _dio.post(
        path,
        data: data,
        queryParameters: queryParameters,
      );
      return _processResponse(response);
    } on DioException catch (e) {
      final error = _handleError(e);
      _logger.e('POST request failed: $path', error);
      throw error;
    } catch (e) {
      _logger.e('Unknown error in POST request: $path', e);
      throw AppException('Unknown error occurred: $e');
    }
  }

  Future<dynamic> put(String path, {dynamic data, Map<String, dynamic>? queryParameters}) async {
    try {
      _logger.d('PUT request to: $path with data: $data and params: $queryParameters');
      final response = await _dio.put(
        path,
        data: data,
        queryParameters: queryParameters,
      );
      return _processResponse(response);
    } on DioException catch (e) {
      final error = _handleError(e);
      _logger.e('PUT request failed: $path', error);
      throw error;
    } catch (e) {
      _logger.e('Unknown error in PUT request: $path', e);
      throw AppException('Unknown error occurred: $e');
    }
  }

  Future<dynamic> delete(String path, {dynamic data, Map<String, dynamic>? queryParameters}) async {
    try {
      _logger.d('DELETE request to: $path with data: $data and params: $queryParameters');
      final response = await _dio.delete(
        path,
        data: data,
        queryParameters: queryParameters,
      );
      return _processResponse(response);
    } on DioException catch (e) {
      final error = _handleError(e);
      _logger.e('DELETE request failed: $path', error);
      throw error;
    } catch (e) {
      _logger.e('Unknown error in DELETE request: $path', e);
      throw AppException('Unknown error occurred: $e');
    }
  }

  dynamic _processResponse(Response response) {
    _logger.d('Processing response with status code: ${response.statusCode}');
    switch (response.statusCode) {
      case 200:
      case 201:
        return response.data;
      case 400:
        _logger.w('Bad request response: ${response.data}');
        throw BadRequestException(response.data['message'] ?? 'Bad request');
      case 401:
      case 403:
        _logger.w('Unauthorized response: ${response.data}');
        throw UnauthorizedException(response.data['message'] ?? 'Unauthorized');
      case 404:
        _logger.w('Not found response: ${response.data}');
        throw NotFoundException(response.data['message'] ?? 'Not found');
      case 500:
      default:
        _logger.e('Server error response: ${response.data}');
        throw ServerException(response.data['message'] ?? 'Server error');
    }
  }

  Exception _handleError(DioException error) {
    _logger.e('DioException encountered', error);
    switch (error.type) {
      case DioExceptionType.connectionTimeout:
      case DioExceptionType.sendTimeout:
      case DioExceptionType.receiveTimeout:
        _logger.e('Connection timeout', error);
        return TimeoutException('Connection timed out');
      case DioExceptionType.connectionError:
        _logger.e('Connection error', error);
        return NoInternetException('No internet connection');
      case DioExceptionType.badResponse:
        _logger.e('Bad response', error, error.stackTrace);
        if (error.response != null) {
          return _processResponse(error.response!);
        }
        return ServerException('Server returned an invalid response');
      default:
        _logger.e('Other Dio error', error);
        return AppException('Something went wrong: ${error.message}');
    }
  }
}