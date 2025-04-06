class WildlifeConflictIncident {
  final int? id;
  final int organisationId;
  final String title;
  final int period;
  final DateTime date;
  final String time;
  final double latitude;
  final double longitude;
  final String? locationDescription;
  final String description;
  final int conflictTypeId;
  final int? speciesId;
  final DateTime? createdAt;
  final DateTime? updatedAt;
  final String? syncStatus;
  final String? author;
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
    required this.period,
    required this.date,
    required this.time,
    required this.latitude,
    required this.longitude,
    this.locationDescription,
    required this.description,
    required this.conflictTypeId,
    this.speciesId,
    this.createdAt,
    this.updatedAt,
    this.syncStatus = 'pending',
    this.author,
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
      'period': period,
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
      'author': author,
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
      period: json['period'] ?? DateTime.now().year,
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
      author: json['author'],
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
      'period': period,
      'incident_date': date.toIso8601String().split('T')[0],
      'incident_time': time,
      'latitude': latitude,
      'longitude': longitude,
      'location_description': '',  // Placeholder, will be filled from form
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
    int? period,
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
    String? author,
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
      period: period ?? this.period,
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
      author: author ?? this.author,
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
  final List<DynamicValue>? dynamicValues; // Note: DB schema links these to Incident, but API might nest them here.

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
      'dynamic_values': dynamicValues?.map((v) => v.toJson()).toList(), // Serializes DynamicValue list
    };
  }

  factory WildlifeConflictOutcome.fromJson(Map<String, dynamic> json) {
    // Note: wildlifeConflictIncidentId might not be directly in the outcome JSON from the API,
    // it might be inferred from the parent incident context when parsing.
    // Assuming it's present for local DB deserialization.
    return WildlifeConflictOutcome(
      id: json['id'],
      // Assuming wildlife_conflict_incident_id is available for local DB mapping
      wildlifeConflictIncidentId: json['wildlife_conflict_incident_id'] ?? 0,
      conflictOutcomeId: json['conflict_outcome_id'],
      notes: json['notes'],
      date: DateTime.parse(json['date']),
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : null,
      updatedAt: json['updated_at'] != null ? DateTime.parse(json['updated_at']) : null,
      syncStatus: json['sync_status'] ?? 'synced',
      remoteId: json['remote_id'] ?? json['id'],
      // Assuming 'conflict_outcome' relation is provided by API
      conflictOutcome: json['conflict_outcome'] != null ? ConflictOutcome.fromJson(json['conflict_outcome']) : null,
      // Assuming 'dynamic_values' might be nested under outcome by API, despite DB change
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
      // Ensure dynamic_values are formatted correctly for API submission
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
// Aligned with MySQL schema (conflict_out_comes table)
class ConflictOutcome {
  final int id;
  final int conflictTypeId; // Added
  final String name;
  final String? description; // Added
  final String? slug; // Added
  final DateTime? createdAt;
  final DateTime? updatedAt;
  final List<DynamicField>? dynamicFields; // Kept for potential API structure

  ConflictOutcome({
    required this.id,
    required this.conflictTypeId, // Added
    required this.name,
    this.description, // Added
    this.slug, // Added
    this.createdAt,
    this.updatedAt,
    this.dynamicFields, // Kept for potential API structure
  });

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'conflict_type_id': conflictTypeId, // Added
      'name': name,
      'description': description, // Added
      'slug': slug, // Added
      'created_at': createdAt?.toIso8601String(),
      'updated_at': updatedAt?.toIso8601String(),
      // Serializes DynamicField list if present
      'dynamic_fields': dynamicFields?.map((field) => field.toJson()).toList(),
    };
  }

  factory ConflictOutcome.fromJson(Map<String, dynamic> json) {
    return ConflictOutcome(
      id: json['id'],
      // Ensure these fields are correctly parsed from API response
      conflictTypeId: json['conflict_type_id'] ?? 0, // Provide default if potentially null
      name: json['name'],
      description: json['description'],
      slug: json['slug'],
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : null,
      updatedAt: json['updated_at'] != null ? DateTime.parse(json['updated_at']) : null,
      // Assuming API might provide 'dynamic_fields' nested under outcome
      dynamicFields: json['dynamic_fields'] != null
          ? (json['dynamic_fields'] as List).map((field) => DynamicField.fromJson(field)).toList()
          : null,
    );
  }
}

// Aligned with MySQL schema (dynamic_fields table)
class DynamicField {
  final int id;
  final int organisationId; // Added
  final int? conflictOutcomeId; // Added (nullable)
  final String fieldName; // Renamed from 'name'
  final String fieldType; // Renamed from 'type'
  final String? label; // Made nullable
  final String? slug; // Added
  final DateTime? createdAt;
  final DateTime? updatedAt;
  final List<DynamicFieldOption>? options; // Kept for potential API structure / options table

  DynamicField({
    required this.id,
    required this.organisationId, // Added
    this.conflictOutcomeId, // Added
    required this.fieldName, // Renamed
    required this.fieldType, // Renamed
    this.label, // Made nullable
    this.slug, // Added
    this.createdAt,
    this.updatedAt,
    this.options, // Kept
  });

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'organisation_id': organisationId, // Added
      'conflict_outcome_id': conflictOutcomeId, // Added
      'field_name': fieldName, // Renamed
      'field_type': fieldType, // Renamed
      'label': label,
      'slug': slug, // Added
      'created_at': createdAt?.toIso8601String(),
      'updated_at': updatedAt?.toIso8601String(),
      // Serializes DynamicFieldOption list if present
      'options': options?.map((option) => option.toJson()).toList(),
    };
  }

  factory DynamicField.fromJson(Map<String, dynamic> json) {
    // Ensure correct parsing of fields from API response
    return DynamicField(
      id: json['id'],
      // Provide defaults if potentially null from API
      organisationId: json['organisation_id'] ?? 0,
      conflictOutcomeId: json['conflict_outcome_id'], // Nullable is okay
      fieldName: json['field_name'],
      fieldType: json['field_type'],
      label: json['label'],
      slug: json['slug'],
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : null,
      updatedAt: json['updated_at'] != null ? DateTime.parse(json['updated_at']) : null,
      // Assuming API might provide 'options' nested under field
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
