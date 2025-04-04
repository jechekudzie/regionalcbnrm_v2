import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:flutter_form_builder/flutter_form_builder.dart';
import 'package:form_builder_validators/form_builder_validators.dart';
import 'package:resource_africa/models/organisation_model.dart';
import 'package:resource_africa/repositories/hunting_activity_repository.dart';
import 'package:resource_africa/repositories/organisation_repository.dart';
import 'package:resource_africa/services/notification_service.dart';
import 'package:resource_africa/ui/widgets/location_picker.dart';

class HuntingConcessionCreateScreen extends StatefulWidget {
  const HuntingConcessionCreateScreen({super.key});

  @override
  State<HuntingConcessionCreateScreen> createState() => _HuntingConcessionCreateScreenState();
}

class _HuntingConcessionCreateScreenState extends State<HuntingConcessionCreateScreen> {
  final _formKey = GlobalKey<FormBuilderState>();
  
  final HuntingActivityRepository _repository = HuntingActivityRepository();
  final OrganisationRepository _organisationRepository = OrganisationRepository();
  final NotificationService _notificationService = Get.find<NotificationService>();
  
  final RxBool _isLoading = false.obs;
  final RxBool _isLoadingData = true.obs;
  final RxList<OrganisationDetail> _safariOperators = RxList<OrganisationDetail>([]);
  final RxInt _organisationId = 0.obs;
  final RxDouble _latitude = 0.0.obs;
  final RxDouble _longitude = 0.0.obs;
  
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
      
      // Load safari operators
      await _loadSafariOperators();
    } catch (e) {
      _notificationService.showSnackBar(
        'Failed to load form data. Please try again.',
        type: SnackBarType.error,
      );
    } finally {
      _isLoadingData.value = false;
    }
  }
  
  Future<void> _loadSafariOperators() async {
    try {
      // In a real app, you would have a repository method to fetch safari operators
      // For now, we'll use a mock list

    } catch (e) {
      // Handle error silently
    }
  }
  
  Future<void> _submitForm() async {
    if (_formKey.currentState?.saveAndValidate() ?? false) {
      final formData = _formKey.currentState!.value;
      
      _isLoading.value = true;
      
      try {
        // Create the hunting concession
        final createdConcession = await _repository.createHuntingConcession(
          _organisationId.value,
          formData['name'],
          formData['hectarage'] != null ? double.tryParse(formData['hectarage']) : null,
          formData['description'],
          double.parse(formData['latitude']),
          double.parse(formData['longitude']),
          formData['safari_id'],
        );
        
        _notificationService.showSnackBar(
          'Hunting concession created successfully',
          type: SnackBarType.success,
        );
        
        Get.back(result: createdConcession);
      } catch (e) {
        print('Error submitting form: $e');
        _notificationService.showSnackBar(
          'Failed to create hunting concession. Please try again.',
          type: SnackBarType.error,
        );
      } finally {
        _isLoading.value = false;
      }
    } else {
      // Show validation errors
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
        title: const Text('Create Hunting Concession'),
      ),
      body: Obx(() {
        if (_isLoadingData.value) {
          return const Center(child: CircularProgressIndicator());
        }
        
        return SingleChildScrollView(
          padding: const EdgeInsets.all(16.0),
          child: FormBuilder(
            key: _formKey,
            autovalidateMode: AutovalidateMode.onUserInteraction,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Basic Information
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
                        Text(
                          'Basic Information',
                          style: Theme.of(context).textTheme.titleLarge?.copyWith(
                                fontWeight: FontWeight.bold,
                              ),
                        ),
                        const SizedBox(height: 16),
                        
                        // Name field
                        FormBuilderTextField(
                          name: 'name',
                          decoration: const InputDecoration(
                            labelText: 'Name',
                            hintText: 'Enter hunting concession name',
                            border: OutlineInputBorder(),
                          ),
                          validator: FormBuilderValidators.compose([
                            FormBuilderValidators.required(),
                          ]),
                        ),
                        const SizedBox(height: 16),
                        
                        // Hectarage field
                        FormBuilderTextField(
                          name: 'hectarage',
                          decoration: const InputDecoration(
                            labelText: 'Hectarage',
                            hintText: 'Enter hectarage',
                            border: OutlineInputBorder(),
                          ),
                          keyboardType: TextInputType.number,
                          validator: FormBuilderValidators.compose([
                            FormBuilderValidators.numeric(),
                          ]),
                        ),
                        const SizedBox(height: 16),
                        
                        // Safari Operator
                        FormBuilderDropdown<int>(
                          name: 'safari_id',
                          decoration: const InputDecoration(
                            labelText: 'Safari Operator',
                            hintText: 'Select a safari operator',
                            border: OutlineInputBorder(),
                          ),
                          items: _safariOperators.map((operator) {
                            return DropdownMenuItem(
                              value: operator.id,
                              child: Text(operator.name),
                            );
                          }).toList(),
                        ),
                        const SizedBox(height: 16),
                        
                        // Description
                        FormBuilderTextField(
                          name: 'description',
                          decoration: const InputDecoration(
                            labelText: 'Description',
                            hintText: 'Enter description',
                            border: OutlineInputBorder(),
                          ),
                          maxLines: 3,
                        ),
                      ],
                    ),
                  ),
                ),
                const SizedBox(height: 16),
                
                // Location Information
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
                        Text(
                          'Location Information',
                          style: Theme.of(context).textTheme.titleLarge?.copyWith(
                                fontWeight: FontWeight.bold,
                              ),
                        ),
                        const SizedBox(height: 16),
                        
                        // Map Picker
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
                        const SizedBox(height: 16),
                        
                        // Location coordinates
                        Row(
                          children: [
                            Expanded(
                              child: FormBuilderTextField(
                                name: 'latitude',
                                decoration: const InputDecoration(
                                  labelText: 'Latitude',
                                  hintText: 'Enter latitude',
                                  border: OutlineInputBorder(),
                                ),
                                initialValue: _latitude.value.toString(),
                                keyboardType: TextInputType.number,
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
                                  hintText: 'Enter longitude',
                                  border: OutlineInputBorder(),
                                ),
                                initialValue: _longitude.value.toString(),
                                keyboardType: TextInputType.number,
                                validator: FormBuilderValidators.compose([
                                  FormBuilderValidators.required(),
                                  FormBuilderValidators.numeric(),
                                ]),
                                enabled: false,
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
                ),
                const SizedBox(height: 24),
                
                // Submit Button
                Obx(() => SizedBox(
                  width: double.infinity,
                  height: 50,
                  child: ElevatedButton(
                    onPressed: _isLoading.value ? null : _submitForm,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Theme.of(context).primaryColor,
                      foregroundColor: Colors.white,
                    ),
                    child: _isLoading.value
                        ? const CircularProgressIndicator(valueColor: AlwaysStoppedAnimation<Color>(Colors.white))
                        : const Text('Create Hunting Concession'),
                  ),
                )),
              ],
            ),
          ),
        );
      }),
    );
  }
}