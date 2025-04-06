class DynamicValue {
  final int? id;
  final int dynamicFieldId;
  final int? incidentId;  // Reference to wildlife_conflict_incidents
  final String value;
  final DateTime? createdAt;
  final DateTime? updatedAt;
  final String? author;
  final String syncStatus;

  DynamicValue({
    this.id,
    required this.dynamicFieldId,
    this.incidentId,
    required this.value,
    this.createdAt,
    this.updatedAt,
    this.author,
    this.syncStatus = 'pending',
  });

  // From API JSON
  factory DynamicValue.fromApiJson(Map<String, dynamic> json) {
    return DynamicValue(
      id: json['id'],
      dynamicFieldId: json['dynamic_field_id'],
      incidentId: json['wildlife_conflict_incident_id'],
      value: json['value'],
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : null,
      updatedAt: json['updated_at'] != null ? DateTime.parse(json['updated_at']) : null,
      author: json['author'],
      syncStatus: 'synced', // API returns synced items
    );
  }

  // From database JSON
  factory DynamicValue.fromDatabaseJson(Map<String, dynamic> json) {
    return DynamicValue(
      id: json['id'],
      dynamicFieldId: json['dynamic_field_id'],
      incidentId: json['incident_id'],
      value: json['value'],
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : null,
      updatedAt: json['updated_at'] != null ? DateTime.parse(json['updated_at']) : null,
      author: json['author'],
      syncStatus: json['sync_status'] ?? 'pending',
    );
  }

  // To database JSON
  Map<String, dynamic> toDatabaseJson() {
    final map = {
      'id': id,
      'dynamic_field_id': dynamicFieldId,
      'value': value,
      'created_at': createdAt?.toIso8601String(),
      'updated_at': updatedAt?.toIso8601String(),
      'author': author,
      'sync_status': syncStatus,
    };
    
    if (incidentId != null) {
      map['incident_id'] = incidentId;
    }
    
    return map;
  }

  // To API JSON
  Map<String, dynamic> toApiJson() {
    final map = {
      'dynamic_field_id': dynamicFieldId,
      'value': value,
    };
    
    if (incidentId != null) {
      map['wildlife_conflict_incident_id'] = incidentId;
    }
    
    return map;
  }

  // Copy with
  DynamicValue copyWith({
    int? id,
    int? dynamicFieldId,
    int? incidentId,
    String? value,
    DateTime? createdAt,
    DateTime? updatedAt,
    String? author,
    String? syncStatus,
  }) {
    return DynamicValue(
      id: id ?? this.id,
      dynamicFieldId: dynamicFieldId ?? this.dynamicFieldId,
      incidentId: incidentId ?? this.incidentId,
      value: value ?? this.value,
      createdAt: createdAt ?? this.createdAt,
      updatedAt: updatedAt ?? this.updatedAt,
      author: author ?? this.author,
      syncStatus: syncStatus ?? this.syncStatus,
    );
  }

  @override
  String toString() {
    return 'DynamicValue(id: $id, dynamicFieldId: $dynamicFieldId, value: $value)';
  }

  @override
  bool operator ==(Object other) {
    if (identical(this, other)) return true;
    
    return other is DynamicValue &&
        other.id == id &&
        other.dynamicFieldId == dynamicFieldId &&
        other.incidentId == incidentId &&
        other.value == value;
  }

  @override
  int get hashCode {
    return id.hashCode ^
        dynamicFieldId.hashCode ^
        incidentId.hashCode ^
        value.hashCode;
  }
}