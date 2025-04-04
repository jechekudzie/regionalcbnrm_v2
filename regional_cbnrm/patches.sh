#\!/bin/bash
echo "Applying fixes to get the app to run"

echo "1. Fixing location picker in Problem Animal Control screen"
sed -i.bak 's/LocationPicker\>/SimpleLocationPicker/g' lib/ui/screens/problem_animal_control/problem_animal_control_create_screen.dart

echo "2. Fixing location picker in Wildlife Conflict screen"
sed -i.bak 's/import .*location_picker.dart/import "package:resource_africa\/ui\/widgets\/simple_location_picker.dart"/g' lib/ui/screens/wildlife_conflict/wildlife_conflict_create_screen.dart
sed -i.bak 's/LocationPicker\>/SimpleLocationPicker/g' lib/ui/screens/wildlife_conflict/wildlife_conflict_create_screen.dart

echo "3. Removing any direct Geolocator references"
sed -i.bak 's/import .*geolocator.dart.*//g' lib/ui/screens/problem_animal_control/problem_animal_control_create_screen.dart
sed -i.bak 's/import .*geolocator.dart.*//g' lib/ui/screens/wildlife_conflict/wildlife_conflict_create_screen.dart

echo "Fixes applied. Try running the app now with: flutter run"
