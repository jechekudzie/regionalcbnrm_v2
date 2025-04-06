
import 'package:flutter/material.dart';
import 'package:regional_cbnrm/ui/widgets/location_picker.dart';
import 'package:get/get.dart';
import 'package:flutter_form_builder/flutter_form_builder.dart';
import 'package:form_builder_validators/form_builder_validators.dart';

import 'package:intl/intl.dart';
import 'package:regional_cbnrm/models/wildlife_conflict_model.dart';
import 'package:regional_cbnrm/repositories/wildlife_conflict_repository.dart';
import 'package:regional_cbnrm/repositories/organisation_repository.dart';
import 'package:regional_cbnrm/services/notification_service.dart';

class WildlifeConflictCreateScreen extends StatefulWidget {
  const WildlifeConflictCreateScreen({super.key});

  @override
  State<WildlifeConflictCreateScreen> createState() => _WildlifeConflictCreateScreenState();
}

class _WildlifeConflictCreateScreenState extends State<WildlifeConflictCreateScreen> {
  final _formKey = GlobalKey<FormBuilderState>();
  
  final WildlifeConflictRepository _repository = WildlifeConflictRepository();
  final OrganisationRepository _organisationRepository = OrganisationRepository();
  final NotificationService _notificationService = Get.find<NotificationService>();
  
  final RxBool _isLoading = false.obs;
  final RxBool _isLoadingData = true.obs; // Start with true to show loading initially
  final RxList<ConflictType> _conflictTypes = RxList<ConflictType>([]);
  final RxList<Species> _species = RxList<Species>([]);
  final RxDouble _latitude = 0.0.obs;
  final RxDouble _longitude = 0.0.obs;
  final RxInt _organisationId = 0.obs;
  
  @override
  void initState() {
    super.initState();
    _initForm();
  }
  
  Future<void> _initForm() async {
    _isLoadingData.value = true;
    
    try {
      // Load organisation
      final organisation = await _organisationRepository.getSelectedOrganisation();
      if (organisation != null) {
        _organisationId.value = organisation.id;
      }
      
      // Load conflict types and species in parallel
      await Future.wait([
        _loadConflictTypes(),
        _loadSpecies(),
      ]);
    } catch (e) {
      _notificationService.showSnackBar(
        'Failed to load form data. Please try again.',
        type: SnackBarType.error,
      );
    } finally {
      _isLoadingData.value = false;
    }
  }
  
  Future<void> _loadConflictTypes() async {
    try {
      final conflictTypes = await _repository.getConflictTypes();
      _conflictTypes.value = conflictTypes;
    } catch (e) {
      // Handle error
      print('Error loading conflict types: $e');
       _notificationService.showSnackBar(
        'Error loading conflict types. Please try again.',
        type: SnackBarType.error,
      );
    }
  }
  
  Future<void> _loadSpecies() async {
    try {
      _notificationService.showSnackBar(
        'Loading species data...',
        type: SnackBarType.info,
      );
      
      // Fetch species from the repository using organisation ID when available
      final species = await _repository.getSpecies(
        organisationId: _organisationId.value > 0 ? _organisationId.value : null
      );
      
      if (species.isEmpty) {
        _notificationService.showSnackBar(
          'No species found. Please check your connection.',
          type: SnackBarType.warning,
        );
      } else {
        // Sort the species alphabetically for better usability
        species.sort((a, b) => a.name.compareTo(b.name));
        _species.value = species;
      }
    } catch (e) {
      print('Error loading species: $e');
      _notificationService.showSnackBar(
        'Error loading species. Please try again.',
        type: SnackBarType.error,
      );
    }
  }
  
  Future<void> _submitForm() async {
    if (_formKey.currentState?.saveAndValidate() ?? false) {
      final formData = _formKey.currentState!.value;
      _isLoading.value = true;
      try {
        // Collect all selected species IDs from checkboxes
        final List<Species> selectedSpecies = [];
        for (int i = 0; i < _species.length; i++) {
          // Use the correct checkbox name format
          if (formData['species_id_$i'] == true) { 
            selectedSpecies.add(_species[i]);
          }
        }

        // Validate if at least one species is selected
        if (selectedSpecies.isEmpty) {
           _notificationService.showSnackBar(
            'Please select at least one species involved.',
            type: SnackBarType.warning,
          );
          _isLoading.value = false; // Stop loading
          return; // Prevent further execution
        }

        // Create the incident with all the data
        final incident = WildlifeConflictIncident(
          organisationId: _organisationId.value,
          title: formData['title'] as String,
          date: formData['date'] as DateTime,
          time: DateFormat('HH:mm').format(formData['time'] as DateTime),
          latitude: double.parse(formData['latitude'] as String),
          longitude: double.parse(formData['longitude'] as String),
          description: formData['description'] as String,
          conflictTypeId: formData['conflict_type_id'] as int,
          period: DateTime.now().year,
          // Use the ID of the first selected species, or null if none (though validated above)
          speciesId: selectedSpecies.isNotEmpty ? selectedSpecies.first.id : null, 
          speciesList: selectedSpecies,
        );
          
        // Send to the repository (repository handles API conversion)
        final createdIncident = await _repository.createIncident(incident);
          
        // Sync local database and repository
        await _repository.getIncidents(_organisationId.value);

        // Clear the form
        _formKey.currentState?.reset();

        // Show success snackbar
        _notificationService.showSnackBar(
          'Record added successfully',
          type: SnackBarType.success,
        );
            
        Get.back(result: createdIncident);
      } catch (e) {
        print('Error submitting form: $e');
        // Check for specific error types if needed
        String errorMessage = 'Failed to report incident. Please try again.';
        if (e.toString().contains("type 'Null' is not a subtype of type 'int'")) {
           errorMessage = 'Failed to report incident: A required number field is missing.';
        }
        _notificationService.showSnackBar(
          errorMessage,
          type: SnackBarType.error,
        );
      } finally {
        _isLoading.value = false;
      }
    } else {
      // Show specific validation errors
      _notificationService.showSnackBar(
        'Please fill in all required fields correctly',
        type: SnackBarType.error,
      );
    }
  }

@override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Report Wildlife Conflict'),
        actions: [
          // Add refresh button to reload data
          IconButton(
            icon: const Icon(Icons.refresh),
            tooltip: 'Refresh species data',
            onPressed: () async {
              // Show loading indicator
              _isLoadingData.value = true;
              try {
                // Force refresh of species data
                await _loadSpecies();
              } finally {
                _isLoadingData.value = false;
              }
            },
          ),
        ],
      ),
      body: Obx(() {
        if (_isLoadingData.value) {
          return const Center(child: CircularProgressIndicator());
        }
        
        return SingleChildScrollView(
          padding: const EdgeInsets.all(16.0),
          child: FormBuilder(
            key: _formKey,
            autovalidateMode: AutovalidateMode.disabled,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Title field
                FormBuilderTextField(
                  name: 'title',
                  decoration: const InputDecoration(
                    labelText: 'Title',
                    hintText: 'Enter a title for this incident',
                    border: OutlineInputBorder(),
                    prefixIcon: Icon(Icons.title),
                  ),
                  validator: FormBuilderValidators.compose([
                    FormBuilderValidators.required(),
                    FormBuilderValidators.maxLength(100),
                  ]),
                ),
                const SizedBox(height: 16),
                
                // Date and time fields
                Row(
                  children: [
                    Expanded(
                      child: FormBuilderDateTimePicker(
                        name: 'date',
                        decoration: const InputDecoration(
                          labelText: 'Date',
                          border: OutlineInputBorder(),
                          prefixIcon: Icon(Icons.calendar_today),
                        ),
                        inputType: InputType.date,
                        format: DateFormat('dd/MM/yyyy'),
                        initialValue: DateTime.now(),
                        validator: FormBuilderValidators.compose([
                          FormBuilderValidators.required(),
                        ]),
                      ),
                    ),
                    const SizedBox(width: 16),
                    Expanded(
                      child: FormBuilderDateTimePicker(
                        name: 'time',
                        decoration: const InputDecoration(
                          labelText: 'Time',
                          border: OutlineInputBorder(),
                          prefixIcon: Icon(Icons.access_time),
                        ),
                        inputType: InputType.time,
                        format: DateFormat('HH:mm'),
                        initialValue: DateTime.now(),
                        validator: FormBuilderValidators.compose([
                          FormBuilderValidators.required(),
                        ]),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 16),
                
                // Conflict type dropdown
                FormBuilderDropdown<int>(
                  name: 'conflict_type_id',
                  decoration: const InputDecoration(
                    labelText: 'Conflict Type',
                    hintText: 'Select conflict type',
                    border: OutlineInputBorder(),
                    prefixIcon: Icon(Icons.warning_amber_rounded),
                  ),
                  items: _conflictTypes.map((type) {
                    return DropdownMenuItem(
                      value: type.id,
                      child: Text(type.name),
                    );
                  }).toList(),
                  validator: FormBuilderValidators.compose([
                    FormBuilderValidators.required(),
                  ]),
                ),
                  // Display message if no species are available
                if (_species.isEmpty)
                  Container(
                    margin: const EdgeInsets.only(top: 16, bottom: 16), // Added top margin
                    padding: const EdgeInsets.all(12),
                    decoration: BoxDecoration(
                      color: Colors.red.shade50,
                      borderRadius: BorderRadius.circular(8),
                      border: Border.all(color: Colors.red.shade200),
                    ),
                    child: Row(
                      children: [
                        Icon(Icons.warning_amber, color: Colors.red.shade800),
                        const SizedBox(width: 8),
                        const Expanded(
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                'No species available',
                                style: TextStyle(
                                  fontWeight: FontWeight.bold,
                                  color: Colors.red,
                                ),
                              ),
                              SizedBox(height: 4),
                              Text(
                                'Tap the refresh button in the app bar to try loading species again.',
                                style: TextStyle(fontSize: 12),
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ),
                
                const SizedBox(height: 16),
                
                // Species checkboxes for multiple selection
                Card(
                  elevation: 1,
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(12),
                    side: BorderSide(color: Colors.grey.shade300),
                  ),
                  child: Padding(
                    padding: const EdgeInsets.all(16),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          children: [
                            Icon(Icons.pest_control, color: Colors.brown.shade800, size: 18),
                            const SizedBox(width: 8),
                            const Text(
                              'Select All Species Involved',
                              style: TextStyle(
                                fontSize: 16,
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 4),
                        Text(
                          'Check all species that were observed in this incident',
                          style: TextStyle(
                            fontSize: 13,
                            color: Colors.grey.shade700,
                          ),
                        ),
                        const SizedBox(height: 12),
                        Divider(color: Colors.grey.shade300),
                        const SizedBox(height: 8),
                        
                        // Enhanced grid layout with scrollable content
                        GridView.builder(
                          shrinkWrap: true,
                          physics: const NeverScrollableScrollPhysics(),
                          gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                            crossAxisCount: 2,
                            childAspectRatio: 3.5,
                            mainAxisSpacing: 8,
                            crossAxisSpacing: 8,
                          ),
                          itemCount: _species.length,
                          itemBuilder: (context, index) {
                            final species = _species[index];
                            // Create a unique key for the FormBuilderCheckbox
                            final String checkboxName = 'species_id_$index'; // Corrected name
                            
                            return Container(
                              decoration: BoxDecoration(
                                borderRadius: BorderRadius.circular(8),
                                border: Border.all(color: Colors.grey.shade200), // Simplified border
                                color: Colors.transparent,
                              ),
                              margin: const EdgeInsets.only(bottom: 2),
                              child: FormBuilderCheckbox(
                                name: checkboxName,
                                title: Row(
                                  children: [
                                    Expanded(
                                      child: Text(
                                        species.name,
                                        style: const TextStyle( // Simplified style
                                          fontSize: 14,
                                          color: Colors.black87,
                                        ),
                                        overflow: TextOverflow.ellipsis,
                                      ),
                                    ),
                                  ],
                                ),
                                // initialValue: false, // Let the form handle initial state
                                decoration: const InputDecoration(
                                  contentPadding: EdgeInsets.zero,
                                  border: InputBorder.none,
                                ),
                                controlAffinity: ListTileControlAffinity.leading,
                              ),
                            );
                          },
                        ),
                      ],
                    ),
                  ),
                ),
                const SizedBox(height: 16),
                
                // Location fields
                const Text(
                  'Location',
                  style: TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                const SizedBox(height: 8),
                
                LocationPicker(
                  initialLatitude: _latitude.value,
                  initialLongitude: _longitude.value,
                  onLocationChanged: (lat, lng) {
                    _latitude.value = lat;
                    _longitude.value = lng;
                    _formKey.currentState?.fields['latitude']?.didChange(lat.toString());
                    _formKey.currentState?.fields['longitude']?.didChange(lng.toString());
                  },
                ),
                const SizedBox(height: 8),
                
                Row(
                  children: [
                    Expanded(
                      child: FormBuilderTextField(
                        name: 'latitude',
                        decoration: const InputDecoration(
                          labelText: 'Latitude',
                          hintText: 'Enter latitude',
                          border: OutlineInputBorder(),
                          prefixIcon: Icon(Icons.location_on),
                        ),
                        validator: FormBuilderValidators.compose([
                          FormBuilderValidators.required(),
                          FormBuilderValidators.numeric(),
                        ]),
                      ),
                    ),
                    const SizedBox(width: 16),
                    Expanded(
                      child: FormBuilderTextField(
                        name: 'longitude',
                        decoration: const InputDecoration(
                          labelText: 'Longitude',
                          hintText: 'Enter longitude',
                          border: OutlineInputBorder(),
                          prefixIcon: Icon(Icons.location_on),
                        ),
                        validator: FormBuilderValidators.compose([
                          FormBuilderValidators.required(),
                          FormBuilderValidators.numeric(),
                        ]),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 16),
                
                // Description field
                FormBuilderTextField(
                  name: 'description',
                  decoration: const InputDecoration(
                    labelText: 'Description',
                    hintText: 'Enter a description for this incident',
                    border: OutlineInputBorder(),
                    prefixIcon: Icon(Icons.description),
                  ),
                  maxLines: 3,
                  validator: FormBuilderValidators.compose([
                    FormBuilderValidators.required(),
                    FormBuilderValidators.maxLength(500),
                  ]),
                ),
                const SizedBox(height: 24),
                
                // Submit button
                SizedBox( // Ensure button takes full width
                  width: double.infinity,
                  child: ElevatedButton(
                    style: ElevatedButton.styleFrom(
                      padding: const EdgeInsets.symmetric(vertical: 16), // Make button taller
                    ),
                    onPressed: _isLoading.value ? null : _submitForm,
                    child: Obx(() => _isLoading.value
                      ? const SizedBox(
                          width: 24,
                          height: 24,
                          child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white),
                        )
                      : const Text('Report Incident'),
                    ),
                  ),
                ),
              ],
            ),
          ),
        );
      }),
    );
  }
}
