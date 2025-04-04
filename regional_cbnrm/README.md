# Resource Africa Mobile App

This is a companion mobile application for the Resource Africa Wildlife Management System. The app provides field users with the ability to record and manage wildlife data, even in offline environments, with synchronization capabilities once connectivity is restored.

## Features

- **Organisation-based Authentication**: Secure login with organisation-based permissions
- **Offline Functionality**: Full offline data capture with synchronization
- **Wildlife Conflict Management**: Record and manage human-wildlife conflict incidents
- **Problem Animal Control**: Track and manage responses to problem animals
- **Poaching Incident Recording**: Document poaching incidents and poachers
- **Hunting Activity Management**: Manage hunting activities and quota allocations
- **Synchronization**: Bidirectional data synchronization with the web platform

## Architecture

### Backend (Laravel API)

- RESTful API built using Laravel
- Passport for OAuth2 authentication
- API versioning for future expansion
- Comprehensive validation and error handling

### Mobile App (Flutter)

- Cross-platform application built with Flutter
- GetX for state management and dependency injection
- SQLite for local data storage
- Offline-first architecture with synchronization capabilities

## Getting Started

### Prerequisites

- Flutter SDK (version 3.0.0 or higher)
- Android Studio / Xcode for device emulation
- Laravel development environment for the backend

### Installation

#### Laravel API Setup

1. The Laravel API is part of the main web application. Ensure the web application is properly configured.
2. Make sure Passport is installed and configured:

```bash
php artisan passport:install
```

#### Flutter App Setup

1. Install Flutter SDK by following the instructions at [flutter.dev](https://flutter.dev/docs/get-started/install)

2. Ensure Flutter is properly added to your PATH:
   ```
   export PATH="$PATH:`pwd`/flutter/bin"
   ```

3. Navigate to the mobile app directory:

```bash
cd resource_africa
```

4. Install dependencies:

```bash
flutter pub get
```

5. Update the API base URL if needed:
   - Open `/lib/utils/app_constants.dart`
   - Change the `baseUrl` to point to your Laravel installation

6. Run the app:

```bash
flutter run
```

## Mobile App Implementation

### Key Components

#### Authentication Flow
1. User logs in with email and password
2. API returns user data and OAuth token
3. Token is stored securely in shared preferences
4. User selects an organization to work with
5. All subsequent API requests include the token

#### Data Models
The app includes comprehensive data models for all entities:
- `User` - User information and roles
- `Organisation` - Organization structure
- `WildlifeConflictIncident` - Wildlife conflict data
- `ProblemAnimalControl` - Animal control measures
- `PoachingIncident` - Poaching incident details
- `HuntingActivity` - Hunting activity records

#### Repository Pattern
Each module has a dedicated repository that handles:
- API data fetching
- Local database operations
- Data synchronization logic
- Error handling

#### Services
- `AuthService` - Handles authentication logic
- `ConnectivityService` - Monitors network connectivity
- `SyncService` - Manages data synchronization
- `NotificationService` - Handles local notifications

#### Offline Capabilities
The app implements robust offline data handling:
- SQLite database mirrors the server structure
- Changes are tracked in a sync queue
- Background sync when connectivity is restored
- Conflict resolution based on timestamps

#### UI Features
- Material Design with custom styling
- Responsive layouts for different device sizes 
- Location picking with map integration
- File attachments (images) with offline storage
- Form validation for data integrity

### Database Schema

The SQLite database includes tables for all major entities:

```
- user
- organisations
- wildlife_conflict_incidents
- wildlife_conflict_outcomes
- problem_animal_controls
- poaching_incidents
- poaching_incident_species
- poaching_incident_methods
- poachers
- hunting_activities
- hunting_activity_species
- professional_hunter_licenses
- sync_queue
```

### Utility Scripts

The repository includes several helper scripts:
- `build_check.sh` - Verify build configuration
- `check_build.sh` - Run pre-build checks
- `final_fixes.sh` - Apply final fixes before build
- `run_app.sh` - Run the app with clean environment

## Troubleshooting

If you encounter build issues:

1. Ensure Flutter is installed and in your PATH:
   ```
   flutter doctor
   ```

2. Check for missing dependencies:
   ```
   flutter pub get
   ```

3. Clear build cache if needed:
   ```
   flutter clean
   flutter pub get
   ```

4. If iOS build fails, try opening Xcode project and resolving any signing issues:
   ```
   cd ios
   pod install
   open Runner.xcworkspace
   ```

5. Debug console is available in the app (tap 7 times on the logo at splash screen)

## Offline Functionality

The app implements a robust offline data management system:

1. All data is stored locally in SQLite database
2. Changes made offline are queued for synchronization
3. When connectivity is restored, data is automatically synced with the server
4. Conflict resolution prioritizes server data for conflicting records

## Project Structure

- `/lib/core`: Core functionality (API service, database, exceptions)
- `/lib/models`: Data models for the application
- `/lib/repositories`: Data access layer
- `/lib/services`: Business logic and services
- `/lib/ui`: User interface components
  - `/screens`: Application screens
  - `/widgets`: Reusable UI components
- `/lib/utils`: Utility classes and constants

## Adding New Features

To extend the application with new features:

1. Create model class in `/lib/models/`
2. Create repository in `/lib/repositories/`
3. Add screens in `/lib/ui/screens/`
4. Update routes in `/lib/utils/app_routes.dart`
5. Add to dashboard in `/lib/ui/screens/dashboard/dashboard_screen.dart`

## License

This project is licensed under the proprietary license of Resource Africa.

## Credits

Developed for Resource Africa to support wildlife conservation and management efforts.
