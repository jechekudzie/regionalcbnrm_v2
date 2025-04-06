import 'package:sqflite/sqflite.dart';
import 'db_patch.dart';
import 'package:path/path.dart';
import 'package:flutter/services.dart';

Future<Database> initializeDatabase() async {
  final database = await openDatabase(
    join(await getDatabasesPath(), 'resource_africa.db'),
    onCreate: (db, version) async {
      // Read the migration script
      try {
        String migrationScript = await rootBundle.loadString('resource_africa_sqlite_migration.sql');
        print('Loaded migration script successfully');

      // Execute the migration script line by line
      List<String> statements = migrationScript.split(';');
      for (String statement in statements) {
        if (statement.trim().isNotEmpty) {
          await db.execute(statement);
        }
      }
      
      } catch (e) {
        print('Error loading migration script: $e');
      }
    },
    version: 1,
  );

  // Patch missing tables if needed
  try {
    print('Applying database patches...');
    await patchDatabaseForConflictOutcomes(database);
    print('Patches applied successfully');
  } catch (e) {
    print('Error applying patches: $e');
  }

  return database;
}