plugins {
    id("com.android.application")
    id("kotlin-android")
    // The Flutter Gradle Plugin must be applied after the Android and Kotlin Gradle plugins.
    id("dev.flutter.flutter-gradle-plugin")
}

android {
    namespace = "com.resource_africa.app"
    compileSdk = 35
    ndkVersion = "27.0.12077973"

    compileOptions {
        isCoreLibraryDesugaringEnabled = true
        sourceCompatibility = JavaVersion.VERSION_11
        targetCompatibility = JavaVersion.VERSION_11
    }

    kotlinOptions {
        jvmTarget = JavaVersion.VERSION_11.toString()
    }

    defaultConfig {
        applicationId = "com.resourceafrica.app"
        minSdk = 23 // Updated to support all required packages
        targetSdk = 35
        versionCode = flutter.versionCode
        versionName = flutter.versionName
    }

    buildTypes {
        release {
            // TODO: Add your own signing config for the release build.
            // Signing with the debug keys for now, so `flutter run --release` works.
            signingConfig = signingConfigs.getByName("debug")
        }
    }
    repositories {
        google()
        mavenCentral()
        maven {
            setUrl("https://storage.googleapis.com/download.flutter.io")
        }
    }
}

dependencies {
    // Add any native Android dependencies here
    coreLibraryDesugaring("com.android.tools:desugar_jdk_libs:2.1.5")
    implementation("javax.annotation:javax.annotation-api:1.3.2")
}

flutter {
    source = "../.."
}
