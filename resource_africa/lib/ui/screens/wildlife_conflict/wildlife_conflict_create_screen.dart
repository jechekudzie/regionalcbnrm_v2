import 'package:flutter/material.dart';
import 'package:resource_africa/ui/widgets/location_picker.dart';
import 'package:get/get.dart';
import 'package:flutter_form_builder/flutter_form_builder.dart';
import 'package:form_builder_validators/form_builder_validators.dart';

import 'package:intl/intl.dart';
import 'package:resource_africa/models/wildlife_conflict_model.dart';
import 'package:resource_africa/repositories/wildlife_conflict_repository.dart';
import 'package:resource_africa/repositories/organisation_repository.dart';
import 'package:resource_africa/services/notification_service.dart';

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
  final RxBool _isLoadingData = true.obs;
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
        'Error',
        'Failed to load form data. Please try again.',
        SnackBarType.error,
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
    }
  }
  
  Future<void> _loadSpecies() async {
    try {
      final species = await _repository.getSpecies();
      _species.value = species;
    } catch (e) {
      // Handle error
    }
  }
  
  
  Future<void> _submitForm() async {
    if (_formKey.currentState?.saveAndValidate() ?? false) {
      final formData = _formKey.currentState!.value;
      
      _isLoading.value = true;
      
      try {
        final incident = WildlifeConflictIncident(
          organisationId: _organisationId.value,
          title: formData['title'],
          date: formData['date'],
          time: DateFormat('HH:mm').format(formData['time']),
          latitude: double.parse(formData['latitude']),
          longitude: double.parse(formData['longitude']),
          description: formData['description'],
          conflictTypeId: formData['conflict_type_id'],
          //speciesIds: List<int>.from(formData['species_id']),
        );
        
        final createdIncident = await _repository.createIncident(incident);
        
        _notificationService.showSnackBar(
          'Success',
          'Wildlife conflict incident reported successfully',
          SnackBarType.success,
        );
        
        Get.back(result: createdIncident);
      } catch (e) {
        _notificationService.showSnackBar(
          'Error',
          'Failed to report incident. Please try again.',
          SnackBarType.error,
        );
      } finally {
        _isLoading.value = false;
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Report Wildlife Conflict'),
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
                FormBuilderDropdown(
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
                const SizedBox(height: 16),

                // Species checkboxes
                SizedBox(
                  height: _species.isNotEmpty ? ((_species.length / 3).ceil() * 50) : 50, // Adjust height based on the number of species
                  child: GridView.builder(
                    physics: const NeverScrollableScrollPhysics(),
                    gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                      crossAxisCount: 2,
                      childAspectRatio: 4,
                    ),
                    itemCount: _species.length,
                    itemBuilder: (context, index) {
                      final species = _species[index];
                      return FormBuilderCheckbox(
                        name: 'species_id_$index',
                        title: Text(species.name),
                        controlAffinity: ListTileControlAffinity.leading,
                      );
                    },
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
                          border: OutlineInputBorder(),
                          prefixIcon: Icon(Icons.location_on),
                        ),
                        initialValue: _latitude.value.toString(),
                        validator: FormBuilderValidators.compose([
                          FormBuilderValidators.required(),
                          FormBuilderValidators.numeric(),
                        ]),
                        enabled: false,
                      ),
                    ),
                    const SizedBox(width: 16),
                    Expanded(
                      child: FormBuilderTextField(
                        name: 'longitude',
                        decoration: const InputDecoration(
                          labelText: 'Longitude',
                          border: OutlineInputBorder(),
                          prefixIcon: Icon(Icons.location_on),
                        ),
                        initialValue: _longitude.value.toString(),
                        validator: FormBuilderValidators.compose([
                          FormBuilderValidators.required(),
                          FormBuilderValidators.numeric(),
                        ]),
                        enabled: false,
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
                    hintText: 'Describe the incident in detail',
                    border: OutlineInputBorder(),
                    prefixIcon: Icon(Icons.description),
                  ),
                  maxLines: 5,
                  validator: FormBuilderValidators.compose([
                    FormBuilderValidators.required(),
                    FormBuilderValidators.maxLength(500),
                  ]),
                ),
                const SizedBox(height: 24),
                
                // Submit button
                SizedBox(
                  width: double.infinity,
                  height: 50,
                  child: ElevatedButton.icon(
                    onPressed: _isLoading.value ? null : _submitForm,
                    icon: const Icon(Icons.save),
                    label: _isLoading.value
                        ? const SizedBox(
                            width: 24,
                            height: 24,
                            child: CircularProgressIndicator(
                              valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                              strokeWidth: 2,
                            ),
                          )
                        : const Text(
                            'Submit Report',
                            style: TextStyle(fontSize: 16),
                          ),
                  ),
                ),
                const SizedBox(height: 32),
              ],
            ),
          ),
        );
      }),
    );
  }
}