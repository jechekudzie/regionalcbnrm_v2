import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:flutter_form_builder/flutter_form_builder.dart';
import 'package:form_builder_validators/form_builder_validators.dart';
import 'package:intl/intl.dart';
import 'package:resource_africa/models/wildlife_conflict_model.dart';
import 'package:resource_africa/repositories/wildlife_conflict_repository.dart';
import 'package:resource_africa/services/notification_service.dart';

class WildlifeConflictAddOutcomeScreen extends StatefulWidget {
  const WildlifeConflictAddOutcomeScreen({super.key});

  @override
  State<WildlifeConflictAddOutcomeScreen> createState() => _WildlifeConflictAddOutcomeScreenState();
}

class _WildlifeConflictAddOutcomeScreenState extends State<WildlifeConflictAddOutcomeScreen> {
  final _formKey = GlobalKey<FormBuilderState>();

  final WildlifeConflictRepository _repository = WildlifeConflictRepository();
  final NotificationService _notificationService = Get.find<NotificationService>();

  final RxBool _isLoading = false.obs;
  final RxBool _isLoadingData = true.obs;
  final RxList<ConflictOutcome> _conflictOutcomes = RxList<ConflictOutcome>([]);
  final RxList<DynamicValue> _dynamicValues = RxList<DynamicValue>([]);

  late int _incidentId;

  @override
  void initState() {
    super.initState();
    _incidentId = Get.arguments['incidentId'];
    _loadConflictOutcomes();
  }

  Future<void> _loadConflictOutcomes() async {
    _isLoadingData.value = true;

    try {
      final outcomes = await _repository.getConflictOutcomes();
      _conflictOutcomes.value = outcomes;
    } catch (e) {
      _notificationService.showSnackBar(
        'Failed to load conflict outcomes',
        type: SnackBarType.error,
      );
    } finally {
      _isLoadingData.value = false;
    }
  }

  void _onConflictOutcomeChanged(int? outcomeId) {
    if (outcomeId == null) return;

    // Find the selected conflict outcome
    final selectedOutcome = _conflictOutcomes.firstWhere(
      (outcome) => outcome.id == outcomeId,
      orElse: () => ConflictOutcome(id: 0, name: ''),
    );

    // Clear previous dynamic values
    _dynamicValues.clear();

    // Create new dynamic value fields if the outcome has dynamic fields
    if (selectedOutcome.dynamicFields != null && selectedOutcome.dynamicFields!.isNotEmpty) {
      for (final field in selectedOutcome.dynamicFields!) {
        _dynamicValues.add(
          DynamicValue(
            dynamicFieldId: field.id,
            value: '',
            dynamicField: field,
          ),
        );
      }
    }
  }

  Future<void> _submitForm() async {
    if (_formKey.currentState?.saveAndValidate() ?? false) {
      final formData = _formKey.currentState!.value;

      _isLoading.value = true;

      try {
        // Collect dynamic values if any
        final List<DynamicValue> dynamicValues = [];
        for (final field in _dynamicValues) {
          final value = formData['dynamic_field_${field.dynamicFieldId}'];
          if (value != null && value.toString().isNotEmpty) {
            dynamicValues.add(
              DynamicValue(
                dynamicFieldId: field.dynamicFieldId,
                value: value.toString(),
              ),
            );
          }
        }

        // Create and submit the outcome
        final outcome = await _repository.addOutcome(
          _incidentId,
          formData['conflict_outcome_id'],
          formData['notes'],
          formData['date'],
          dynamicValues.isNotEmpty ? dynamicValues : null,
        );

        Get.back(result: outcome);
      } catch (e) {
        _notificationService.showSnackBar(
          'Failed to add outcome. Please try again.',
          type: SnackBarType.error,
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
        title: const Text('Add Conflict Outcome'),
      ),
      body: Obx(() {
        if (_isLoadingData.value) {
          return const Center(child: CircularProgressIndicator());
        }

        return SingleChildScrollView(
          padding: const EdgeInsets.all(16),
          child: FormBuilder(
            key: _formKey,
            autovalidateMode: AutovalidateMode.disabled,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Conflict outcome dropdown
                FormBuilderDropdown(
                  name: 'conflict_outcome_id',
                  decoration: const InputDecoration(
                    labelText: 'Conflict Outcome',
                    hintText: 'Select outcome',
                    border: OutlineInputBorder(),
                    prefixIcon: Icon(Icons.assignment_turned_in),
                  ),
                  items: _conflictOutcomes.map((outcome) {
                    return DropdownMenuItem(
                      value: outcome.id,
                      child: Text(outcome.name),
                    );
                  }).toList(),
                  validator: FormBuilderValidators.compose([
                    FormBuilderValidators.required(),
                  ]),
                  onChanged: _onConflictOutcomeChanged,
                ),
                const SizedBox(height: 16),

                // Date field
                FormBuilderDateTimePicker(
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
                const SizedBox(height: 16),

                // Notes field
                FormBuilderTextField(
                  name: 'notes',
                  decoration: const InputDecoration(
                    labelText: 'Notes',
                    hintText: 'Additional notes about the outcome',
                    border: OutlineInputBorder(),
                    prefixIcon: Icon(Icons.note),
                  ),
                  maxLines: 3,
                ),
                const SizedBox(height: 16),

                // Dynamic fields
                ..._buildDynamicFields(),

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
                            'Add Outcome',
                            style: TextStyle(fontSize: 16),
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

  List<Widget> _buildDynamicFields() {
    final List<Widget> fields = [];

    for (final dynamicValue in _dynamicValues) {
      final field = dynamicValue.dynamicField;
      if (field == null) continue;

      Widget formField;

      switch (field.type) {
        case 'text':
          formField = FormBuilderTextField(
            name: 'dynamic_field_${field.id}',
            decoration: InputDecoration(
              labelText: field.label,
              border: const OutlineInputBorder(),
            ),
            validator: field.required
                ? FormBuilderValidators.compose([FormBuilderValidators.required()])
                : null,
          );
          break;
        case 'number':
          formField = FormBuilderTextField(
            name: 'dynamic_field_${field.id}',
            decoration: InputDecoration(
              labelText: field.label,
              border: const OutlineInputBorder(),
            ),
            keyboardType: TextInputType.number,
            validator: field.required
                ? FormBuilderValidators.compose([
                    FormBuilderValidators.required(),
                    FormBuilderValidators.numeric(),
                  ])
                : FormBuilderValidators.numeric(),
          );
          break;
        case 'select':
          if (field.options == null || field.options!.isEmpty) {
            formField = const SizedBox.shrink();
          } else {
            formField = FormBuilderDropdown(
              name: 'dynamic_field_${field.id}',
              decoration: InputDecoration(
                labelText: field.label,
                border: const OutlineInputBorder(),
              ),
              items: field.options!.map((option) {
                return DropdownMenuItem(
                  value: option.value,
                  child: Text(option.label),
                );
              }).toList(),
              validator: field.required
                  ? FormBuilderValidators.compose([FormBuilderValidators.required()])
                  : null,
            );
          }
          break;
        default:
          formField = const SizedBox.shrink();
      }

      fields.add(
        Padding(
          padding: const EdgeInsets.only(bottom: 16),
          child: formField,
        ),
      );
    }

    return fields;
  }
}
