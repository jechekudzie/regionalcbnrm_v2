import 'package:sqflite/sqflite.dart';
import 'db_patch.dart';
import 'package:path/path.dart';
import 'package:get/get.dart';
import 'package:flutter/services.dart';
import 'package:regional_cbnrm/utils/logger.dart';

import 'dart:io';

Future<Database> initializeDatabase() async {
  // Delete the database file if it exists
  final dbPath = join(await getDatabasesPath(), 'resource_africa.db');
  final dbFile = File(dbPath);
  if (await dbFile.exists()) {
    await dbFile.delete();
  }

  final database = await openDatabase(
    join(await getDatabasesPath(), 'resource_africa.db'),
    onCreate: (db, version) async {
      // Read the migration script
      try {
        String migrationScript = await rootBundle.loadString('resource_africa_sqlite_migration.sql');
        AppLogger().d('Loaded migration script successfully');

      // Execute the migration script line by line
      List<String> statements = migrationScript.split(';');
      for (String statement in statements) {
        if (statement.trim().isNotEmpty) {
          await db.execute(statement);
        }
      }
      
      } catch (e) {
        AppLogger().d('Error loading migration script: $e');
      }
    },
    version: 3,
  );

  // Patch missing tables if needed
  try {
    AppLogger().d('Applying database patches...');
    await patchDatabaseForConflictOutcomes(database);
    AppLogger().d('Patches applied successfully');
  } catch (e) {
    AppLogger().d('Error applying patches: $e');
  }

  Get.put<Database>(database, tag: 'app_database');
  return database;
}