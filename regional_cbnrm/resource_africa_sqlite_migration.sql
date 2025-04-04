-- SQLite migration file

-- Organisations table
CREATE TABLE organisations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(255) NOT NULL,
    organisation_type_id INTEGER NOT NULL,
    organisation_id INTEGER,
    logo VARCHAR(255),
    description TEXT,
    slug VARCHAR(255),
    created_at DATETIME,
    updated_at DATETIME,
    FOREIGN KEY (organisation_type_id) REFERENCES organisation_types(id),
    FOREIGN KEY (organisation_id) REFERENCES organisations(id)
);

CREATE INDEX idx_organisations_organisation_type_id ON organisations (organisation_type_id);
CREATE INDEX idx_organisations_organisation_id ON organisations (organisation_id);

-- Permissions table
CREATE TABLE permissions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(255) NOT NULL,
    guard_name VARCHAR(255) NOT NULL,
    created_at DATETIME,
    updated_at DATETIME
);

-- Roles table
CREATE TABLE roles (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    organisation_id INTEGER,
    name VARCHAR(255) NOT NULL,
    guard_name VARCHAR(255) NOT NULL,
    created_at DATETIME,
    updated_at DATETIME,
    FOREIGN KEY (organisation_id) REFERENCES organisations(id)
);

CREATE INDEX idx_roles_organisation_id ON roles (organisation_id);

-- Role_has_permissions table
CREATE TABLE role_has_permissions (
    permission_id INTEGER NOT NULL,
    role_id INTEGER NOT NULL,
    FOREIGN KEY (permission_id) REFERENCES permissions(id),
    FOREIGN KEY (role_id) REFERENCES roles(id),
    PRIMARY KEY (permission_id, role_id)
);

CREATE INDEX idx_role_has_permissions_permission_id ON role_has_permissions (permission_id);
CREATE INDEX idx_role_has_permissions_role_id ON role_has_permissions (role_id);

-- Organisation_users table
CREATE TABLE organisation_users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    organisation_id INTEGER NOT NULL,
    user_id INTEGER NOT NULL,
    role_id INTEGER,
    is_active BOOLEAN NOT NULL DEFAULT 1,
    created_at DATETIME,
    updated_at DATETIME,
    FOREIGN KEY (organisation_id) REFERENCES organisations(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

CREATE INDEX idx_organisation_users_organisation_id ON organisation_users (organisation_id);
CREATE INDEX idx_organisation_users_user_id ON organisation_users (user_id);
CREATE INDEX idx_organisation_users_role_id ON organisation_users (role_id);

-- Wildlife_conflict_incidents table
CREATE TABLE wildlife_conflict_incidents (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    organisation_id INTEGER NOT NULL,
    title VARCHAR(255) NOT NULL,
    period INTEGER NOT NULL,
    incident_date DATE NOT NULL,
    incident_time TIME NOT NULL,
    longitude DECIMAL(10,7),
    latitude DECIMAL(10,7),
    location_description VARCHAR(255),
    description TEXT,
    conflict_type_id INTEGER,
    created_at DATETIME,
    updated_at DATETIME,
    FOREIGN KEY (organisation_id) REFERENCES organisations(id),
    FOREIGN KEY (conflict_type_id) REFERENCES conflict_types(id)
);

CREATE INDEX idx_wildlife_conflict_incidents_organisation_id ON wildlife_conflict_incidents (organisation_id);
CREATE INDEX idx_wildlife_conflict_incidents_conflict_type_id ON wildlife_conflict_incidents (conflict_type_id);

-- Wildlife_conflict_incident_species table
CREATE TABLE wildlife_conflict_incident_species (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    wildlife_conflict_incident_id INTEGER NOT NULL,
    species_id INTEGER NOT NULL,
    created_at DATETIME,
    updated_at DATETIME,
    FOREIGN KEY (wildlife_conflict_incident_id) REFERENCES wildlife_conflict_incidents(id),
    FOREIGN KEY (species_id) REFERENCES species(id)
);

CREATE INDEX idx_wildlife_conflict_incident_species_wildlife_conflict_incident_id ON wildlife_conflict_incident_species (wildlife_conflict_incident_id);
CREATE INDEX idx_wildlife_conflict_incident_species_species_id ON wildlife_conflict_incident_species (species_id);

-- Wildlife_conflict_outcomes table
CREATE TABLE wildlife_conflict_outcomes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    wildlife_conflict_incident_id INTEGER NOT NULL,
    conflict_out_come_id INTEGER NOT NULL,
    created_at DATETIME,
    updated_at DATETIME,
    FOREIGN KEY (wildlife_conflict_incident_id) REFERENCES wildlife_conflict_incidents(id),
    FOREIGN KEY (conflict_out_come_id) REFERENCES conflict_out_comes(id)
);

CREATE INDEX idx_wildlife_conflict_outcomes_wildlife_conflict_incident_id ON wildlife_conflict_outcomes (wildlife_conflict_incident_id);
CREATE INDEX idx_wildlife_conflict_outcomes_conflict_out_come_id ON wildlife_conflict_outcomes (conflict_out_come_id);

-- Problem_animal_controls table
CREATE TABLE problem_animal_controls (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    organisation_id INTEGER NOT NULL,
    wildlife_conflict_incident_id INTEGER NOT NULL,
    control_date DATE NOT NULL,
    control_time TIME,
    period INTEGER NOT NULL,
    location VARCHAR(255) NOT NULL,
    description TEXT,
    latitude DECIMAL(10,7),
    longitude DECIMAL(10,7),
    estimated_number INTEGER NOT NULL DEFAULT 1,
    created_at DATETIME,
    updated_at DATETIME,
    FOREIGN KEY (organisation_id) REFERENCES organisations(id),
    FOREIGN KEY (wildlife_conflict_incident_id) REFERENCES wildlife_conflict_incidents(id)
);

CREATE INDEX idx_problem_animal_controls_organisation_id ON problem_animal_controls (organisation_id);
CREATE INDEX idx_problem_animal_controls_wildlife_conflict_incident_id ON problem_animal_controls (wildlife_conflict_incident_id);

-- Poaching_incidents table
CREATE TABLE poaching_incidents (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    organisation_id INTEGER NOT NULL,
    title VARCHAR(255) NOT NULL,
    location TEXT,
    longitude DECIMAL(10,7),
    docket_number VARCHAR(255),
    docket_status VARCHAR(255),
    latitude DECIMAL(10,7),
    period INTEGER NOT NULL,
    date DATE NOT NULL,
    time TIME NOT NULL,
    created_at DATETIME,
    updated_at DATETIME,
    FOREIGN KEY (organisation_id) REFERENCES organisations(id)
);

CREATE INDEX idx_poaching_incidents_organisation_id ON poaching_incidents (organisation_id);

-- Poaching_incident_methods table
CREATE TABLE poaching_incident_methods (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    poaching_incident_id INTEGER NOT NULL,
    poaching_method_id INTEGER NOT NULL,
    created_at DATETIME,
    updated_at DATETIME,
    FOREIGN KEY (poaching_incident_id) REFERENCES poaching_incidents(id),
    FOREIGN KEY (poaching_method_id) REFERENCES poaching_methods(id)
);

CREATE INDEX idx_poaching_incident_methods_poaching_incident_id ON poaching_incident_methods (poaching_incident_id);
CREATE INDEX idx_poaching_incident_methods_poaching_method_id ON poaching_incident_methods (poaching_method_id);

-- Poaching_incident_species table
CREATE TABLE poaching_incident_species (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    poaching_incident_id INTEGER NOT NULL,
    species_id INTEGER NOT NULL,
    created_at DATETIME,
    updated_at DATETIME,
    FOREIGN KEY (poaching_incident_id) REFERENCES poaching_incidents(id),
    FOREIGN KEY (species_id) REFERENCES species(id)
);

CREATE INDEX idx_poaching_incident_species_poaching_incident_id ON poaching_incident_species (poaching_incident_id);
CREATE INDEX idx_poaching_incident_species_species_id ON poaching_incident_species (species_id);

-- Hunting_activities table
CREATE TABLE hunting_activities (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    organisation_id INTEGER NOT NULL,
    hunting_concession_id INTEGER NOT NULL,
    safari_id INTEGER,
    period VARCHAR(255),
    start_date DATE NOT NULL,
    end_date DATE,
    created_at DATETIME,
    updated_at DATETIME,
    FOREIGN KEY (organisation_id) REFERENCES organisations(id),
    FOREIGN KEY (hunting_concession_id) REFERENCES hunting_concessions(id)
);

CREATE INDEX idx_hunting_activities_organisation_id ON hunting_activities (organisation_id);
CREATE INDEX idx_hunting_activities_hunting_concession_id ON hunting_activities (hunting_concession_id);

-- Hunting_activity_professional_hunter_licenses table
CREATE TABLE hunting_activity_professional_hunter_licenses (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    hunting_activity_id INTEGER NOT NULL,
    license_number VARCHAR(255),
    hunter_name VARCHAR(255),
    created_at DATETIME,
    updated_at DATETIME,
    FOREIGN KEY (hunting_activity_id) REFERENCES hunting_activities(id)
);

CREATE INDEX idx_hunting_activity_professional_hunter_licenses_hunting_activity_id ON hunting_activity_professional_hunter_licenses (hunting_activity_id);

-- Hunting_activity_species table
CREATE TABLE hunting_activity_species (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    hunting_activity_id INTEGER NOT NULL,
    species_id INTEGER NOT NULL,
    off_take INTEGER NOT NULL,
    created_at DATETIME,
    updated_at DATETIME,
    FOREIGN KEY (hunting_activity_id) REFERENCES hunting_activities(id),
    FOREIGN KEY (species_id) REFERENCES species(id)
);

CREATE INDEX idx_hunting_activity_species_hunting_activity_id ON hunting_activity_species (hunting_activity_id);
CREATE INDEX idx_hunting_activity_species_species_id ON hunting_activity_species (species_id);