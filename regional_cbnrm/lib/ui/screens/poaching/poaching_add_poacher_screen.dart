import 'package:flutter/material.dart';
import 'package:flutter_form_builder/flutter_form_builder.dart';
import 'package:form_builder_validators/form_builder_validators.dart';
import 'package:get/get.dart';
import 'package:regional_cbnrm/repositories/poaching_repository.dart';

class PoachingAddPoacherScreen extends StatefulWidget {
  const PoachingAddPoacherScreen({Key? key}) : super(key: key);

  @override
  State<PoachingAddPoacherScreen> createState() => _PoachingAddPoacherScreenState();
}

class _PoachingAddPoacherScreenState extends State<PoachingAddPoacherScreen> {
  final PoachingRepository _repository = PoachingRepository();
  final _formKey = GlobalKey<FormBuilderState>();
  final int _incidentId = int.parse(Get.parameters['id'] ?? '0');
  
  final RxBool _isLoading = false.obs;
  final RxBool _isSubmitting = false.obs;
  final RxString _error = ''.obs;
  
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Add Poacher'),
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
                  onPressed: () => Get.back(),
                  child: const Text('Go Back'),
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
                _buildSectionHeader('Poacher Information'),
                FormBuilderTextField(
                  name: 'name',
                  decoration: const InputDecoration(
                    labelText: 'Name',
                    hintText: 'Enter poacher\'s name',
                    border: OutlineInputBorder(),
                  ),
                  validator: FormBuilderValidators.compose([
                    FormBuilderValidators.required(),
                  ]),
                ),
                const SizedBox(height: 16),
                FormBuilderTextField(
                  name: 'id_number',
                  decoration: const InputDecoration(
                    labelText: 'ID Number (Optional)',
                    hintText: 'Enter poacher\'s ID number if available',
                    border: OutlineInputBorder(),
                  ),
                ),
                const SizedBox(height: 16),
                FormBuilderDropdown<String>(
                  name: 'gender',
                  decoration: const InputDecoration(
                    labelText: 'Gender (Optional)',
                    border: OutlineInputBorder(),
                  ),
                  items: ['Male', 'Female', 'Other']
                      .map((gender) => DropdownMenuItem(
                            value: gender,
                            child: Text(gender),
                          ))
                      .toList(),
                ),
                const SizedBox(height: 16),
                FormBuilderTextField(
                  name: 'age',
                  decoration: const InputDecoration(
                    labelText: 'Age (Optional)',
                    hintText: 'Enter poacher\'s age if known',
                    border: OutlineInputBorder(),
                  ),
                  keyboardType: TextInputType.number,
                  validator: FormBuilderValidators.compose([
                    FormBuilderValidators.numeric(errorText: 'Please enter a valid number'),
                    FormBuilderValidators.min(0, errorText: 'Age must be positive'),
                    FormBuilderValidators.max(120, errorText: 'Age must be reasonable'),
                  ]),
                ),
                const SizedBox(height: 16),
                FormBuilderTextField(
                  name: 'nationality',
                  decoration: const InputDecoration(
                    labelText: 'Nationality (Optional)',
                    hintText: 'Enter poacher\'s nationality if known',
                    border: OutlineInputBorder(),
                  ),
                ),
                const SizedBox(height: 24),
                _buildSectionHeader('Status'),
                FormBuilderRadioGroup<String>(
                  name: 'status',
                  decoration: const InputDecoration(
                    border: InputBorder.none,
                  ),
                  initialValue: 'apprehended',
                  options: const [
                    FormBuilderFieldOption(
                      value: 'apprehended',
                      child: Text('Apprehended'),
                    ),
                    FormBuilderFieldOption(
                      value: 'escaped',
                      child: Text('Escaped'),
                    ),
                    FormBuilderFieldOption(
                      value: 'unknown',
                      child: Text('Unknown'),
                    ),
                  ],
                  validator: FormBuilderValidators.compose([
                    FormBuilderValidators.required(),
                  ]),
                ),
                
                // Submit button
                const SizedBox(height: 32),
                SizedBox(
                  width: double.infinity,
                  height: 50,
                  child: ElevatedButton(
                    onPressed: _isSubmitting.value ? null : _submitForm,
                    child: _isSubmitting.value
                        ? const CircularProgressIndicator()
                        : const Text('Add Poacher', style: TextStyle(fontSize: 16)),
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
  
  Future<void> _submitForm() async {
    if (_formKey.currentState?.saveAndValidate() ?? false) {
      _isSubmitting.value = true;
      
      try {
        final formData = _formKey.currentState!.value;
        
        await _repository.addPoacher(
          _incidentId,
          formData['name'],
          formData['id_number'],
          null, // identification_type_id not implemented yet
          formData['gender'],
          formData['age'] != null && formData['age'].isNotEmpty
              ? int.parse(formData['age'])
              : null,
          formData['nationality'],
          formData['status'],
        );
        
        Get.back(result: true);
        Get.snackbar(
          'Success',
          'Poacher added successfully',
          snackPosition: SnackPosition.BOTTOM,
          backgroundColor: Colors.green,
          colorText: Colors.white,
        );
      } catch (e) {
        Get.snackbar(
          'Error',
          'Failed to add poacher: ${e.toString()}',
          snackPosition: SnackPosition.BOTTOM,
          backgroundColor: Colors.red,
          colorText: Colors.white,
        );
      } finally {
        _isSubmitting.value = false;
      }
    }
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