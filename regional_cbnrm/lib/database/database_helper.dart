import 'package:sqflite/sqflite.dart';
import 'package:path/path.dart';
import 'package:flutter/services.dart';

Future<Database> initializeDatabase() async {
  final database = await openDatabase(
    join(await getDatabasesPath(), 'resource_africa.db'),
    onCreate: (db, version) async {
      // Read the migration script
      String migrationScript = await rootBundle.loadString('resource_africa/resource_africa_sqlite_migration.sql');

      // Execute the migration script line by line
      List<String> statements = migrationScript.split(';');
      for (String statement in statements) {
        if (statement.trim().isNotEmpty) {
          await db.execute(statement);
        }
      }
    },
    version: 1,
  );
  return database;
}