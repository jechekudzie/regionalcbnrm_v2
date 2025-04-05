class WildlifeConflictIncident {
  final int? id;
  final int organisationId;
  final String title;
  final DateTime date;
  final String time;
  final double latitude;
  final double longitude;
  final String description;
  final int conflictTypeId;
  final int? speciesId;
  final DateTime? createdAt;
  final DateTime? updatedAt;
  final String? syncStatus;
  final int? remoteId;
  
  // Relations
  final ConflictType? conflictType;
  final Species? species;
  final List<Species>? speciesList;
  final List<WildlifeConflictOutcome>? outcomes;
  final List<DynamicValue>? dynamicValues;

  WildlifeConflictIncident({
    this.id,
    required this.organisationId,
    required this.title,
    required this.date,
    required this.time,
    required this.latitude,
    required this.longitude,
    required this.description,
    required this.conflictTypeId,
    this.speciesId,
    this.createdAt,
    this.updatedAt,
    this.syncStatus = 'pending',
    this.remoteId,
    this.conflictType,
    this.species,
    this.speciesList,
    this.outcomes,
    this.dynamicValues,
  });

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'organisation_id': organisationId,
      'title': title,
      'date': date.toIso8601String().split('T')[0],
      'time': time,
      'latitude': latitude,
      'longitude': longitude,
      'description': description,
      'conflict_type_id': conflictTypeId,
      'species_id': speciesId,
      'created_at': createdAt?.toIso8601String(),
      'updated_at': updatedAt?.toIso8601String(),
      'sync_status': syncStatus,
      'remote_id': remoteId,
      'conflict_type': conflictType?.toJson(),
      'species': species?.toJson(),
      'species_list': speciesList?.map((s) => s.toJson()).toList(),
      'outcomes': outcomes?.map((o) => o.toJson()).toList(),
      'dynamic_values': dynamicValues?.map((v) => v.toJson()).toList(),
    };
  }

  factory WildlifeConflictIncident.fromJson(Map<String, dynamic> json) {
    // Handle the API structure differences
    String dateField = json.containsKey('incident_date') ? 'incident_date' : 'date';
    String timeField = json.containsKey('incident_time') ? 'incident_time' : 'time';
    
    // Handle species list or single species
    List<Species>? speciesList;
    Species? primarySpecies;
    
    if (json['species'] is List) {
      final speciesJsonList = json['species'] as List;
      if (speciesJsonList.isNotEmpty) {
        // Convert all species in the list
        speciesList = speciesJsonList
            .map((speciesJson) => Species.fromJson(speciesJson))
            .toList();
        // Use the first one as the primary species for backward compatibility
        primarySpecies = speciesList.first;
      }
    } else if (json['species'] is Map) {
      // If it's a single species object
      primarySpecies = Species.fromJson(json['species']);
      speciesList = [primarySpecies];
    }
    
    // Get speciesId either directly or from the primary species
    int speciesId = json['species_id'] ?? 
                    (primarySpecies != null ? primarySpecies.id : 0);
    
    return WildlifeConflictIncident(
      id: json['id'],
      organisationId: json['organisation_id'],
      title: json['title'],
      date: DateTime.parse(json[dateField]),
      time: json[timeField] is String ? json[timeField] : json[timeField].toString(),
      latitude: json['latitude'] is String ? double.parse(json['latitude']) : json['latitude'].toDouble(),
      longitude: json['longitude'] is String ? double.parse(json['longitude']) : json['longitude'].toDouble(),
      description: json['description'] ?? '',
      conflictTypeId: json['conflict_type_id'],
      speciesId: speciesId,
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : null,
      updatedAt: json['updated_at'] != null ? DateTime.parse(json['updated_at']) : null,
      syncStatus: json['sync_status'] ?? 'synced',
      remoteId: json['remote_id'] ?? json['id'],
      conflictType: json['conflict_type'] != null ? ConflictType.fromJson(json['conflict_type']) : null,
      species: primarySpecies, // Keep for backward compatibility
      speciesList: speciesList, // Store the full list of species
      outcomes: json['outcomes'] != null
          ? (json['outcomes'] as List).map((o) => WildlifeConflictOutcome.fromJson(o)).toList()
          : null,
      dynamicValues: json['dynamic_values'] != null
          ? (json['dynamic_values'] as List).map((v) => DynamicValue.fromJson(v)).toList()
          : null,
    );
  }

  Map<String, dynamic> toApiJson() {
    // Create a base map with required fields
    final Map<String, dynamic> json = {
      'organisation_id': organisationId,
      'title': title,
      'date': date.toIso8601String().split('T')[0],
      'time': time,
      'latitude': latitude,
      'longitude': longitude,
      'description': description,
      'conflict_type_id': conflictTypeId,
      'species_id': speciesId,
      'dynamic_values': dynamicValues?.map((v) => v.toApiJson()).toList(),
    };
    
    // Add additional species IDs if available
    if (speciesList != null && speciesList!.isNotEmpty) {
      json['species_ids'] = speciesList!.map((s) => s.id).toList();
    }
    
    return json;
  }

  WildlifeConflictIncident copyWith({
    int? id,
    int? organisationId,
    String? title,
    DateTime? date,
    String? time,
    double? latitude,
    double? longitude,
    String? description,
    int? conflictTypeId,
    int? speciesId,
    DateTime? createdAt,
    DateTime? updatedAt,
    String? syncStatus,
    int? remoteId,
    ConflictType? conflictType,
    Species? species,
    List<Species>? speciesList,
    List<WildlifeConflictOutcome>? outcomes,
    List<DynamicValue>? dynamicValues,
  }) {
    return WildlifeConflictIncident(
      id: id ?? this.id,
      organisationId: organisationId ?? this.organisationId,
      title: title ?? this.title,
      date: date ?? this.date,
      time: time ?? this.time,
      latitude: latitude ?? this.latitude,
      longitude: longitude ?? this.longitude,
      description: description ?? this.description,
      conflictTypeId: conflictTypeId ?? this.conflictTypeId,
      speciesId: speciesId ?? this.speciesId,
      createdAt: createdAt ?? this.createdAt,
      updatedAt: updatedAt ?? this.updatedAt,
      syncStatus: syncStatus ?? this.syncStatus,
      remoteId: remoteId ?? this.remoteId,
      conflictType: conflictType ?? this.conflictType,
      species: species ?? this.species,
      speciesList: speciesList ?? this.speciesList,
      outcomes: outcomes ?? this.outcomes,
      dynamicValues: dynamicValues ?? this.dynamicValues,
    );
  }
}

class WildlifeConflictOutcome {
  final int? id;
  final int wildlifeConflictIncidentId;
  final int conflictOutcomeId;
  final String? notes;
  final DateTime date;
  final DateTime? createdAt;
  final DateTime? updatedAt;
  final String? syncStatus;
  final int? remoteId;
  
  // Relations
  final ConflictOutcome? conflictOutcome;
  final List<DynamicValue>? dynamicValues;

  WildlifeConflictOutcome({
    this.id,
    required this.wildlifeConflictIncidentId,
    required this.conflictOutcomeId,
    this.notes,
    required this.date,
    this.createdAt,
    this.updatedAt,
    this.syncStatus = 'pending',
    this.remoteId,
    this.conflictOutcome,
    this.dynamicValues,
  });

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'wildlife_conflict_incident_id': wildlifeConflictIncidentId,
      'conflict_outcome_id': conflictOutcomeId,
      'notes': notes,
      'date': date.toIso8601String().split('T')[0],
      'created_at': createdAt?.toIso8601String(),
      'updated_at': updatedAt?.toIso8601String(),
      'sync_status': syncStatus,
      'remote_id': remoteId,
      'conflict_outcome': conflictOutcome?.toJson(),
      'dynamic_values': dynamicValues?.map((v) => v.toJson()).toList(),
    };
  }

  factory WildlifeConflictOutcome.fromJson(Map<String, dynamic> json) {
    return WildlifeConflictOutcome(
      id: json['id'],
      wildlifeConflictIncidentId: json['wildlife_conflict_incident_id'],
      conflictOutcomeId: json['conflict_outcome_id'],
      notes: json['notes'],
      date: DateTime.parse(json['date']),
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : null,
      updatedAt: json['updated_at'] != null ? DateTime.parse(json['updated_at']) : null,
      syncStatus: json['sync_status'] ?? 'synced',
      remoteId: json['remote_id'] ?? json['id'],
      conflictOutcome: json['conflict_outcome'] != null ? ConflictOutcome.fromJson(json['conflict_outcome']) : null,
      dynamicValues: json['dynamic_values'] != null
          ? (json['dynamic_values'] as List).map((v) => DynamicValue.fromJson(v)).toList()
          : null,
    );
  }

  Map<String, dynamic> toApiJson() {
    return {
      'conflict_outcome_id': conflictOutcomeId,
      'notes': notes,
      'date': date.toIso8601String().split('T')[0],
      'dynamic_values': dynamicValues?.map((v) => v.toApiJson()).toList(),
    };
  }
}

class ConflictType {
  final int id;
  final String name;
  final DateTime? createdAt;
  final DateTime? updatedAt;

  ConflictType({
    required this.id,
    required this.name,
    this.createdAt,
    this.updatedAt,
  });

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'created_at': createdAt?.toIso8601String(),
      'updated_at': updatedAt?.toIso8601String(),
    };
  }

  factory ConflictType.fromJson(Map<String, dynamic> json) {
    return ConflictType(
      id: json['id'],
      name: json['name'],
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : null,
      updatedAt: json['updated_at'] != null ? DateTime.parse(json['updated_at']) : null,
    );
  }
}

class ConflictOutcome {
  final int id;
  final String name;
  final DateTime? createdAt;
  final DateTime? updatedAt;
  final List<DynamicField>? dynamicFields;

  ConflictOutcome({
    required this.id,
    required this.name,
    this.createdAt,
    this.updatedAt,
    this.dynamicFields,
  });

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'created_at': createdAt?.toIso8601String(),
      'updated_at': updatedAt?.toIso8601String(),
      'dynamic_fields': dynamicFields?.map((field) => field.toJson()).toList(),
    };
  }

  factory ConflictOutcome.fromJson(Map<String, dynamic> json) {
    return ConflictOutcome(
      id: json['id'],
      name: json['name'],
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : null,
      updatedAt: json['updated_at'] != null ? DateTime.parse(json['updated_at']) : null,
      dynamicFields: json['dynamic_fields'] != null
          ? (json['dynamic_fields'] as List).map((field) => DynamicField.fromJson(field)).toList()
          : null,
    );
  }
}

class DynamicField {
  final int id;
  final String name;
  final String type;
  final String label;
  final bool required;
  final DateTime? createdAt;
  final DateTime? updatedAt;
  final List<DynamicFieldOption>? options;

  DynamicField({
    required this.id,
    required this.name,
    required this.type,
    required this.label,
    required this.required,
    this.createdAt,
    this.updatedAt,
    this.options,
  });

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'type': type,
      'label': label,
      'required': required,
      'created_at': createdAt?.toIso8601String(),
      'updated_at': updatedAt?.toIso8601String(),
      'options': options?.map((option) => option.toJson()).toList(),
    };
  }

  factory DynamicField.fromJson(Map<String, dynamic> json) {
    return DynamicField(
      id: json['id'],
      name: json['name'],
      type: json['type'],
      label: json['label'],
      required: json['required'] == 1 || json['required'] == true,
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : null,
      updatedAt: json['updated_at'] != null ? DateTime.parse(json['updated_at']) : null,
      options: json['options'] != null
          ? (json['options'] as List).map((option) => DynamicFieldOption.fromJson(option)).toList()
          : null,
    );
  }
}

class DynamicFieldOption {
  final int id;
  final int dynamicFieldId;
  final String value;
  final String label;
  final DateTime? createdAt;
  final DateTime? updatedAt;

  DynamicFieldOption({
    required this.id,
    required this.dynamicFieldId,
    required this.value,
    required this.label,
    this.createdAt,
    this.updatedAt,
  });

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'dynamic_field_id': dynamicFieldId,
      'value': value,
      'label': label,
      'created_at': createdAt?.toIso8601String(),
      'updated_at': updatedAt?.toIso8601String(),
    };
  }

  factory DynamicFieldOption.fromJson(Map<String, dynamic> json) {
    return DynamicFieldOption(
      id: json['id'],
      dynamicFieldId: json['dynamic_field_id'],
      value: json['value'],
      label: json['label'],
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : null,
      updatedAt: json['updated_at'] != null ? DateTime.parse(json['updated_at']) : null,
    );
  }
}

class DynamicValue {
  final int? id;
  final int dynamicFieldId;
  final String value;
  final DateTime? createdAt;
  final DateTime? updatedAt;
  final DynamicField? dynamicField;

  DynamicValue({
    this.id,
    required this.dynamicFieldId,
    required this.value,
    this.createdAt,
    this.updatedAt,
    this.dynamicField,
  });

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'dynamic_field_id': dynamicFieldId,
      'value': value,
      'created_at': createdAt?.toIso8601String(),
      'updated_at': updatedAt?.toIso8601String(),
      'dynamic_field': dynamicField?.toJson(),
    };
  }

  factory DynamicValue.fromJson(Map<String, dynamic> json) {
    return DynamicValue(
      id: json['id'],
      dynamicFieldId: json['dynamic_field_id'],
      value: json['value'],
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : null,
      updatedAt: json['updated_at'] != null ? DateTime.parse(json['updated_at']) : null,
      dynamicField: json['dynamic_field'] != null ? DynamicField.fromJson(json['dynamic_field']) : null,
    );
  }

  Map<String, dynamic> toApiJson() {
    return {
      'dynamic_field_id': dynamicFieldId,
      'value': value,
    };
  }
}

class Species {
  final int id;
  final String name;
  final int? speciesGenderId;
  final int? maturityId;
  final DateTime? createdAt;
  final DateTime? updatedAt;

  Species({
    required this.id,
    required this.name,
    this.speciesGenderId,
    this.maturityId,
    this.createdAt,
    this.updatedAt,
  });

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'species_gender_id': speciesGenderId,
      'maturity_id': maturityId,
      'created_at': createdAt?.toIso8601String(),
      'updated_at': updatedAt?.toIso8601String(),
    };
  }

  factory Species.fromJson(Map<String, dynamic> json) {
    return Species(
      id: json['id'],
      name: json['name'],
      speciesGenderId: json['species_gender_id'],
      maturityId: json['maturity_id'],
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : null,
      updatedAt: json['updated_at'] != null ? DateTime.parse(json['updated_at']) : null,
    );
  }
}