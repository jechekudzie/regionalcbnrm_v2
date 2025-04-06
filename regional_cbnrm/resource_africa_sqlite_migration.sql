-- SQLite migration for Resource Africa Flutter App
-- Based on MySQL schema structure

PRAGMA foreign_keys = ON;

-- Countries table
CREATE TABLE countries (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  code TEXT,
  slug TEXT,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);

-- Provinces table
CREATE TABLE provinces (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  slug TEXT,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);

-- Cities table
CREATE TABLE cities (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  province_id INTEGER NOT NULL,
  name TEXT NOT NULL,
  slug TEXT,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
);

-- Species table
CREATE TABLE species (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  scientific TEXT,
  male_name TEXT,
  female_name TEXT,
  avatar TEXT,
  is_special INTEGER NOT NULL DEFAULT 0,
  slug TEXT,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);

-- Genders table
CREATE TABLE genders (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  description TEXT,
  slug TEXT,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);

-- Species genders table
CREATE TABLE species_genders (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  description TEXT,
  slug TEXT,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);

-- Organisation types table
CREATE TABLE organisation_types (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  description TEXT,
  slug TEXT,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);

-- Organisations table
CREATE TABLE organisations (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  organisation_type_id INTEGER NOT NULL,
  organisation_id INTEGER,
  logo TEXT,
  description TEXT,
  slug TEXT,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
);

-- Users table
CREATE TABLE users (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  email TEXT NOT NULL UNIQUE,
  email_verified_at TIMESTAMP,
  password TEXT NOT NULL,
  slug TEXT,
  remember_token TEXT,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);

-- Permissions table
CREATE TABLE permissions (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  guard_name TEXT NOT NULL,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);

-- Roles table
CREATE TABLE roles (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  organisation_id INTEGER,
  name TEXT NOT NULL,
  guard_name TEXT NOT NULL,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
);

-- Role has permissions table
CREATE TABLE role_has_permissions (
  permission_id INTEGER NOT NULL,
  role_id INTEGER NOT NULL,
  PRIMARY KEY (permission_id, role_id),
);

-- Model has permissions table
CREATE TABLE model_has_permissions (
  permission_id INTEGER NOT NULL,
  model_type TEXT NOT NULL,
  model_id INTEGER NOT NULL,
  organisation_id INTEGER NOT NULL,
  PRIMARY KEY (organisation_id, permission_id, model_id, model_type),
);

-- Model has roles table
CREATE TABLE model_has_roles (
  role_id INTEGER NOT NULL,
  model_type TEXT NOT NULL,
  model_id INTEGER NOT NULL,
  organisation_id INTEGER NOT NULL,
  PRIMARY KEY (organisation_id, role_id, model_id, model_type),
);

-- Organisation users table
CREATE TABLE organisation_users (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  organisation_id INTEGER NOT NULL,
  user_id INTEGER NOT NULL,
  role_id INTEGER,
  is_active INTEGER NOT NULL DEFAULT 1,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
);

-- Organisation permissions table
CREATE TABLE organisation_permissions (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  organisation_id INTEGER NOT NULL,
  permission_id INTEGER NOT NULL,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
);

-- Conflict types table
CREATE TABLE conflict_types (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  description TEXT,
  slug TEXT,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);

-- Conflict outcomes table
CREATE TABLE conflict_out_comes (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  conflict_type_id INTEGER NOT NULL,
  name TEXT NOT NULL,
  description TEXT,
  slug TEXT,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
);

-- Control measures table
CREATE TABLE control_measures (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  conflict_type_id INTEGER NOT NULL,
  name TEXT NOT NULL,
  type TEXT,
  description TEXT,
  slug TEXT,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
);

-- Wildlife conflict incidents table
CREATE TABLE wildlife_conflict_incidents (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  organisation_id INTEGER NOT NULL,
  title TEXT NOT NULL,
  period TEXT NOT NULL,
  incident_date DATE NOT NULL,
  incident_time TIME NOT NULL,
  longitude REAL,
  latitude REAL,
  location_description TEXT,
  description TEXT,
  conflict_type_id INTEGER,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  author TEXT,
  sync_status TEXT DEFAULT 'pending'
);

-- Wildlife conflict incident species table
CREATE TABLE wildlife_conflict_incident_species (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  wildlife_conflict_incident_id INTEGER NOT NULL,
  species_id INTEGER NOT NULL,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
);

-- Wildlife conflict outcomes table
CREATE TABLE wildlife_conflict_outcomes (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  wildlife_conflict_incident_id INTEGER NOT NULL,
  conflict_out_come_id INTEGER NOT NULL,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  UNIQUE (wildlife_conflict_incident_id, conflict_out_come_id),
);

-- Dynamic fields table
CREATE TABLE dynamic_fields (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  organisation_id INTEGER NOT NULL,
  conflict_outcome_id INTEGER,
  field_name TEXT NOT NULL,
  field_type TEXT NOT NULL,
  label TEXT,
  slug TEXT,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
);

-- Dynamic field options table
CREATE TABLE dynamic_field_options (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  dynamic_field_id INTEGER NOT NULL,
  option_value TEXT NOT NULL,
  option_label TEXT NOT NULL,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
);

-- Conflict outcome dynamic field values table
CREATE TABLE conflict_outcome_dynamic_field_values (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  wildlife_conflict_incident_id INTEGER NOT NULL,
  dynamic_field_id INTEGER NOT NULL,
  value TEXT NOT NULL,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
);

-- Wildlife conflict dynamic values table
CREATE TABLE wildlife_conflict_dynamic_values (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  wildlife_conflict_outcome_id INTEGER NOT NULL,
  dynamic_field_id INTEGER NOT NULL,
  field_value TEXT,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  UNIQUE (wildlife_conflict_outcome_id, dynamic_field_id),
);

-- Problem animal controls table
CREATE TABLE problem_animal_controls (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  organisation_id INTEGER NOT NULL,
  wildlife_conflict_incident_id INTEGER NOT NULL,
  control_date DATE NOT NULL,
  control_time TIME,
  period INTEGER NOT NULL,
  location TEXT NOT NULL,
  description TEXT,
  latitude REAL,
  longitude REAL,
  estimated_number INTEGER NOT NULL DEFAULT 1,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  author TEXT,
  sync_status TEXT DEFAULT 'pending'
);

-- PAC control measures table
CREATE TABLE pac_control_measures (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  problem_animal_control_id INTEGER NOT NULL,
  control_measure_id INTEGER NOT NULL,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
);

-- Crop types table
CREATE TABLE crop_types (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  description TEXT,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);

-- Livestock types table
CREATE TABLE live_stock_types (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  description TEXT,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);

-- Animal control records table
CREATE TABLE animal_control_records (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  organisation_id INTEGER NOT NULL,
  species_id INTEGER NOT NULL,
  period TEXT NOT NULL,
  number_of_cases INTEGER NOT NULL DEFAULT 0,
  killed INTEGER NOT NULL DEFAULT 0,
  relocated INTEGER NOT NULL DEFAULT 0,
  scared INTEGER NOT NULL DEFAULT 0,
  injured INTEGER NOT NULL DEFAULT 0,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  UNIQUE (organisation_id, species_id, period),
  author TEXT,
  sync_status TEXT DEFAULT 'pending'
);

-- Crop conflict records table
CREATE TABLE crop_conflict_records (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  organisation_id INTEGER NOT NULL,
  species_id INTEGER NOT NULL,
  crop_type_id INTEGER NOT NULL,
  hectrage_destroyed REAL NOT NULL,
  period TEXT NOT NULL,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  UNIQUE (organisation_id, species_id, crop_type_id, period),
  author TEXT,
  sync_status TEXT DEFAULT 'pending'
);

-- Human conflict records table
CREATE TABLE human_conflict_records (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  organisation_id INTEGER NOT NULL,
  species_id INTEGER NOT NULL,
  gender_id INTEGER NOT NULL,
  deaths INTEGER NOT NULL DEFAULT 0,
  injured INTEGER NOT NULL DEFAULT 0,
  period TEXT NOT NULL,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  UNIQUE (organisation_id, species_id, gender_id, period),
  author TEXT,
  sync_status TEXT DEFAULT 'pending'
);

-- Livestock conflict records table
CREATE TABLE live_stock_conflict_records (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  organisation_id INTEGER NOT NULL,
  species_id INTEGER NOT NULL,
  live_stock_type_id INTEGER NOT NULL,
  killed INTEGER NOT NULL DEFAULT 0,
  injured INTEGER NOT NULL DEFAULT 0,
  period TEXT NOT NULL,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  UNIQUE (organisation_id, species_id, live_stock_type_id, period),
  author TEXT,
  sync_status TEXT DEFAULT 'pending'
);

-- Poaching methods table
CREATE TABLE poaching_methods (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  slug TEXT,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);

-- Poaching reasons table
CREATE TABLE poaching_reasons (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  slug TEXT,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);

-- Poacher types table
CREATE TABLE poacher_types (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  slug TEXT,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);

-- Offence types table
CREATE TABLE offence_types (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  name TEXT NOT NULL,
  slug TEXT,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);

-- Poaching incidents table
CREATE TABLE poaching_incidents (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  organisation_id INTEGER NOT NULL,
  title TEXT NOT NULL,
  location TEXT,
  longitude REAL,
  docket_number TEXT,
  docket_status TEXT,
  latitude REAL,
  period INTEGER NOT NULL,
  date DATE NOT NULL,
  time TIME NOT NULL,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  author TEXT,
  sync_status TEXT DEFAULT 'pending'
);

-- Poaching incident methods table
CREATE TABLE poaching_incident_methods (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  poaching_incident_id INTEGER NOT NULL,
  poaching_method_id INTEGER NOT NULL,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
);

-- Poaching incident species table
CREATE TABLE poaching_incident_species (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  poaching_incident_id INTEGER NOT NULL,
  species_id INTEGER NOT NULL,
  estimate_number INTEGER,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
);

-- Poachers table
CREATE TABLE poachers (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  poaching_incident_id INTEGER NOT NULL,
  first_name TEXT NOT NULL,
  last_name TEXT NOT NULL,
  middle_name TEXT,
  age INTEGER,
  status TEXT,
  country_id INTEGER,
  province_id INTEGER,
  city_id INTEGER,
  offence_type_id INTEGER,
  poacher_type_id INTEGER,
  poaching_reason_id INTEGER,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  author TEXT,
  sync_status TEXT DEFAULT 'pending'
);

-- Human resource records table
CREATE TABLE human_resource_records (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  organisation_id INTEGER NOT NULL,
  period TEXT NOT NULL,
  wildlife_managers INTEGER NOT NULL DEFAULT 0,
  game_scouts INTEGER NOT NULL DEFAULT 0,
  rangers INTEGER NOT NULL DEFAULT 0,
  employed_by TEXT NOT NULL DEFAULT 'organisation',
  notes TEXT,
  created_at TIMESTAMP,
 author TEXT,
 sync_status TEXT DEFAULT 'pending'
  updated_at TIMESTAMP,
  author TEXT,
  sync_status TEXT DEFAULT 'pending'
);

-- Income records table
CREATE TABLE income_records (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  organisation_id INTEGER NOT NULL,
  period TEXT NOT NULL,
  rdc_share REAL NOT NULL,
  community_share REAL NOT NULL,
  ca_share REAL NOT NULL,
  created_at TIMESTAMP,
 author TEXT,
 sync_status TEXT DEFAULT 'pending'
  updated_at TIMESTAMP,
  author TEXT,
  sync_status TEXT DEFAULT 'pending'
);

-- Income beneficiary records table
CREATE TABLE income_beneficiary_records (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  organisation_id INTEGER NOT NULL,
  period TEXT NOT NULL,
  households INTEGER NOT NULL DEFAULT 0,
  males INTEGER NOT NULL DEFAULT 0,
  females INTEGER NOT NULL DEFAULT 0,
  notes TEXT,
  created_at TIMESTAMP,
 author TEXT,
 sync_status TEXT DEFAULT 'pending'
  updated_at TIMESTAMP,
  UNIQUE (organisation_id, period),
);

-- Income use records table
CREATE TABLE income_use_records (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  organisation_id INTEGER NOT NULL,
  period TEXT NOT NULL,
  administration_amount REAL NOT NULL DEFAULT 0,
  management_activities_amount REAL NOT NULL DEFAULT 0,
  social_services_amount REAL NOT NULL DEFAULT 0,
  law_enforcement_amount REAL NOT NULL DEFAULT 0,
  other_amount REAL NOT NULL DEFAULT 0,
  other_description TEXT,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  UNIQUE (organisation_id, period),
);

-- Source of income records table
CREATE TABLE source_of_income_records (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  organisation_id INTEGER NOT NULL,
  period TEXT NOT NULL,
  trophy_fee_amount REAL NOT NULL DEFAULT 0,
  hides_amount REAL NOT NULL DEFAULT 0,
  meat_amount REAL NOT NULL DEFAULT 0,
  hunting_concession_fee_amount REAL NOT NULL DEFAULT 0,
  photographic_fee_amount REAL NOT NULL DEFAULT 0,
  other_amount REAL NOT NULL DEFAULT 0,
  other_description TEXT,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  UNIQUE (organisation_id, period),
);

-- Create indexes

-- Organizations
CREATE INDEX idx_organisations_type ON organisations(organisation_type_id);
CREATE INDEX idx_organisations_parent ON organisations(organisation_id);

-- Users
CREATE INDEX idx_users_email ON users(email);

-- Conflicts
CREATE INDEX idx_wildlife_conflict_incidents_org ON wildlife_conflict_incidents(organisation_id);
CREATE INDEX idx_wildlife_conflict_incidents_type ON wildlife_conflict_incidents(conflict_type_id);

-- Wildlife conflict incidents relationships
CREATE INDEX idx_wildlife_conflict_incident_species_incident ON wildlife_conflict_incident_species(wildlife_conflict_incident_id);
CREATE INDEX idx_wildlife_conflict_incident_species_species ON wildlife_conflict_incident_species(species_id);
CREATE INDEX idx_wildlife_conflict_outcomes_incident ON wildlife_conflict_outcomes(wildlife_conflict_incident_id);
CREATE INDEX idx_wildlife_conflict_outcomes_outcome ON wildlife_conflict_outcomes(conflict_out_come_id);

-- Dynamic fields
CREATE INDEX idx_dynamic_fields_org ON dynamic_fields(organisation_id);
CREATE INDEX idx_dynamic_fields_outcome ON dynamic_fields(conflict_outcome_id);
CREATE INDEX idx_dynamic_field_options_field ON dynamic_field_options(dynamic_field_id);

-- PAC
CREATE INDEX idx_problem_animal_controls_org ON problem_animal_controls(organisation_id);
CREATE INDEX idx_problem_animal_controls_incident ON problem_animal_controls(wildlife_conflict_incident_id);
CREATE INDEX idx_pac_control_measures_pac ON pac_control_measures(problem_animal_control_id);
CREATE INDEX idx_pac_control_measures_cm ON pac_control_measures(control_measure_id);

-- Poaching
CREATE INDEX idx_poaching_incidents_org ON poaching_incidents(organisation_id);
CREATE INDEX idx_poaching_incident_methods_incident ON poaching_incident_methods(poaching_incident_id);
CREATE INDEX idx_poaching_incident_methods_method ON poaching_incident_methods(poaching_method_id);
CREATE INDEX idx_poaching_incident_species_incident ON poaching_incident_species(poaching_incident_id);
CREATE INDEX idx_poaching_incident_species_species ON poaching_incident_species(species_id);
CREATE INDEX idx_poachers_incident ON poachers(poaching_incident_id);

-- Records
CREATE INDEX idx_animal_control_records_org_species ON animal_control_records(organisation_id, species_id);
CREATE INDEX idx_crop_conflict_records_org_species ON crop_conflict_records(organisation_id, species_id);
CREATE INDEX idx_human_conflict_records_org_species ON human_conflict_records(organisation_id, species_id);
CREATE INDEX idx_live_stock_conflict_records_org_species ON live_stock_conflict_records(organisation_id, species_id);
CREATE INDEX idx_human_resource_records_org ON human_resource_records(organisation_id);
CREATE INDEX idx_income_records_org ON income_records(organisation_id);