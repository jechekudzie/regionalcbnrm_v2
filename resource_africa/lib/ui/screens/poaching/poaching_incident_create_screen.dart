import 'package:flutter/material.dart';
import 'package:flutter_form_builder/flutter_form_builder.dart';
import 'package:form_builder_validators/form_builder_validators.dart';
import 'package:get/get.dart';
import 'package:intl/intl.dart';
import 'package:resource_africa/models/poaching_model.dart';
import 'package:resource_africa/models/wildlife_conflict_model.dart';
import 'package:resource_africa/repositories/poaching_repository.dart';
import 'package:resource_africa/ui/widgets/simple_location_picker.dart';

class PoachingIncidentCreateScreen extends StatefulWidget {
  const PoachingIncidentCreateScreen({Key? key}) : super(key: key);

  @override
  State<PoachingIncidentCreateScreen> createState() => _PoachingIncidentCreateScreenState();
}

class _PoachingIncidentCreateScreenState extends State<PoachingIncidentCreateScreen> {
  final PoachingRepository _repository = PoachingRepository();
  final _formKey = GlobalKey<FormBuilderState>();
  final int _organisationId = int.parse(Get.parameters['organisationId'] ?? '0');
  
  final RxBool _isLoading = false.obs;
  final RxBool _isSubmitting = false.obs;
  final RxString _error = ''.obs;
  final RxList<PoachingMethod> _poachingMethods = <PoachingMethod>[].obs;
  final RxList<PoachingReason> _poachingReasons = <PoachingReason>[].obs;
  final RxList<Species> _allSpecies = <Species>[].obs;
  
  // Selected values
  final RxList<int> _selectedMethods = <int>[].obs;
  final RxList<Map<String, dynamic>> _selectedSpecies = <Map<String, dynamic>>[].obs;
  
  @override
  void initState() {
    super.initState();
    _loadData();
  }
  
  Future<void> _loadData() async {
    _isLoading.value = true;
    _error.value = '';
    
    try {
      // Load species list
      final species = await _repository.getSpeciesList();
      _allSpecies.value = species;
      
      // Load poaching methods
      final methods = await _repository.getPoachingMethods();
      _poachingMethods.value = methods;
      
      // Load poaching reasons
      final reasons = await _repository.getPoachingReasons();
      _poachingReasons.value = reasons;
    } catch (e) {
      _error.value = e.toString();
    } finally {
      _isLoading.value = false;
    }
  }
  
  void _addSpecies() {
    _selectedSpecies.add({
      'species_id': null,
      'quantity': 1,
    });
  }
  
  void _removeSpecies(int index) {
    _selectedSpecies.removeAt(index);
  }
  
  Future<void> _submitForm() async {
    if (_formKey.currentState?.saveAndValidate() ?? false) {
      _isSubmitting.value = true;
      
      try {
        final formData = _formKey.currentState!.value;
        
        // Create list of species
        List<PoachingIncidentSpecies> species = [];
        for (var speciesData in _selectedSpecies) {
          if (speciesData['species_id'] != null) {
            species.add(PoachingIncidentSpecies(
              poachingIncidentId: 0, // Will be set after creation
              speciesId: speciesData['species_id'],
              quantity: speciesData['quantity'],
            ));
          }
        }
        
        // Create list of methods
        List<PoachingIncidentMethod> methods = [];
        for (var methodId in _selectedMethods) {
          methods.add(PoachingIncidentMethod(
            poachingIncidentId: 0, // Will be set after creation
            poachingMethodId: methodId,
          ));
        }
        
        // Create poaching incident
        final incident = PoachingIncident(
          organisationId: _organisationId,
          title: formData['title'],
          date: formData['date'],
          time: DateFormat('HH:mm').format(formData['time']),
          latitude: formData['location']['latitude'],
          longitude: formData['location']['longitude'],
          description: formData['description'],
          docketNumber: formData['docket_number'],
          docketStatus: formData['docket_status'],
          species: species,
          methods: methods,
        );
        
        await _repository.createIncident(incident);
        
        Get.back(result: true);
        Get.snackbar(
          'Success',
          'Poaching incident reported successfully',
          snackPosition: SnackPosition.BOTTOM,
          backgroundColor: Colors.green,
          colorText: Colors.white,
        );
      } catch (e) {
        Get.snackbar(
          'Error',
          'Failed to report poaching incident: ${e.toString()}',
          snackPosition: SnackPosition.BOTTOM,
          backgroundColor: Colors.red,
          colorText: Colors.white,
        );
      } finally {
        _isSubmitting.value = false;
      }
    }
  }
  
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Report Poaching Incident'),
      ),
      body: Obx(() {
        if (_isLoading.value) {
          return const Center(child: CircularProgressIndicator());
        }
        
        if (_error.value.isNotEmpty) {
          return Center(
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                const Icon(Icons.error_outline, color: Colors.red, size: 48),
                const SizedBox(height: 16),
                Text(_error.value, textAlign: TextAlign.center),
                const SizedBox(height: 16),
                ElevatedButton(
                  onPressed: _loadData,
                  child: const Text('Retry'),
                ),
              ],
            ),
          );
        }
        
        return FormBuilder(
          key: _formKey,
          autovalidateMode: AutovalidateMode.disabled,
          child: SingleChildScrollView(
            padding: const EdgeInsets.all(16.0),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Basic incident information
                _buildSectionHeader('Incident Information'),
                FormBuilderTextField(
                  name: 'title',
                  decoration: const InputDecoration(
                    labelText: 'Title',
                    hintText: 'Enter a title for this incident',
                    border: OutlineInputBorder(),
                  ),
                  validator: FormBuilderValidators.compose([
                    FormBuilderValidators.required(),
                  ]),
                ),
                const SizedBox(height: 16),
                Row(
                  children: [
                    Expanded(
                      child: FormBuilderDateTimePicker(
                        name: 'date',
                        inputType: InputType.date,
                        decoration: const InputDecoration(
                          labelText: 'Date',
                          border: OutlineInputBorder(),
                          suffixIcon: Icon(Icons.calendar_today),
                        ),
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
                        inputType: InputType.time,
                        decoration: const InputDecoration(
                          labelText: 'Time',
                          border: OutlineInputBorder(),
                          suffixIcon: Icon(Icons.access_time),
                        ),
                        initialValue: DateTime.now(),
                        validator: FormBuilderValidators.compose([
                          FormBuilderValidators.required(),
                        ]),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 16),
                FormBuilderTextField(
                  name: 'description',
                  decoration: const InputDecoration(
                    labelText: 'Description',
                    hintText: 'Describe the poaching incident',
                    border: OutlineInputBorder(),
                    alignLabelWithHint: true,
                  ),
                  maxLines: 5,
                  validator: FormBuilderValidators.compose([
                    FormBuilderValidators.required(),
                  ]),
                ),
                
                // Location
                const SizedBox(height: 24),
                _buildSectionHeader('Location'),
                const Text(
                  'Tap the map to select the location of the incident or use the "Get Current Location" button.',
                  style: TextStyle(color: Colors.grey),
                ),
                const SizedBox(height: 8),
                FormBuilderField(
                  name: 'location',
                  validator: (value) {
                    if (value == null) {
                      return 'Please select a location';
                    }
                    return null;
                  },
                  initialValue: const {'latitude': 0.0, 'longitude': 0.0},
                  builder: (FormFieldState<dynamic> field) {
                    return InputDecorator(
                      decoration: InputDecoration(
                        errorText: field.errorText,
                        border: InputBorder.none,
                        contentPadding: EdgeInsets.zero,
                      ),
                      child: SimpleLocationPicker(
                        initialLatitude: 0.0,
                        initialLongitude: 0.0,
                        onLocationChanged: (lat, lng) {
                          field.didChange({'latitude': lat, 'longitude': lng});
                        },
                      ),
                    );
                  },
                ),
                
                // Docket information
                const SizedBox(height: 24),
                _buildSectionHeader('Docket Information (Optional)'),
                FormBuilderTextField(
                  name: 'docket_number',
                  decoration: const InputDecoration(
                    labelText: 'Docket Number',
                    hintText: 'Enter docket number if available',
                    border: OutlineInputBorder(),
                  ),
                ),
                const SizedBox(height: 16),
                FormBuilderDropdown<String>(
                  name: 'docket_status',
                  decoration: const InputDecoration(
                    labelText: 'Docket Status',
                    border: OutlineInputBorder(),
                  ),
                  items: ['Open', 'Closed', 'Pending']
                      .map((status) => DropdownMenuItem(
                            value: status,
                            child: Text(status),
                          ))
                      .toList(),
                ),
                
                // Poaching methods
                const SizedBox(height: 24),
                _buildSectionHeader('Poaching Methods'),
                Obx(() => Column(
                  children: _poachingMethods.map((method) {
                    return CheckboxListTile(
                      title: Text(method.name),
                      value: _selectedMethods.contains(method.id),
                      onChanged: (bool? value) {
                        if (value == true) {
                          _selectedMethods.add(method.id);
                        } else {
                          _selectedMethods.remove(method.id);
                        }
                      },
                    );
                  }).toList(),
                )),
                
                // Species
                const SizedBox(height: 24),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    _buildSectionHeader('Species Involved'),
                    ElevatedButton.icon(
                      icon: const Icon(Icons.add),
                      label: const Text('Add Species'),
                      onPressed: _addSpecies,
                    ),
                  ],
                ),
                Obx(() {
                  if (_selectedSpecies.isEmpty) {
                    return Padding(
                      padding: const EdgeInsets.symmetric(vertical: 16.0),
                      child: Center(
                        child: Text(
                          'No species added yet. Click "Add Species" to add one.',
                          style: TextStyle(color: Colors.grey.shade600),
                        ),
                      ),
                    );
                  }
                  
                  return ListView.builder(
                    shrinkWrap: true,
                    physics: const NeverScrollableScrollPhysics(),
                    itemCount: _selectedSpecies.length,
                    itemBuilder: (context, index) {
                      return Card(
                        margin: const EdgeInsets.only(bottom: 16.0),
                        child: Padding(
                          padding: const EdgeInsets.all(16.0),
                          child: Row(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Expanded(
                                flex: 2,
                                child: DropdownButtonFormField<int>(
                                  value: _selectedSpecies[index]['species_id'],
                                  decoration: const InputDecoration(
                                    labelText: 'Species',
                                    border: OutlineInputBorder(),
                                  ),
                                  items: _allSpecies
                                      .map((species) => DropdownMenuItem(
                                            value: species.id,
                                            child: Text(species.name),
                                          ))
                                      .toList(),
                                  onChanged: (value) {
                                    if (value != null) {
                                      _selectedSpecies[index]['species_id'] = value;
                                    }
                                  },
                                  validator: (value) {
                                    if (value == null) {
                                      return 'Required';
                                    }
                                    return null;
                                  },
                                ),
                              ),
                              const SizedBox(width: 16),
                              Expanded(
                                flex: 1,
                                child: TextFormField(
                                  initialValue: _selectedSpecies[index]['quantity'].toString(),
                                  decoration: const InputDecoration(
                                    labelText: 'Quantity',
                                    border: OutlineInputBorder(),
                                  ),
                                  keyboardType: TextInputType.number,
                                  onChanged: (value) {
                                    _selectedSpecies[index]['quantity'] = int.tryParse(value) ?? 1;
                                  },
                                  validator: (value) {
                                    if (value == null || value.isEmpty) {
                                      return 'Required';
                                    }
                                    final qty = int.tryParse(value);
                                    if (qty == null || qty < 1) {
                                      return 'Invalid';
                                    }
                                    return null;
                                  },
                                ),
                              ),
                              IconButton(
                                icon: const Icon(Icons.delete, color: Colors.red),
                                onPressed: () => _removeSpecies(index),
                              ),
                            ],
                          ),
                        ),
                      );
                    },
                  );
                }),
                
                // Submit button
                const SizedBox(height: 32),
                SizedBox(
                  width: double.infinity,
                  height: 50,
                  child: ElevatedButton(
                    onPressed: _isSubmitting.value ? null : _submitForm,
                    child: _isSubmitting.value
                        ? const CircularProgressIndicator()
                        : const Text('Submit Report', style: TextStyle(fontSize: 16)),
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
  
  Widget _buildSectionHeader(String title) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 16.0),
      child: Text(
        title,
        style: const TextStyle(
          fontSize: 18,
          fontWeight: FontWeight.bold,
        ),
      ),
    );
  }
}