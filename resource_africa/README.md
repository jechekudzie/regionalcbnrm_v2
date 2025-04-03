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

## License

This project is licensed under the proprietary license of Resource Africa.

## Credits

Developed for Resource Africa to support wildlife conservation and management efforts.