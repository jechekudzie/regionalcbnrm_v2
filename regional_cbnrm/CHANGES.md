# Changes Made to Fix Build Issues

This document outlines the changes made to fix build issues in the Resource Africa mobile app.

## 1. Updated Dependencies

In `pubspec.yaml`:

- Added missing required dependencies:
  - `pretty_dio_logger: ^1.3.1` - For better API debugging
  - `device_info_plus: ^9.0.3` - For device identification
  - `flutter_local_notifications: ^15.1.1` - For local notifications

- Changed SDK requirement from `^3.7.0` to `'>=3.0.0 <4.0.0'` for better compatibility

## 2. Android Configuration

Modified `android/app/build.gradle.kts`:

- Updated app ID to proper format: `com.resource_africa.app`
- Set minimum SDK version to 23 (Android 6.0) to support all required packages
- Added dependencies section for native dependencies

## 3. iOS Configuration

Updated `ios/Runner/Info.plist`:

- Added required permission descriptions:
  - Location permissions
  - Camera access
  - Photo library access
  - Microphone access

- Added background modes configuration
- Added App Transport Security settings to allow HTTP connections

## 4. Documentation

- Updated README.md with:
  - Updated setup instructions
  - Added detailed troubleshooting steps
  - Instructions for Flutter installation
  - Updated SDK version requirements

## Running the App

1. Install Flutter SDK following instructions at [flutter.dev](https://flutter.dev/docs/get-started/install)

2. Add Flutter to your PATH:
   ```
   export PATH="$PATH:`pwd`/flutter/bin"
   ```

3. Install dependencies:
   ```
   cd /Applications/XAMPP/xamppfiles/htdocs/regionalcbnrm_v2/resource_africa
   flutter pub get
   ```

4. Run the app:
   ```
   flutter run
   ```

## Troubleshooting

If you encounter issues:

1. Check Flutter installation with `flutter doctor`
2. Clean build files with `flutter clean` followed by `flutter pub get`
3. For iOS, run `pod install` in the `ios` directory