import 'package:flutter/material.dart';
import 'package:resource_africa/ui/widgets/location_picker.dart';
import 'package:get/get.dart';
import 'package:flutter_form_builder/flutter_form_builder.dart';
import 'package:form_builder_validators/form_builder_validators.dart';
import 'package:intl/intl.dart';
import 'package:resource_africa/models/problem_animal_control_model.dart';
import 'package:resource_africa/models/wildlife_conflict_model.dart';
import 'package:resource_africa/repositories/problem_animal_control_repository.dart';
import 'package:resource_africa/repositories/wildlife_conflict_repository.dart';
import 'package:resource_africa/repositories/organisation_repository.dart';
import 'package:resource_africa/services/notification_service.dart';

class ProblemAnimalControlCreateScreen extends StatefulWidget {
  const ProblemAnimalControlCreateScreen({super.key});

  @override
  State<ProblemAnimalControlCreateScreen> createState() => _ProblemAnimalControlCreateScreenState();
}

class _ProblemAnimalControlCreateScreenState extends State<ProblemAnimalControlCreateScreen> {
  final _formKey = GlobalKey<FormBuilderState>();

  final ProblemAnimalControlRepository _repository = ProblemAnimalControlRepository();
  final WildlifeConflictRepository _conflictRepository = WildlifeConflictRepository();
  final OrganisationRepository _organisationRepository = OrganisationRepository();
  final NotificationService _notificationService = Get.find<NotificationService>();

  final RxBool _isLoading = false.obs;
  final RxBool _isLoadingData = true.obs;
  final RxList<ControlMeasure> _controlMeasures = RxList<ControlMeasure>([]);
  final RxList<WildlifeConflictIncident> _incidents = RxList<WildlifeConflictIncident>([]);
  final RxDouble _latitude = 0.0.obs;
  final RxDouble _longitude = 0.0.obs;
  final RxInt _organisationId = 0.obs;

  // For linked wildlife conflict incident
  final RxBool _isRelatedToIncident = false.obs;
  final Rx<WildlifeConflictIncident?> _selectedIncident = Rx<WildlifeConflictIncident?>(null);
  int? _relatedIncidentId;

  @override
  void initState() {
    super.initState();

    // Check if we have a pre-selected wildlife conflict incident ID from the arguments
    if (Get.arguments != null && Get.arguments['wildlifeConflictIncidentId'] != null) {
      _relatedIncidentId = Get.arguments['wildlifeConflictIncidentId'];
      _isRelatedToIncident.value = true;
    }

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

      // Load control measures and incidents in parallel
      await Future.wait([
        _loadControlMeasures(),
        _loadIncidents(),
      ]);

      // If we have a related incident ID, load the incident details and set location
      if (_relatedIncidentId != null) {
        await _loadRelatedIncident();
      }
    } catch (e) {
      _notificationService.showSnackBar(
        'Failed to load form data. Please try again.',
        type: SnackBarType.error,
      );
    } finally {
      _isLoadingData.value = false;
    }
  }

  Future<void> _loadControlMeasures() async {
    try {
      final measures = await _repository.getControlMeasures();
      _controlMeasures.value = measures;
    } catch (e) {
      // Handle error
    }
  }

  Future<void> _loadIncidents() async {
    if (_organisationId.value == 0) return;

    try {
      final incidents = await _conflictRepository.getIncidents(_organisationId.value);
      _incidents.value = incidents;
    } catch (e) {
      // Handle error
    }
  }

  Future<void> _loadRelatedIncident() async {
    if (_relatedIncidentId == null) return;

    try {
      final incident = await _conflictRepository.getIncident(_relatedIncidentId!);
      _selectedIncident.value = incident;
      _latitude.value = incident.latitude ?? 0.0;
      _longitude.value = incident.longitude ?? 0.0;

      // Update the form values
      _formKey.currentState?.fields['latitude']?.didChange(_latitude.value.toString());
      _formKey.currentState?.fields['longitude']?.didChange(_longitude.value.toString());
    } catch (e) {
      // If there's an error loading the related incident, fallback to getting user location
    }
  }


  Future<void> _submitForm() async {
    if (_formKey.currentState?.saveAndValidate() ?? false) {
      final formData = _formKey.currentState!.value;

      _isLoading.value = true;

      try {
        final control = ProblemAnimalControl(
          wildlifeConflictIncidentId: _isRelatedToIncident.value && _selectedIncident.value != null
              ? _selectedIncident.value!.id!
              : 0, // Use 0 as a placeholder if no incident is selected
          controlMeasureId: formData['control_measure_id'],
          organisationId: _organisationId.value,
          date: formData['date'],
          time: DateFormat('HH:mm').format(formData['time']),
          description: formData['description'],
          latitude: double.parse(formData['latitude']),
          longitude: double.parse(formData['longitude']),
          numberOfAnimals: int.parse(formData['number_of_animals']),
        );

        final createdControl = await _repository.createControl(control);

        _notificationService.showSnackBar(
          'Problem animal control measure recorded successfully',
          type: SnackBarType.success,
        );

        Get.back(result: createdControl);
      } catch (e) {
        _notificationService.showSnackBar(
          'Failed to record control measure. Please try again.',
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
        title: const Text('Add Control Measure'),
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
                // Related incident section
                if (_relatedIncidentId != null && _selectedIncident.value != null) ...[
                  Card(
                    child: Padding(
                      padding: const EdgeInsets.all(16),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          const Text(
                            'Related Wildlife Conflict Incident',
                            style: TextStyle(
                              fontSize: 16,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                          const SizedBox(height: 8),
                          Text(
                            _selectedIncident.value?.title ?? 'No Title',
                            style: const TextStyle(fontSize: 14),
                          ),
                          const SizedBox(height: 4),
                          Text(
                            'Date: ${_selectedIncident.value?.date != null ? DateFormat('dd MMM yyyy').format(_selectedIncident.value!.date) : 'Unknown Date'}',
                            style: const TextStyle(fontSize: 12, color: Colors.grey),
                          ),
                        ],
                      ),
                    ),
                  ),
                  const SizedBox(height: 16),
                ] else ...[
                  // Option to link to an incident
                  FormBuilderSwitch(
                    name: 'link_to_incident',
                    title: const Text('Link to Wildlife Conflict Incident'),
                    initialValue: false,
                    decoration: const InputDecoration(
                      border: InputBorder.none,
                    ),
                    onChanged: (value) {
                      _isRelatedToIncident.value = value ?? false;
                    },
                  ),

                  if (_isRelatedToIncident.value) ...[
                    FormBuilderDropdown(
                      name: 'incident_id',
                      decoration: const InputDecoration(
                        labelText: 'Conflict Incident',
                        hintText: 'Select related incident',
                        border: OutlineInputBorder(),
                        prefixIcon: Icon(Icons.warning_amber_rounded),
                      ),
                      items: _incidents.map((incident) {
                        final formattedDate = incident.date != null ? DateFormat('dd MMM yyyy').format(incident.date) : 'Unknown Date';
                        return DropdownMenuItem(
                          value: incident.id,
                          child: Text('${incident.title} ($formattedDate)'),
                        );
                      }).toList(),
                      validator: _isRelatedToIncident.value
                          ? FormBuilderValidators.compose([FormBuilderValidators.required()])
                          : null,
                      onChanged: (value) {
                        if (value != null) {
                          final incident = _incidents.firstWhere((i) => i.id == value);
                          _selectedIncident.value = incident;
                          _latitude.value = incident.latitude ?? 0.0;
                          _longitude.value = incident.longitude ?? 0.0;
                          _formKey.currentState?.fields['latitude']?.didChange(_latitude.value.toString());
                          _formKey.currentState?.fields['longitude']?.didChange(_longitude.value.toString());
                        }
                      },
                    ),
                    const SizedBox(height: 16),
                  ],
                ],

                // Control measure dropdown
                FormBuilderDropdown(
                  name: 'control_measure_id',
                  decoration: const InputDecoration(
                    labelText: 'Control Measure',
                    hintText: 'Select control measure',
                    border: OutlineInputBorder(),
                    prefixIcon: Icon(Icons.pets),
                  ),
                  items: _controlMeasures.map((measure) {
                    return DropdownMenuItem(
                      value: measure.id,
                      child: Text(measure.name),
                    );
                  }).toList(),
                  validator: FormBuilderValidators.compose([
                    FormBuilderValidators.required(),
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

                // Number of animals field
                FormBuilderTextField(
                  name: 'number_of_animals',
                  decoration: const InputDecoration(
                    labelText: 'Number of Animals',
                    border: OutlineInputBorder(),
                    prefixIcon: Icon(Icons.numbers),
                  ),
                  keyboardType: TextInputType.number,
                  initialValue: '1',
                  validator: FormBuilderValidators.compose([
                    FormBuilderValidators.required(),
                    FormBuilderValidators.numeric(),
                    FormBuilderValidators.min(1),
                  ]),
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
                    hintText: 'Describe the control measure in detail',
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
                            'Record Control Measure',
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
