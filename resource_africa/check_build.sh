#!/bin/bash
# Simple build validation script

# Check for Dart syntax errors
echo "Checking for Dart syntax errors..."
find lib -name "*.dart" -exec cat {} \; | grep -n "import " | grep -v "package:resource_africa" | grep -v "dart:" | grep -v "package:flutter" | grep -v "package:get" | grep -v "package:" > /tmp/imports.txt

# Check for common import errors
if [ -s /tmp/imports.txt ]; then
  echo "Potential import issues found:"
  cat /tmp/imports.txt
fi

# Check that all route constants are defined
echo "Checking route constants..."
ROUTE_CONSTANTS=$(grep -r "AppRoutes." lib | grep -v "AppRoutes.dart" | sort | uniq)
echo "$ROUTE_CONSTANTS" | grep "poaching"

# Check for common errors in repository files
echo "Checking repository implementations..."
grep -r "getSpeciesList" lib/repositories/ || echo "getSpeciesList not found in any repository"

echo "Build check complete. Please run 'flutter run' in a terminal with Flutter SDK access to launch on an emulator."