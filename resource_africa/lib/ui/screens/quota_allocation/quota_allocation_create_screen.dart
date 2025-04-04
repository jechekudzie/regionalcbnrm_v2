import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:flutter_form_builder/flutter_form_builder.dart';
import 'package:form_builder_validators/form_builder_validators.dart';
import 'package:intl/intl.dart';

import 'package:resource_africa/models/wildlife_conflict_model.dart';
import 'package:resource_africa/repositories/hunting_activity_repository.dart';
import 'package:resource_africa/repositories/organisation_repository.dart';
import 'package:resource_africa/repositories/wildlife_conflict_repository.dart';
import 'package:resource_africa/services/notification_service.dart';

class QuotaAllocationCreateScreen extends StatefulWidget {
  const QuotaAllocationCreateScreen({super.key});

  @override
  State<QuotaAllocationCreateScreen> createState() => _QuotaAllocationCreateScreenState();
}

class _QuotaAllocationCreateScreenState extends State<QuotaAllocationCreateScreen> {
  final _formKey = GlobalKey<FormBuilderState>();
  
  final HuntingActivityRepository _repository = HuntingActivityRepository();
  final WildlifeConflictRepository _wildlifeRepository = WildlifeConflictRepository();
  final OrganisationRepository _organisationRepository = OrganisationRepository();
  final NotificationService _notificationService = Get.find<NotificationService>();
  
  final RxBool _isLoading = false.obs;
  final RxBool _isLoadingData = true.obs;
  final RxList<Species> _species = RxList<Species>([]);
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
      
      // Load species
      await _loadSpecies();
    } catch (e) {
      _notificationService.showSnackBar(
        'Failed to load form data. Please try again.',
        type: SnackBarType.error,
      );
    } finally {
      _isLoadingData.value = false;
    }
  }
  
  Future<void> _loadSpecies() async {
    try {
      final species = await _wildlifeRepository.getSpecies(
        organisationId: _organisationId.value > 0 ? _organisationId.value : null
      );
      // Sort alphabetically
      species.sort((a, b) => a.name.compareTo(b.name));
      _species.value = species;
    } catch (e) {
      print('Error loading species: $e');
    }
  }
  
  Future<void> _submitForm() async {
    if (_formKey.currentState?.saveAndValidate() ?? false) {
      final formData = _formKey.currentState!.value;
      
      _isLoading.value = true;
      
      try {
        // Extract data from form
        final speciesId = formData['species_id'];
        final huntingQuota = int.parse(formData['hunting_quota'].toString());
        final rationalKillingQuota = int.parse(formData['rational_killing_quota'].toString());
        final startDate = formData['start_date'];
        final endDate = formData['end_date'];
        final notes = formData['notes'];
        
        // Create the quota allocation
        final createdAllocation = await _repository.createQuotaAllocation(
          _organisationId.value,
          speciesId,
          huntingQuota,
          rationalKillingQuota,
          startDate,
          endDate,
          notes,
        );
        
        _notificationService.showSnackBar(
          'Quota allocation created successfully',
          type: SnackBarType.success,
        );
        
        Get.back(result: createdAllocation);
      } catch (e) {
        print('Error submitting form: $e');
        _notificationService.showSnackBar(
          'Failed to create quota allocation. Please try again.',
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
        title: const Text('Create Quota Allocation'),
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
                          'Quota Information',
                          style: Theme.of(context).textTheme.titleLarge?.copyWith(
                                fontWeight: FontWeight.bold,
                              ),
                        ),
                        const SizedBox(height: 16),
                        
                        // Species Dropdown
                        FormBuilderDropdown<int>(
                          name: 'species_id',
                          decoration: const InputDecoration(
                            labelText: 'Species',
                            hintText: 'Select a species',
                            border: OutlineInputBorder(),
                          ),
                          items: _species.map((species) {
                            return DropdownMenuItem(
                              value: species.id,
                              child: Text(species.name),
                            );
                          }).toList(),
                          validator: FormBuilderValidators.compose([
                            FormBuilderValidators.required(),
                          ]),
                        ),
                        const SizedBox(height: 16),
                        
                        // Quota Amounts
                        Row(
                          children: [
                            Expanded(
                              child: FormBuilderTextField(
                                name: 'hunting_quota',
                                decoration: const InputDecoration(
                                  labelText: 'Hunting Quota',
                                  hintText: 'Enter hunting quota',
                                  border: OutlineInputBorder(),
                                ),
                                keyboardType: TextInputType.number,
                                initialValue: '0',
                                validator: FormBuilderValidators.compose([
                                  FormBuilderValidators.required(),
                                  FormBuilderValidators.numeric(),
                                  FormBuilderValidators.min(0),
                                ]),
                              ),
                            ),
                            const SizedBox(width: 16),
                            Expanded(
                              child: FormBuilderTextField(
                                name: 'rational_killing_quota',
                                decoration: const InputDecoration(
                                  labelText: 'Problem Animal Control Quota',
                                  hintText: 'Enter PAC quota',
                                  border: OutlineInputBorder(),
                                ),
                                keyboardType: TextInputType.number,
                                initialValue: '0',
                                validator: FormBuilderValidators.compose([
                                  FormBuilderValidators.required(),
                                  FormBuilderValidators.numeric(),
                                  FormBuilderValidators.min(0),
                                ]),
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
                ),
                const SizedBox(height: 16),
                
                // Date Information
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
                          'Validity Period',
                          style: Theme.of(context).textTheme.titleLarge?.copyWith(
                                fontWeight: FontWeight.bold,
                              ),
                        ),
                        const SizedBox(height: 16),
                        
                        // Date Range
                        Row(
                          children: [
                            Expanded(
                              child: FormBuilderDateTimePicker(
                                name: 'start_date',
                                decoration: const InputDecoration(
                                  labelText: 'Start Date',
                                  hintText: 'Select start date',
                                  border: OutlineInputBorder(),
                                ),
                                initialValue: DateTime(DateTime.now().year, 1, 1),
                                inputType: InputType.date,
                                format: DateFormat('yyyy-MM-dd'),
                                validator: FormBuilderValidators.compose([
                                  FormBuilderValidators.required(),
                                ]),
                              ),
                            ),
                            const SizedBox(width: 16),
                            Expanded(
                              child: FormBuilderDateTimePicker(
                                name: 'end_date',
                                decoration: const InputDecoration(
                                  labelText: 'End Date',
                                  hintText: 'Select end date',
                                  border: OutlineInputBorder(),
                                ),
                                initialValue: DateTime(DateTime.now().year, 12, 31),
                                inputType: InputType.date,
                                format: DateFormat('yyyy-MM-dd'),
                                validator: FormBuilderValidators.compose([
                                  FormBuilderValidators.required(),
                                ]),
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),
                ),
                const SizedBox(height: 16),
                
                // Additional Information
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
                          'Additional Information',
                          style: Theme.of(context).textTheme.titleLarge?.copyWith(
                                fontWeight: FontWeight.bold,
                              ),
                        ),
                        const SizedBox(height: 16),
                        
                        // Notes
                        FormBuilderTextField(
                          name: 'notes',
                          decoration: const InputDecoration(
                            labelText: 'Notes',
                            hintText: 'Enter any additional notes',
                            border: OutlineInputBorder(),
                          ),
                          maxLines: 3,
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
                        : const Text('Create Quota Allocation'),
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