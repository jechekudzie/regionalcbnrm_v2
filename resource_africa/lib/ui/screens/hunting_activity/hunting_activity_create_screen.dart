import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:flutter_form_builder/flutter_form_builder.dart';
import 'package:form_builder_validators/form_builder_validators.dart';
import 'package:intl/intl.dart';

import 'package:resource_africa/models/hunting_model.dart';
import 'package:resource_africa/models/organisation_model.dart';
import 'package:resource_africa/models/wildlife_conflict_model.dart';
import 'package:resource_africa/repositories/hunting_activity_repository.dart';
import 'package:resource_africa/repositories/organisation_repository.dart';
import 'package:resource_africa/repositories/wildlife_conflict_repository.dart';
import 'package:resource_africa/services/notification_service.dart';

class HuntingActivityCreateScreen extends StatefulWidget {
  const HuntingActivityCreateScreen({super.key});

  @override
  State<HuntingActivityCreateScreen> createState() => _HuntingActivityCreateScreenState();
}

class _HuntingActivityCreateScreenState extends State<HuntingActivityCreateScreen> {
  final _formKey = GlobalKey<FormBuilderState>();
  
  final HuntingActivityRepository _repository = HuntingActivityRepository();
  final WildlifeConflictRepository _wildlifeRepository = WildlifeConflictRepository();
  final OrganisationRepository _organisationRepository = OrganisationRepository();
  final NotificationService _notificationService = Get.find<NotificationService>();
  
  final RxBool _isLoading = false.obs;
  final RxBool _isLoadingData = true.obs;
  final RxList<HuntingConcession> _huntingConcessions = RxList<HuntingConcession>([]);
  final RxList<Species> _species = RxList<Species>([]);
  final RxList<OrganisationDetail> _safariOperators = RxList<OrganisationDetail>([]);
  final RxInt _organisationId = 0.obs;
  
  // Professionals hunters licenses
  final RxList<Map<String, dynamic>> _professionalHunters = RxList<Map<String, dynamic>>([
    {'license_number': '', 'hunter_name': ''}
  ]);
  
  // Species and off-take
  final RxList<Map<String, dynamic>> _selectedSpecies = RxList<Map<String, dynamic>>([
    {'species_id': 0, 'quantity': 1, 'quota_remaining': 0}
  ]);
  
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
      
      // Load hunting concessions, species, and safari operators in parallel
      await Future.wait([
        _loadHuntingConcessions(),
        _loadSpecies(),
        _loadSafariOperators(),
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
  
  Future<void> _loadHuntingConcessions() async {
    try {
      final concessions = await _repository.getHuntingConcessions(_organisationId.value);
      _huntingConcessions.value = concessions;
    } catch (e) {
      print('Error loading hunting concessions: $e');
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
  
  Future<void> _loadSafariOperators() async {
    try {
      // In a real app, you would have a repository method to fetch safari operators
      // For now, we'll use a mock list
    } catch (e) {
      print('Error loading safari operators: $e');
    }
  }
  
  // Function to check quota available for a species
  Future<int> _checkQuotaRemaining(int speciesId, String period) async {
    try {
      final allocations = await _repository.getQuotaAllocations(
        _organisationId.value, 
        period: period
      );
      
      final allocation = allocations.firstWhereOrNull(
        (a) => a.speciesId == speciesId
      );
      
      if (allocation != null && allocation.quotaAllocationBalance != null) {
        return allocation.quotaAllocationBalance!.remaining;
      } else {
        // If no allocation found, return 0
        return 0;
      }
    } catch (e) {
      print('Error checking quota: $e');
      return 0;
    }
  }
  
  // Add professional hunter
  void _addProfessionalHunter() {
    _professionalHunters.add({'license_number': '', 'hunter_name': ''});
  }
  
  // Remove professional hunter
  void _removeProfessionalHunter(int index) {
    if (_professionalHunters.length > 1) {
      _professionalHunters.removeAt(index);
    } else {
      _notificationService.showSnackBar(
        'At least one professional hunter is required',
        type: SnackBarType.warning,
      );
    }
  }
  
  // Add species
  void _addSpecies() {
    _selectedSpecies.add({'species_id': 0, 'quantity': 1, 'quota_remaining': 0});
  }
  
  // Remove species
  void _removeSpecies(int index) {
    if (_selectedSpecies.length > 1) {
      _selectedSpecies.removeAt(index);
    } else {
      _notificationService.showSnackBar(
        'At least one species is required',
        type: SnackBarType.warning,
      );
    }
  }
  
  // Update quota remaining for a species
  Future<void> _updateQuotaRemaining(int index) async {
    final speciesId = _selectedSpecies[index]['species_id'];
    if (speciesId == 0) return;
    
    // Get period from the form
    final period = _formKey.currentState?.fields['period']?.value?.toString() ?? '';
    if (period.isEmpty) return;
    
    // Check quota remaining
    final remaining = await _checkQuotaRemaining(speciesId, period);
    
    // Update the value in the list
    _selectedSpecies[index]['quota_remaining'] = remaining;
    
    // Force UI refresh
    setState(() {});
  }
  
  Future<void> _submitForm() async {
    if (_formKey.currentState?.saveAndValidate() ?? false) {
      final formData = _formKey.currentState!.value;
      
      _isLoading.value = true;
      
      try {
        // Extract data from form
        final huntingConcessionId = formData['hunting_concession_id'];
        final safariOperatorId = formData['safari_operator_id'];
        final startDate = formData['start_date'];
        final endDate = formData['end_date'];
        final period = formData['period'];
        
        // Process professional hunters
        final professionalHunterLicenses = <ProfessionalHunterLicense>[];
        for (int i = 0; i < _professionalHunters.length; i++) {
          final licenseNumber = formData['professional_hunter_license_$i'];
          final hunterName = formData['professional_hunter_name_$i'];
          
          if (licenseNumber != null && licenseNumber.isNotEmpty &&
              hunterName != null && hunterName.isNotEmpty) {
            professionalHunterLicenses.add(
              ProfessionalHunterLicense(
                huntingActivityId: 0, // Will be set by the server
                licenseNumber: licenseNumber,
                name: hunterName,
              ),
            );
          }
        }
        
        // Process species
        final speciesList = <HuntingActivitySpecies>[];
        for (int i = 0; i < _selectedSpecies.length; i++) {
          final speciesId = formData['species_id_$i'];
          final quantity = formData['quantity_$i'];
          
          if (speciesId != null && speciesId > 0 && quantity != null && quantity > 0) {
            speciesList.add(
              HuntingActivitySpecies(
                huntingActivityId: 0, // Will be set by the server
                speciesId: speciesId,
                quantity: quantity,
              ),
            );
          }
        }
        
        // Create the hunting activity
        final huntingActivity = HuntingActivity(
          organisationId: _organisationId.value,
          huntingConcessionId: huntingConcessionId,
          safariOperatorId: safariOperatorId,
          period: period.toString(),
          startDate: startDate,
          endDate: endDate,
          clientName: formData['client_name'] ?? '',
          clientNationality: formData['client_nationality'] ?? '',
          clientCountryOfResidence: formData['client_country_of_residence'] ?? '',
          species: speciesList,
          professionalHunterLicenses: professionalHunterLicenses,
        );
        
        // Convert to API format
        final apiData = {
          'organisation_id': _organisationId.value,
          'hunting_concession_id': huntingConcessionId,
          'safari_id': safariOperatorId,
          'period': period.toString(),
          'start_date': DateFormat('yyyy-MM-dd').format(startDate),
          'end_date': DateFormat('yyyy-MM-dd').format(endDate),
          'client_name': formData['client_name'] ?? '',
          'client_nationality': formData['client_nationality'] ?? '',
          'client_country_of_residence': formData['client_country_of_residence'] ?? '',
          'species': speciesList.map((s) => {
            'species_id': s.speciesId,
            'off_take': s.quantity,
          }).toList(),
          'professional_hunter_licenses': professionalHunterLicenses.map((p) => {
            'license_number': p.licenseNumber,
            'hunter_name': p.name,
          }).toList(),
        };
        
        // Send to the repository
        final createdActivity = await _repository.createHuntingActivity(
          huntingActivity,
          additionalData: apiData,
        );
        
        _notificationService.showSnackBar(
          'Hunting activity created successfully',
          type: SnackBarType.success,
        );
        
        Get.back(result: createdActivity);
      } catch (e) {
        print('Error submitting form: $e');
        _notificationService.showSnackBar(
          'Failed to create hunting activity. Please try again.',
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
        title: const Text('Create Hunting Activity'),
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
                        
                        // Hunting Concession
                        FormBuilderDropdown<int>(
                          name: 'hunting_concession_id',
                          decoration: const InputDecoration(
                            labelText: 'Hunting Concession',
                            hintText: 'Select a hunting concession',
                            border: OutlineInputBorder(),
                          ),
                          items: _huntingConcessions.map((concession) {
                            return DropdownMenuItem(
                              value: concession.id,
                              child: Text(concession.name),
                            );
                          }).toList(),
                          validator: FormBuilderValidators.compose([
                            FormBuilderValidators.required(),
                          ]),
                        ),
                        const SizedBox(height: 16),
                        
                        // Safari Operator
                        FormBuilderDropdown<int>(
                          name: 'safari_operator_id',
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
                          validator: FormBuilderValidators.compose([
                            FormBuilderValidators.required(),
                          ]),
                        ),
                        const SizedBox(height: 16),
                        
                        // Client Information
                        Row(
                          children: [
                            Expanded(
                              child: FormBuilderTextField(
                                name: 'client_name',
                                decoration: const InputDecoration(
                                  labelText: 'Client Name',
                                  hintText: 'Enter client name',
                                  border: OutlineInputBorder(),
                                ),
                                validator: FormBuilderValidators.compose([
                                  FormBuilderValidators.required(),
                                ]),
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 16),
                        
                        // Client Nationality and Country of Residence
                        Row(
                          children: [
                            Expanded(
                              child: FormBuilderTextField(
                                name: 'client_nationality',
                                decoration: const InputDecoration(
                                  labelText: 'Client Nationality',
                                  hintText: 'Enter nationality',
                                  border: OutlineInputBorder(),
                                ),
                                validator: FormBuilderValidators.compose([
                                  FormBuilderValidators.required(),
                                ]),
                              ),
                            ),
                            const SizedBox(width: 16),
                            Expanded(
                              child: FormBuilderTextField(
                                name: 'client_country_of_residence',
                                decoration: const InputDecoration(
                                  labelText: 'Country of Residence',
                                  hintText: 'Enter country',
                                  border: OutlineInputBorder(),
                                ),
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
                          'Date Information',
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
                                initialValue: DateTime.now(),
                                inputType: InputType.date,
                                format: DateFormat('yyyy-MM-dd'),
                                onChanged: (date) {
                                  if (date != null) {
                                    // Set the period (year) automatically
                                    _formKey.currentState?.fields['period']?.didChange(date.year);
                                  }
                                },
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
                                initialValue: DateTime.now().add(const Duration(days: 7)),
                                inputType: InputType.date,
                                format: DateFormat('yyyy-MM-dd'),
                                validator: FormBuilderValidators.compose([
                                  FormBuilderValidators.required(),
                                ]),
                              ),
                            ),
                          ],
                        ),
                        const SizedBox(height: 16),
                        
                        // Period (Year)
                        FormBuilderDropdown<int>(
                          name: 'period',
                          decoration: const InputDecoration(
                            labelText: 'Quota Period (Year)',
                            hintText: 'Select period',
                            border: OutlineInputBorder(),
                          ),
                          initialValue: DateTime.now().year,
                          items: List.generate(5, (index) {
                            final year = DateTime.now().year + index - 2;
                            return DropdownMenuItem(
                              value: year,
                              child: Text(year.toString()),
                            );
                          }),
                          validator: FormBuilderValidators.compose([
                            FormBuilderValidators.required(),
                          ]),
                          onChanged: (value) {
                            // When period changes, update quota remaining for all species
                            if (value != null) {
                              for (int i = 0; i < _selectedSpecies.length; i++) {
                                _updateQuotaRemaining(i);
                              }
                            }
                          },
                        ),
                      ],
                    ),
                  ),
                ),
                const SizedBox(height: 16),
                
                // Professional Hunter Licenses
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
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Text(
                              'Professional Hunter Licenses',
                              style: Theme.of(context).textTheme.titleLarge?.copyWith(
                                    fontWeight: FontWeight.bold,
                                  ),
                            ),
                            IconButton(
                              icon: const Icon(Icons.add_circle_outline),
                              onPressed: _addProfessionalHunter,
                              tooltip: 'Add Professional Hunter',
                            ),
                          ],
                        ),
                        const SizedBox(height: 16),
                        
                        // Professional Hunter License List
                        Obx(() => Column(
                          children: List.generate(_professionalHunters.length, (index) {
                            return Padding(
                              padding: const EdgeInsets.only(bottom: 8.0),
                              child: Row(
                                children: [
                                  Expanded(
                                    child: FormBuilderTextField(
                                      name: 'professional_hunter_license_$index',
                                      decoration: const InputDecoration(
                                        labelText: 'License Number',
                                        hintText: 'Enter license number',
                                        border: OutlineInputBorder(),
                                      ),
                                      validator: FormBuilderValidators.compose([
                                        FormBuilderValidators.required(),
                                      ]),
                                    ),
                                  ),
                                  const SizedBox(width: 8),
                                  Expanded(
                                    child: FormBuilderTextField(
                                      name: 'professional_hunter_name_$index',
                                      decoration: const InputDecoration(
                                        labelText: 'Hunter Name',
                                        hintText: 'Enter hunter name',
                                        border: OutlineInputBorder(),
                                      ),
                                      validator: FormBuilderValidators.compose([
                                        FormBuilderValidators.required(),
                                      ]),
                                    ),
                                  ),
                                  IconButton(
                                    icon: const Icon(Icons.delete_outline, color: Colors.red),
                                    onPressed: () => _removeProfessionalHunter(index),
                                    tooltip: 'Remove Professional Hunter',
                                  ),
                                ],
                              ),
                            );
                          }),
                        )),
                      ],
                    ),
                  ),
                ),
                const SizedBox(height: 16),
                
                // Species and Off-take
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
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Text(
                              'Species and Off-take',
                              style: Theme.of(context).textTheme.titleLarge?.copyWith(
                                    fontWeight: FontWeight.bold,
                                  ),
                            ),
                            IconButton(
                              icon: const Icon(Icons.add_circle_outline),
                              onPressed: _addSpecies,
                              tooltip: 'Add Species',
                            ),
                          ],
                        ),
                        const SizedBox(height: 16),
                        
                        // Column Headers
                        Row(
                          children: [
                            const Expanded(
                              flex: 3,
                              child: Text(
                                'Species',
                                style: TextStyle(fontWeight: FontWeight.bold),
                              ),
                            ),
                            const Expanded(
                              flex: 2,
                              child: Text(
                                'Quantity',
                                style: TextStyle(fontWeight: FontWeight.bold),
                              ),
                            ),
                            Expanded(
                              flex: 2,
                              child: Text(
                                'Remaining Quota',
                                style: TextStyle(
                                  fontWeight: FontWeight.bold,
                                  color: Colors.grey.shade700,
                                ),
                              ),
                            ),
                            const SizedBox(width: 40), // For delete button
                          ],
                        ),
                        const SizedBox(height: 8),
                        const Divider(),
                        const SizedBox(height: 8),
                        
                        // Species List
                        Obx(() => Column(
                          children: List.generate(_selectedSpecies.length, (index) {
                            final speciesItem = _selectedSpecies[index];
                            final quotaRemaining = speciesItem['quota_remaining'] ?? 0;
                            
                            return Padding(
                              padding: const EdgeInsets.only(bottom: 16.0),
                              child: Row(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  // Species dropdown
                                  Expanded(
                                    flex: 3,
                                    child: FormBuilderDropdown<int>(
                                      name: 'species_id_$index',
                                      decoration: const InputDecoration(
                                        labelText: 'Species',
                                        hintText: 'Select species',
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
                                      onChanged: (value) {
                                        if (value != null) {
                                          speciesItem['species_id'] = value;
                                          _updateQuotaRemaining(index);
                                        }
                                      },
                                    ),
                                  ),
                                  const SizedBox(width: 8),
                                  
                                  // Quantity input
                                  Expanded(
                                    flex: 2,
                                    child: FormBuilderTextField(
                                      name: 'quantity_$index',
                                      decoration: const InputDecoration(
                                        labelText: 'Quantity',
                                        hintText: 'Enter quantity',
                                        border: OutlineInputBorder(),
                                      ),
                                      initialValue: '1',
                                      keyboardType: TextInputType.number,
                                      validator: FormBuilderValidators.compose([
                                        FormBuilderValidators.required(),
                                        FormBuilderValidators.numeric(),
                                        FormBuilderValidators.min(1),
                                        (value) {
                                          if (value == null || value.isEmpty) return null;
                                          final quantity = int.tryParse(value);
                                          if (quantity != null && quantity > quotaRemaining) {
                                            return 'Exceeds quota';
                                          }
                                          return null;
                                        },
                                      ]),
                                    ),
                                  ),
                                  const SizedBox(width: 8),
                                  
                                  // Quota remaining display
                                  Expanded(
                                    flex: 2,
                                    child: Container(
                                      height: 60,
                                      padding: const EdgeInsets.all(8),
                                      decoration: BoxDecoration(
                                        border: Border.all(
                                          color: quotaRemaining > 0 ? Colors.green.shade300 : Colors.red.shade300,
                                        ),
                                        borderRadius: BorderRadius.circular(4),
                                        color: quotaRemaining > 0 ? Colors.green.shade50 : Colors.red.shade50,
                                      ),
                                      child: Column(
                                        mainAxisAlignment: MainAxisAlignment.center,
                                        children: [
                                          Text(
                                            quotaRemaining.toString(),
                                            style: TextStyle(
                                              fontWeight: FontWeight.bold,
                                              fontSize: 18,
                                              color: quotaRemaining > 0 ? Colors.green.shade700 : Colors.red.shade700,
                                            ),
                                          ),
                                          Text(
                                            'animals',
                                            style: TextStyle(
                                              fontSize: 12,
                                              color: Colors.grey.shade700,
                                            ),
                                          ),
                                        ],
                                      ),
                                    ),
                                  ),
                                  
                                  // Delete button
                                  IconButton(
                                    icon: const Icon(Icons.delete_outline, color: Colors.red),
                                    onPressed: () => _removeSpecies(index),
                                    tooltip: 'Remove Species',
                                  ),
                                ],
                              ),
                            );
                          }),
                        )),
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
                        : const Text('Create Hunting Activity'),
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