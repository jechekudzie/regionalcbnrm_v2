# Resource Africa Mobile App Implementation

This document provides a technical overview of the implementation details for the Resource Africa mobile application.

## Architecture Overview

The application is built with a layered architecture following best practices for both Flutter and Laravel:

1. **Data Layer**: Models and repositories
2. **Service Layer**: Business logic and state management
3. **Presentation Layer**: UI components and screens

### Backend API (Laravel)

The Laravel backend provides a RESTful API with the following endpoints:

- **Authentication**: `/api/v1/login`, `/api/v1/logout`, `/api/v1/user`
- **Organizations**: `/api/v1/organisations`, `/api/v1/organisations/{id}`
- **Wildlife Conflicts**: `/api/v1/organisations/{id}/wildlife-conflicts`, `/api/v1/wildlife-conflicts/{id}`
- **Problem Animal Control**: `/api/v1/organisations/{id}/problem-animal-controls`, `/api/v1/problem-animal-controls/{id}`
- **Poaching**: `/api/v1/organisations/{id}/poaching-incidents`, `/api/v1/poaching-incidents/{id}`
- **Hunting**: `/api/v1/organisations/{id}/hunting-activities`, `/api/v1/hunting-activities/{id}`

All API responses follow a consistent format:

```json
{
  "status": "success|error",
  "message": "Optional message",
  "data": {
    // Response data
  },
  "errors": {
    // Validation errors (if any)
  }
}
```

### Mobile App Architecture (Flutter with GetX)

The mobile app is built using the GetX pattern, which provides:

1. **Dependency Injection**: Services and repositories are injected where needed
2. **State Management**: Reactive state management with Rx variables
3. **Route Management**: Named routes with arguments

## Key Components

### Authentication

Authentication is implemented using Laravel Passport for token-based authentication:

1. User credentials are sent to the API
2. API returns a bearer token and user details
3. Token is stored securely in shared preferences
4. All API requests include the token in Authorization header

### Database Implementation

The app uses SQLite for local storage with a schema that mirrors the server database:

```dart
// Database initialization
Future<void> _onCreate(Database db, int version) async {
  await db.transaction((txn) async {
    // Create user table
    await txn.execute('''
      CREATE TABLE user (
        id INTEGER PRIMARY KEY,
        name TEXT,
        email TEXT,
        roles TEXT
      )
    ''');
    
    // Create organisations table
    await txn.execute('''
      CREATE TABLE organisations (
        id INTEGER PRIMARY KEY,
        name TEXT,
        type_id INTEGER,
        type_name TEXT,
        parent_id INTEGER,
        created_at TEXT,
        updated_at TEXT
      )
    ''');
    
    // Additional tables for wildlife conflicts, poaching, etc.
    // ...
    
    // Sync queue for tracking offline changes
    await txn.execute('''
      CREATE TABLE sync_queue (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        table_name TEXT,
        record_id INTEGER,
        action TEXT,
        data TEXT,
        timestamp TEXT,
        status TEXT DEFAULT 'pending',
        attempt_count INTEGER DEFAULT 0,
        last_attempt TEXT
      )
    ''');
  });
}
```

### Offline Synchronization

The app implements a robust synchronization mechanism:

1. All changes are saved locally first
2. Changes are added to a sync queue
3. When connectivity is restored, the sync service processes the queue:

```dart
Future<bool> syncData() async {
  if (!_connectivityService.isConnected.value) {
    return false;
  }
  
  isSyncing.value = true;
  
  try {
    final pendingItems = await _dbHelper.getPendingSyncItems();
    
    for (final item in pendingItems) {
      final tableName = item['table_name'];
      final recordId = item['record_id'];
      final action = item['action'];
      final data = item['data'];
      
      bool success = await _syncItem(item['id'], tableName, recordId, action, data);
      if (!success) {
        // Log failure but continue with other items
      }
    }
    
    await _updatePendingSyncCount();
    return true;
  } catch (e) {
    // Handle errors
    return false;
  } finally {
    isSyncing.value = false;
  }
}
```

### Repository Pattern

Each module has its own repository that handles data operations:

```dart
class WildlifeConflictRepository {
  final ApiService _apiService = ApiService();
  final DatabaseHelper _dbHelper = DatabaseHelper();

  // Get incidents from API or local database
  Future<List<WildlifeConflictIncident>> getIncidents(int organisationId) async {
    try {
      // Try API first
      final response = await _apiService.get('/organisations/$organisationId/wildlife-conflicts');
      
      if (response['status'] == 'success') {
        final incidents = (response['data']['data'] as List)
            .map((incident) => WildlifeConflictIncident.fromJson(incident))
            .toList();
        
        // Cache in local database
        await _saveIncidentsToDb(incidents);
        
        return incidents;
      } else {
        throw AppException(response['message'] ?? 'Failed to fetch incidents');
      }
    } catch (e) {
      // If API fails, try local database
      // ...
    }
  }
  
  // Create incident (handles offline mode too)
  Future<WildlifeConflictIncident> createIncident(WildlifeConflictIncident incident) async {
    try {
      // Try API first
      final response = await _apiService.post(
        '/wildlife-conflicts',
        data: incident.toApiJson(),
      );
      
      // Process response and save to local DB
      // ...
    } catch (e) {
      // If API fails, save locally and queue for sync
      // ...
    }
  }
}
```

### UI Implementation

The UI is built using Material Design with custom styling:

1. Each module has dedicated screens for listing, creating, and viewing details
2. Forms use validation to ensure data integrity
3. Maps are integrated for location selection
4. Error handling provides user-friendly feedback

Example screen structure:

```dart
class WildlifeConflictListScreen extends StatefulWidget {
  @override
  _WildlifeConflictListScreenState createState() => _WildlifeConflictListScreenState();
}

class _WildlifeConflictListScreenState extends State<WildlifeConflictListScreen> {
  final WildlifeConflictRepository _repository = WildlifeConflictRepository();
  final RxList<WildlifeConflictIncident> _incidents = RxList<WildlifeConflictIncident>([]);
  final RxBool _isLoading = true.obs;
  
  @override
  void initState() {
    super.initState();
    _loadIncidents();
  }
  
  Future<void> _loadIncidents() async {
    // Load data from repository
  }
  
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: Text('Wildlife Conflicts')),
      body: Obx(() {
        if (_isLoading.value) {
          return Center(child: CircularProgressIndicator());
        }
        
        // Build list of incidents
        return ListView.builder(
          // ...
        );
      }),
      floatingActionButton: FloatingActionButton(
        onPressed: () => Get.toNamed('/wildlife-conflicts/create'),
        child: Icon(Icons.add),
      ),
    );
  }
}
```

## Testing Implementation

The implementation includes tests at multiple levels:

1. **Unit Tests**: Testing repositories and services in isolation
2. **Widget Tests**: Testing UI components
3. **Integration Tests**: Testing end-to-end flows

Example repository test:

```dart
void main() {
  group('WildlifeConflictRepository', () {
    late WildlifeConflictRepository repository;
    late MockApiService mockApiService;
    late MockDatabaseHelper mockDatabaseHelper;
    
    setUp(() {
      mockApiService = MockApiService();
      mockDatabaseHelper = MockDatabaseHelper();
      repository = WildlifeConflictRepository(
        apiService: mockApiService,
        dbHelper: mockDatabaseHelper,
      );
    });
    
    test('getIncidents returns incidents from API when connected', () async {
      // Set up mock responses
      // Execute test
      // Verify results
    });
    
    test('getIncidents returns incidents from local DB when offline', () async {
      // Set up mock responses
      // Execute test
      // Verify results
    });
  });
}
```

## Performance Considerations

The app is optimized for performance and resource usage:

1. **Memory Management**: Proper disposal of controllers and subscriptions
2. **Database Efficiency**: Indexed queries and efficient data structures
3. **UI Performance**: Lazy loading of list items and efficient rebuilds
4. **Battery Usage**: Minimized background operations
5. **Network Bandwidth**: Compressed data transfer and selective syncing

## Security Implementation

Security is implemented at multiple levels:

1. **Authentication**: Secure token storage and expiration handling
2. **Data Encryption**: Sensitive data is encrypted at rest
3. **Input Validation**: All user input is validated
4. **Permission Checking**: Organization-based access control

## Future Improvements

The current implementation provides a solid foundation, with opportunities for future enhancements:

1. **Biometric Authentication**: Add fingerprint/face ID login
2. **Enhanced Offline Maps**: Better caching of map tiles
3. **Push Notifications**: Real-time updates from the server
4. **Analytics**: Usage tracking and error reporting
5. **Expanded Test Coverage**: More comprehensive tests

## Conclusion

The Resource Africa mobile app provides a robust companion to the web application, with comprehensive offline capabilities and a user-friendly interface. The layered architecture ensures maintainability and extensibility for future development.