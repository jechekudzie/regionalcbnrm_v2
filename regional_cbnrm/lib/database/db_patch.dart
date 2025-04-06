import 'package:sqflite/sqflite.dart';
import 'package:regional_cbnrm/utils/logger.dart';

Future<void> patchDatabaseForConflictOutcomes(Database db) async {
  // Check if dynamic_fields table exists
  final tables = await db.rawQuery(
    "SELECT name FROM sqlite_master WHERE type='table' AND name='dynamic_fields';"
  );
  
  AppLogger().d('Patching database for conflict outcomes. Tables found: ${tables.length}');

  if (tables.isEmpty) {
    AppLogger().d('Creating missing tables for conflict outcomes...');
    // Create missing tables
    await db.execute('''
CREATE TABLE conflict_outcomes (
    id INTEGER PRIMARY KEY,
    name TEXT NOT NULL,
    organisation_id INTEGER,
    created_at TEXT,
    updated_at TEXT
);
''');

    await db.execute('CREATE INDEX idx_conflict_outcomes_org_id ON conflict_outcomes (organisation_id);');

    await db.execute('''
CREATE TABLE dynamic_fields (
    id INTEGER PRIMARY KEY,
    name TEXT NOT NULL,
    field_type TEXT NOT NULL,
    options TEXT,
    required INTEGER DEFAULT 0,
    created_at TEXT,
    updated_at TEXT
);
''');

    await db.execute('''
CREATE TABLE conflict_outcome_dynamic_field_values (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    conflict_outcome_id INTEGER NOT NULL,
    dynamic_field_id INTEGER NOT NULL,
    value TEXT,
    created_at TEXT,
    updated_at TEXT,
    FOREIGN KEY(conflict_outcome_id) REFERENCES conflict_outcomes(id),
    FOREIGN KEY(dynamic_field_id) REFERENCES dynamic_fields(id)
);
''');

    await db.execute('CREATE INDEX idx_conflict_outcome_dynamic_field_values_outcome_id ON conflict_outcome_dynamic_field_values (conflict_outcome_id);');
    await db.execute('CREATE INDEX idx_conflict_outcome_dynamic_field_values_field_id ON conflict_outcome_dynamic_field_values (dynamic_field_id);');
    
    AppLogger().d('Successfully created tables: conflict_outcomes, dynamic_fields, conflict_outcome_dynamic_field_values');
  } else {
    AppLogger().d('Tables already exist, no need to patch');
      await db.execute('''
  CREATE TABLE wildlife_conflict_species (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    wildlife_conflict_incident_id INTEGER,
    species_id INTEGER,
    created_at TEXT,
    updated_at TEXT
  );
  ''');
  }
}