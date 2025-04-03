import 'package:path/path.dart';
import 'package:sqflite/sqflite.dart';
import 'package:path_provider/path_provider.dart';
import 'package:resource_africa/utils/app_constants.dart';
import 'package:resource_africa/utils/logger.dart';
import 'dart:async';

class DatabaseHelper {
  static final DatabaseHelper _instance = DatabaseHelper._internal();
  static Database? _database;
  final AppLogger _logger = AppLogger();

  factory DatabaseHelper() => _instance;

  DatabaseHelper._internal();

  Future<Database> get database async {
    if (_database != null) return _database!;
    
    _logger.i('Initializing database');
    _database = await _initDatabase();
    return _database!;
  }

  Future<Database> _initDatabase() async {
    try {
      final documentsDirectory = await getApplicationDocumentsDirectory();
      final path = join(documentsDirectory.path, AppConstants.databaseName);
      _logger.d('Database path: $path');
      
      return await openDatabase(
        path,
        version: AppConstants.databaseVersion,
        onCreate: _onCreate,
        onUpgrade: _onUpgrade,
        onOpen: (db) => _logger.i('Database opened successfully'),
        onConfigure: (db) async {
          // Enable foreign keys
          await db.execute('PRAGMA foreign_keys = ON');
          _logger.d('Foreign keys enabled');
        },
      );
    } catch (e, stackTrace) {
      _logger.e('Error initializing database', e, stackTrace);
      rethrow;
    }
  }

  Future<void> _onCreate(Database db, int version) async {
    _logger.i('Creating database tables for version $version');
    
    // Wrap in transaction for better performance and atomicity
    await db.transaction((txn) async {
      try {
        // Create tables with foreign key constraints
        await txn.execute('''
          CREATE TABLE user (
            id INTEGER PRIMARY KEY,
            name TEXT,
            email TEXT,
            roles TEXT
          )
        ''');
        _logger.d('Created user table');

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
        _logger.d('Created organisations table');

        await txn.execute('''
          CREATE TABLE wildlife_conflict_incidents (
            id INTEGER PRIMARY KEY,
            organisation_id INTEGER,
            title TEXT,
            date TEXT,
            time TEXT,
            latitude REAL,
            longitude REAL,
            description TEXT,
            conflict_type_id INTEGER,
            species_id INTEGER,
            created_at TEXT,
            updated_at TEXT,
            sync_status TEXT DEFAULT 'pending',
            remote_id INTEGER
          )
        ''');
        _logger.d('Created wildlife_conflict_incidents table');

        await txn.execute('''
          CREATE TABLE wildlife_conflict_outcomes (
            id INTEGER PRIMARY KEY,
            incident_id INTEGER,
            conflict_outcome_id INTEGER,
            notes TEXT,
            date TEXT,
            created_at TEXT,
            updated_at TEXT,
            sync_status TEXT DEFAULT 'pending',
            remote_id INTEGER,
            FOREIGN KEY (incident_id) REFERENCES wildlife_conflict_incidents(id) ON DELETE CASCADE
          )
        ''');
        _logger.d('Created wildlife_conflict_outcomes table');

        await txn.execute('''
          CREATE TABLE problem_animal_controls (
            id INTEGER PRIMARY KEY,
            wildlife_conflict_incident_id INTEGER,
            control_measure_id INTEGER,
            date TEXT,
            time TEXT,
            description TEXT,
            latitude REAL,
            longitude REAL,
            number_of_animals INTEGER,
            created_at TEXT,
            updated_at TEXT,
            sync_status TEXT DEFAULT 'pending',
            remote_id INTEGER,
            FOREIGN KEY (wildlife_conflict_incident_id) REFERENCES wildlife_conflict_incidents(id) ON DELETE CASCADE
          )
        ''');
        _logger.d('Created problem_animal_controls table');

        await txn.execute('''
          CREATE TABLE poaching_incidents (
            id INTEGER PRIMARY KEY,
            organisation_id INTEGER,
            title TEXT,
            date TEXT,
            time TEXT,
            latitude REAL,
            longitude REAL,
            description TEXT,
            docket_number TEXT,
            docket_status TEXT,
            created_at TEXT,
            updated_at TEXT,
            sync_status TEXT DEFAULT 'pending',
            remote_id INTEGER
          )
        ''');
        _logger.d('Created poaching_incidents table');

        await txn.execute('''
          CREATE TABLE poaching_incident_species (
            id INTEGER PRIMARY KEY,
            poaching_incident_id INTEGER,
            species_id INTEGER,
            quantity INTEGER,
            created_at TEXT,
            updated_at TEXT,
            FOREIGN KEY (poaching_incident_id) REFERENCES poaching_incidents(id) ON DELETE CASCADE
          )
        ''');
        _logger.d('Created poaching_incident_species table');

        await txn.execute('''
          CREATE TABLE poaching_incident_methods (
            id INTEGER PRIMARY KEY,
            poaching_incident_id INTEGER,
            poaching_method_id INTEGER,
            created_at TEXT,
            updated_at TEXT,
            FOREIGN KEY (poaching_incident_id) REFERENCES poaching_incidents(id) ON DELETE CASCADE
          )
        ''');
        _logger.d('Created poaching_incident_methods table');

        await txn.execute('''
          CREATE TABLE poachers (
            id INTEGER PRIMARY KEY,
            poaching_incident_id INTEGER,
            name TEXT,
            id_number TEXT,
            identification_type_id INTEGER,
            gender TEXT,
            age INTEGER,
            nationality TEXT,
            status TEXT,
            created_at TEXT,
            updated_at TEXT,
            sync_status TEXT DEFAULT 'pending',
            remote_id INTEGER,
            FOREIGN KEY (poaching_incident_id) REFERENCES poaching_incidents(id) ON DELETE CASCADE
          )
        ''');
        _logger.d('Created poachers table');

        await txn.execute('''
          CREATE TABLE hunting_activities (
            id INTEGER PRIMARY KEY,
            organisation_id INTEGER,
            hunting_concession_id INTEGER,
            safari_operator_id INTEGER,
            period TEXT,
            start_date TEXT,
            end_date TEXT,
            client_name TEXT,
            client_nationality TEXT,
            client_country_of_residence TEXT,
            created_at TEXT,
            updated_at TEXT,
            sync_status TEXT DEFAULT 'pending',
            remote_id INTEGER
          )
        ''');
        _logger.d('Created hunting_activities table');

        await txn.execute('''
          CREATE TABLE hunting_activity_species (
            id INTEGER PRIMARY KEY,
            hunting_activity_id INTEGER,
            species_id INTEGER,
            quantity INTEGER,
            quota_allocation_balance_id INTEGER,
            created_at TEXT,
            updated_at TEXT,
            FOREIGN KEY (hunting_activity_id) REFERENCES hunting_activities(id) ON DELETE CASCADE
          )
        ''');
        _logger.d('Created hunting_activity_species table');

        await txn.execute('''
          CREATE TABLE professional_hunter_licenses (
            id INTEGER PRIMARY KEY,
            hunting_activity_id INTEGER,
            name TEXT,
            license_number TEXT,
            created_at TEXT,
            updated_at TEXT,
            FOREIGN KEY (hunting_activity_id) REFERENCES hunting_activities(id) ON DELETE CASCADE
          )
        ''');
        _logger.d('Created professional_hunter_licenses table');

        await txn.execute('''
          CREATE TABLE species (
            id INTEGER PRIMARY KEY,
            name TEXT,
            species_gender_id INTEGER,
            maturity_id INTEGER,
            created_at TEXT,
            updated_at TEXT
          )
        ''');
        _logger.d('Created species table');

        await txn.execute('''
          CREATE TABLE conflict_types (
            id INTEGER PRIMARY KEY,
            name TEXT,
            created_at TEXT,
            updated_at TEXT
          )
        ''');
        _logger.d('Created conflict_types table');

        await txn.execute('''
          CREATE TABLE conflict_outcomes (
            id INTEGER PRIMARY KEY,
            name TEXT,
            created_at TEXT,
            updated_at TEXT
          )
        ''');
        _logger.d('Created conflict_outcomes table');

        await txn.execute('''
          CREATE TABLE control_measures (
            id INTEGER PRIMARY KEY,
            name TEXT,
            created_at TEXT,
            updated_at TEXT
          )
        ''');
        _logger.d('Created control_measures table');

        await txn.execute('''
          CREATE TABLE poaching_methods (
            id INTEGER PRIMARY KEY,
            name TEXT,
            created_at TEXT,
            updated_at TEXT
          )
        ''');
        _logger.d('Created poaching_methods table');

        await txn.execute('''
          CREATE TABLE poaching_reasons (
            id INTEGER PRIMARY KEY,
            name TEXT,
            created_at TEXT,
            updated_at TEXT
          )
        ''');
        _logger.d('Created poaching_reasons table');

        await txn.execute('''
          CREATE TABLE hunting_concessions (
            id INTEGER PRIMARY KEY,
            organisation_id INTEGER,
            safari_id INTEGER,
            name TEXT,
            hectarage REAL,
            description TEXT,
            latitude REAL,
            longitude REAL,
            created_at TEXT,
            updated_at TEXT
          )
        ''');
        _logger.d('Created hunting_concessions table');

        await txn.execute('''
          CREATE TABLE quota_allocations (
            id INTEGER PRIMARY KEY,
            organisation_id INTEGER,
            species_id INTEGER,
            hunting_quota INTEGER,
            rational_killing_quota INTEGER,
            period TEXT,
            start_date TEXT,
            end_date TEXT,
            created_at TEXT,
            updated_at TEXT
          )
        ''');
        _logger.d('Created quota_allocations table');

        await txn.execute('''
          CREATE TABLE quota_allocation_balances (
            id INTEGER PRIMARY KEY,
            quota_allocation_id INTEGER,
            allocated INTEGER,
            off_take INTEGER,
            remaining INTEGER,
            created_at TEXT,
            updated_at TEXT,
            FOREIGN KEY (quota_allocation_id) REFERENCES quota_allocations(id) ON DELETE CASCADE
          )
        ''');
        _logger.d('Created quota_allocation_balances table');

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
        _logger.d('Created sync_queue table');
        
        _logger.i('All tables created successfully');
      } catch (e, stackTrace) {
        _logger.e('Error creating database tables', e, stackTrace);
        rethrow;
      }
    });
  }

  Future<void> _onUpgrade(Database db, int oldVersion, int newVersion) async {
    _logger.i('Upgrading database from $oldVersion to $newVersion');
    
    // Handle incremental upgrades
    if (oldVersion < 2) {
      // Version 1 to 2 upgrades
      _logger.d('Performing upgrade from version 1 to 2');
      // Add migration scripts here when needed
    }
    
    if (oldVersion < 3) {
      // Version 2 to 3 upgrades (when needed in future)
      // ...
    }
  }

  // Generic CRUD operations with improved error handling
  Future<int> insert(String table, Map<String, dynamic> values, {String? where, List<dynamic>? whereArgs}) async {
    try {
      _logger.d('Inserting into $table: $values');
      final db = await database;
      
      // Check if record exists when where clause is provided
      if (where != null && whereArgs != null) {
        final existing = await db.query(
          table,
          where: where,
          whereArgs: whereArgs,
          limit: 1,
        );
        
        if (existing.isNotEmpty) {
          // Update instead of insert
          _logger.d('Record already exists in $table, updating instead');
          return await update(table, values, where: where, whereArgs: whereArgs);
        }
      }
      
      final id = await db.insert(
        table, 
        values,
        conflictAlgorithm: ConflictAlgorithm.replace,
      );
      _logger.d('Inserted record ID: $id in $table');
      return id;
    } catch (e, stackTrace) {
      _logger.e('Error inserting into $table', e, stackTrace);
      rethrow;
    }
  }

  Future<List<Map<String, dynamic>>> query(
    String table, {
    List<String>? columns,
    String? where,
    List<dynamic>? whereArgs,
    String? orderBy,
    int? limit,
    int? offset,
  }) async {
    try {
      _logger.d('Querying $table - where: $where, args: $whereArgs');
      final db = await database;
      final result = await db.query(
        table,
        columns: columns,
        where: where,
        whereArgs: whereArgs,
        orderBy: orderBy,
        limit: limit,
        offset: offset,
      );
      _logger.d('Query result from $table: ${result.length} records');
      return result;
    } catch (e, stackTrace) {
      _logger.e('Error querying $table', e, stackTrace);
      rethrow;
    }
  }

  Future<int> update(
    String table,
    Map<String, dynamic> values, {
    String? where,
    List<dynamic>? whereArgs,
  }) async {
    try {
      _logger.d('Updating $table - where: $where, values: $values');
      final db = await database;
      final affected = await db.update(
        table,
        values,
        where: where,
        whereArgs: whereArgs,
      );
      _logger.d('Updated $affected records in $table');
      return affected;
    } catch (e, stackTrace) {
      _logger.e('Error updating $table', e, stackTrace);
      rethrow;
    }
  }

  Future<int> delete(
    String table, {
    String? where,
    List<dynamic>? whereArgs,
  }) async {
    try {
      _logger.d('Deleting from $table - where: $where, args: $whereArgs');
      final db = await database;
      final affected = await db.delete(
        table,
        where: where,
        whereArgs: whereArgs,
      );
      _logger.d('Deleted $affected records from $table');
      return affected;
    } catch (e, stackTrace) {
      _logger.e('Error deleting from $table', e, stackTrace);
      rethrow;
    }
  }

  Future<List<Map<String, dynamic>>> rawQuery(String sql, [List<dynamic>? arguments]) async {
    try {
      _logger.d('Executing raw query: $sql with args: $arguments');
      final db = await database;
      final result = await db.rawQuery(sql, arguments);
      _logger.d('Raw query result: ${result.length} records');
      return result;
    } catch (e, stackTrace) {
      _logger.e('Error executing raw query', e, stackTrace);
      rethrow;
    }
  }

  // Add to sync queue with improved error handling
  Future<int> addToSyncQueue(String tableName, int recordId, String action, String data) async {
    try {
      _logger.d('Adding to sync queue: table=$tableName, id=$recordId, action=$action');
      final db = await database;
      final id = await db.insert('sync_queue', {
        'table_name': tableName,
        'record_id': recordId,
        'action': action,
        'data': data,
        'timestamp': DateTime.now().toIso8601String(),
        'status': 'pending',
        'attempt_count': 0,
      });
      _logger.d('Added item to sync queue with ID: $id');
      return id;
    } catch (e, stackTrace) {
      _logger.e('Error adding to sync queue', e, stackTrace);
      rethrow;
    }
  }

  // Get pending sync items
  Future<List<Map<String, dynamic>>> getPendingSyncItems() async {
    try {
      _logger.d('Getting pending sync items');
      final db = await database;
      final result = await db.query(
        'sync_queue',
        where: 'status = ?',
        whereArgs: ['pending'],
        orderBy: 'timestamp ASC',
      );
      _logger.d('Found ${result.length} pending sync items');
      return result;
    } catch (e, stackTrace) {
      _logger.e('Error getting pending sync items', e, stackTrace);
      rethrow;
    }
  }

  // Update sync item status
  Future<int> updateSyncItemStatus(int id, String status) async {
    try {
      _logger.d('Updating sync item $id status to $status');
      final db = await database;
      
      // Get current attempt count
      final item = await db.query(
        'sync_queue',
        columns: ['attempt_count'],
        where: 'id = ?',
        whereArgs: [id],
      );
      
      final currentAttemptCount = item.isNotEmpty ? item.first['attempt_count'] as int : 0;
      
      final affected = await db.update(
        'sync_queue',
        {
          'status': status,
          'last_attempt': DateTime.now().toIso8601String(),
          'attempt_count': currentAttemptCount + 1,
        },
        where: 'id = ?',
        whereArgs: [id],
      );
      
      _logger.d('Updated sync item status for ID $id to $status (attempt ${currentAttemptCount + 1})');
      return affected;
    } catch (e, stackTrace) {
      _logger.e('Error updating sync item status', e, stackTrace);
      rethrow;
    }
  }

  // Clear database
  Future<void> clearDatabase() async {
    try {
      _logger.w('Clearing entire database');
      final db = await database;
      
      final tables = [
        'user',
        'organisations',
        'wildlife_conflict_incidents',
        'wildlife_conflict_outcomes',
        'problem_animal_controls',
        'poaching_incidents',
        'poaching_incident_species',
        'poaching_incident_methods',
        'poachers',
        'hunting_activities',
        'hunting_activity_species',
        'professional_hunter_licenses',
        'species',
        'conflict_types',
        'conflict_outcomes',
        'control_measures',
        'poaching_methods',
        'poaching_reasons',
        'hunting_concessions',
        'quota_allocations',
        'quota_allocation_balances',
        'sync_queue',
      ];

      // Using a transaction for atomicity
      await db.transaction((txn) async {
        // First disable foreign keys to avoid constraint errors
        await txn.execute('PRAGMA foreign_keys = OFF');
        
        for (String table in tables) {
          await txn.delete(table);
        }
        
        // Re-enable foreign keys
        await txn.execute('PRAGMA foreign_keys = ON');
      });
      
      _logger.i('Database cleared successfully');
    } catch (e, stackTrace) {
      _logger.e('Error clearing database', e, stackTrace);
      rethrow;
    }
  }
  
  // Check if database exists
  Future<bool> databaseExists() async {
    try {
      final documentsDirectory = await getApplicationDocumentsDirectory();
      final path = join(documentsDirectory.path, AppConstants.databaseName);
      return await databaseFactory.databaseExists(path);
    } catch (e, stackTrace) {
      _logger.e('Error checking if database exists', e, stackTrace);
      return false;
    }
  }
  
  // Get database path
  Future<String> getDatabasePath() async {
    try {
      final documentsDirectory = await getApplicationDocumentsDirectory();
      final path = join(documentsDirectory.path, AppConstants.databaseName);
      return path;
    } catch (e, stackTrace) {
      _logger.e('Error getting database path', e, stackTrace);
      rethrow;
    }
  }
  
  // Delete database file
  Future<void> deleteDatabase() async {
    try {
      _logger.w('Deleting database file');
      final documentsDirectory = await getApplicationDocumentsDirectory();
      final path = join(documentsDirectory.path, AppConstants.databaseName);
      
      // Close database if open
      if (_database != null) {
        await _database!.close();
        _database = null;
      }
      
      // Delete the file
      await databaseFactory.deleteDatabase(path);
      _logger.i('Database file deleted');
    } catch (e, stackTrace) {
      _logger.e('Error deleting database file', e, stackTrace);
      rethrow;
    }
  }
  
  // Reset database (delete and recreate)
  Future<void> resetDatabase() async {
    try {
      await deleteDatabase();
      await database; // This will recreate the database
      _logger.i('Database reset completed');
    } catch (e, stackTrace) {
      _logger.e('Error resetting database', e, stackTrace);
      rethrow;
    }
  }
}