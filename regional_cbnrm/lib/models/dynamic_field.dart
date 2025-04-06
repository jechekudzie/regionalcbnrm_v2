class DynamicField {
  final int? id;
  final int organisationId;
  final int? conflictOutcomeId;
  final String fieldName;
  final String fieldType;
  final String? label;
  final String? slug;
  final DateTime? createdAt;
  final DateTime? updatedAt;
  final String? author;
  final List<DynamicOption> options;
  final String syncStatus;

  DynamicField({
    this.id,
    required this.organisationId,
    this.conflictOutcomeId,
    required this.fieldName,
    required this.fieldType,
    this.label,
    this.slug,
    this.createdAt,
    this.updatedAt,
    this.author,
    this.syncStatus = 'pending',
    this.options = const [],
  });

  // From API JSON
  factory DynamicField.fromApiJson(Map<String, dynamic> json) {
    return DynamicField(
      id: json['id'],
      organisationId: json['organisation_id'],
      conflictOutcomeId: json['conflict_outcome_id'],
      fieldName: json['field_name'],
      fieldType: json['field_type'],
      label: json['label'],
      slug: json['slug'],
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : null,
      updatedAt: json['updated_at'] != null ? DateTime.parse(json['updated_at']) : null,
      author: json['author'],
      syncStatus: 'synced', // API returns synced items
      options: json['options'] != null
          ? List<Map<String, dynamic>>.from(json['options'])
              .map((opt) => DynamicOption.fromJson(opt))
              .toList()
          : [],
    );
  }

  // From database JSON
  factory DynamicField.fromDatabaseJson(Map<String, dynamic> json) {
    return DynamicField(
      id: json['id'],
      organisationId: json['organisation_id'],
      conflictOutcomeId: json['conflict_outcome_id'],
      fieldName: json['field_name'],
      fieldType: json['field_type'],
      label: json['label'],
      slug: json['slug'],
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : null,
      updatedAt: json['updated_at'] != null ? DateTime.parse(json['updated_at']) : null,
      author: json['author'],
      syncStatus: json['sync_status'] ?? 'pending',
      options: json['options'] != null
          ? List<Map<String, dynamic>>.from(json['options'])
              .map((opt) => DynamicOption.fromJson(opt))
              .toList()
          : [],
    );
  }

  // To database JSON
  Map<String, dynamic> toDatabaseJson() {
    return {
      'id': id,
      'organisation_id': organisationId,
      'conflict_outcome_id': conflictOutcomeId,
      'field_name': fieldName,
      'field_type': fieldType,
      'label': label,
      'slug': slug,
      'created_at': createdAt?.toIso8601String(),
      'updated_at': updatedAt?.toIso8601String(),
      'author': author,
      'sync_status': syncStatus,
    };
  }

  // To API JSON
  Map<String, dynamic> toApiJson() {
    final Map<String, dynamic> json = {
      'organisation_id': organisationId,
      'field_name': fieldName,
      'field_type': fieldType,
    };
    
    if (conflictOutcomeId != null) {
      json['conflict_outcome_id'] = conflictOutcomeId;
    }
    
    if (label != null) {
      json['label'] = label;
    }
    
    if (slug != null) {
      json['slug'] = slug;
    }
    
    return json;
  }

  // Copy with
  DynamicField copyWith({
    int? id,
    int? organisationId,
    int? conflictOutcomeId,
    String? fieldName,
    String? fieldType,
    String? label,
    String? slug,
    DateTime? createdAt,
    DateTime? updatedAt,
    String? author,
    String? syncStatus,
  }) {
    return DynamicField(
      id: id ?? this.id,
      organisationId: organisationId ?? this.organisationId,
      conflictOutcomeId: conflictOutcomeId ?? this.conflictOutcomeId,
      fieldName: fieldName ?? this.fieldName,
      fieldType: fieldType ?? this.fieldType,
      label: label ?? this.label,
      slug: slug ?? this.slug,
      createdAt: createdAt ?? this.createdAt,
      updatedAt: updatedAt ?? this.updatedAt,
      author: author ?? this.author,
      syncStatus: syncStatus ?? this.syncStatus,
    );
  }

  @override
  String toString() {
    return 'DynamicField(id: $id, fieldName: $fieldName, fieldType: $fieldType, conflictOutcomeId: $conflictOutcomeId)';
  }

  @override
  bool operator ==(Object other) {
    if (identical(this, other)) return true;
    
    return other is DynamicField &&
        other.id == id &&
        other.organisationId == organisationId &&
        other.conflictOutcomeId == conflictOutcomeId &&
        other.fieldName == fieldName &&
        other.fieldType == fieldType;
  }

  @override
  int get hashCode {
    return id.hashCode ^
        organisationId.hashCode ^
        conflictOutcomeId.hashCode ^
        fieldName.hashCode ^
        fieldType.hashCode;
  }
}

class DynamicOption {
  final String value;
  final String label;

  DynamicOption({
    required this.value,
    required this.label,
  });

  factory DynamicOption.fromJson(Map<String, dynamic> json) {
    return DynamicOption(
      value: json['value'].toString(),
      label: json['label'].toString(),
    );
  }
}