#\!/bin/bash
echo "Applying final fixes to specific files..."

# Fix getActiveOrganisationId issue
sed -i.bak 's/getActiveOrganisationId/getSelectedOrganisation/g' lib/ui/screens/poaching/poaching_incident_list_screen.dart

# Fix location picker issue
sed -i.bak 's/onLocationSelected/onLocationChanged/g' lib/ui/screens/poaching/poaching_incident_create_screen.dart

echo "Final fixes applied. Try running the app now."
