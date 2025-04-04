#\!/bin/bash
echo "Checking specific error patterns in the codebase..."

# Check for 'wildlifeConflictCreate' error
echo "Checking for wildlifeConflictCreate references:"
grep -r "wildlifeConflictCreate" lib

echo
echo "Checking for syncStatus parameter mismatches:"
grep -r "syncStatus:" lib/ui/screens/poaching

echo
echo "Checking for buttonText parameter mismatches:"
grep -r "buttonText:" lib/ui/screens/poaching

echo 
echo "Checking for title parameter in LocationMapView:"
grep -r "title:" --include="*.dart" lib | grep "LocationMapView"

echo
echo "Checking for onLocationSelected parameter:"
grep -r "onLocationSelected:" lib/ui/screens/poaching

echo
echo "Checking for getActiveOrganisationId method references:"
grep -r "getActiveOrganisationId" --include="*.dart" lib | grep -v "app_preferences.dart"

echo
echo "Build check complete."
