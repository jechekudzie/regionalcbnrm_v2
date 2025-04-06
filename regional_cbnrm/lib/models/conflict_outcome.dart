import 'package:regional_cbnrm/models/dynamic_field.dart';

class ConflictOutcome {
  final int? id;
  final int conflictTypeId;
  final String name;
  final String? description;
  final String? slug;
  final DateTime? createdAt;
  final DateTime? updatedAt;
  final String? author;
  final String syncStatus;
  final List<DynamicField>? dynamicFields;

  ConflictOutcome({
    this.id,
    required this.conflictTypeId,
    required this.name,
    this.description,
    this.slug,
    this.createdAt,
    this.updatedAt,
    this.author,
    this.syncStatus = 'pending',
    this.dynamicFields,
  });

  // From API JSON
  factory ConflictOutcome.fromApiJson(Map json) {
    List<DynamicField> dynamicFields = [];
    if (json['dynamic_fields'] != null && json['dynamic_fields'] is List) {
      dynamicFields = (json['dynamic_fields'] as List)
          .map((field) => DynamicField.fromApiJson(field))
          .toList();
    }

    return ConflictOutcome(
      id: json['id'],
      conflictTypeId: json['conflict_type_id'],
      name: json['name'],
      description: json['description'],
      slug: json['slug'],
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : null,
      updatedAt: json['updated_at'] != null ? DateTime.parse(json['updated_at']) : null,
      author: json['author'],
      syncStatus: 'synced', // API returns synced items
      dynamicFields: dynamicFields.isNotEmpty ? dynamicFields : null,
    );
  }

  // From database JSON
  factory ConflictOutcome.fromDatabaseJson(Map json) {
    List<DynamicField> dynamicFields = [];
    if (json['dynamic_fields'] != null && json['dynamic_fields'] is List) {
      dynamicFields = (json['dynamic_fields'] as List)
          .map((field) => DynamicField.fromDatabaseJson(field))
          .toList();
    }

    return ConflictOutcome(
      id: json['id'],
      conflictTypeId: json['conflict_type_id'],
      name: json['name'],
      description: json['description'],
      slug: json['slug'],
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : null,
      updatedAt: json['updated_at'] != null ? DateTime.parse(json['updated_at']) : null,
      author: json['author'],
      syncStatus: json['sync_status'] ?? 'pending',
      dynamicFields: dynamicFields.isNotEmpty ? dynamicFields : null,
    );
  }

  // To database JSON
  Map toDatabaseJson() {
    return {
      'id': id,
      'conflict_type_id': conflictTypeId,
      'name': name,
      'description': description,
      'slug': slug,
      'created_at': createdAt?.toIso8601String(),
      'updated_at': updatedAt?.toIso8601String(),
      'author': author,
      'sync_status': syncStatus,
      'dynamic_fields': dynamicFields?.map((field) => field.toDatabaseJson()).toList(),
    };
  }

  // To API JSON
  Map toApiJson() {
    return {
      'conflict_type_id': conflictTypeId,
      'name': name,
      'description': description,
      'slug': slug,
      'dynamic_fields': dynamicFields?.map((field) => field.toApiJson()).toList(),
    };
  }

  ConflictOutcome copyWith({
    int? id,
    int? conflictTypeId,
    String? name,
    String? description,
    String? slug,
    DateTime? createdAt,
    DateTime? updatedAt,
    String? author,
    String? syncStatus,
    List<DynamicField>? dynamicFields,
  }) {
    return ConflictOutcome(
      id: id ?? this.id,
      conflictTypeId: conflictTypeId ?? this.conflictTypeId,
      name: name ?? this.name,
      description: description ?? this.description,
      slug: slug ?? this.slug,
      createdAt: createdAt ?? this.createdAt,
      updatedAt: updatedAt ?? this.updatedAt,
      author: author ?? this.author,
      syncStatus: syncStatus ?? this.syncStatus,
      dynamicFields: dynamicFields ?? this.dynamicFields,
    );
  }

  @override
  String toString() {
    return 'ConflictOutcome(id: $id, name: $name, conflictTypeId: $conflictTypeId, dynamicFields: ${dynamicFields?.length ?? 0})';
  }

  @override
  bool operator ==(Object other) {
    if (identical(this, other)) return true;

    return other is ConflictOutcome &&
        other.id == id &&
        other.conflictTypeId == conflictTypeId &&
        other.name == name;
  }

  @override
  int get hashCode {
    return id.hashCode ^ conflictTypeId.hashCode ^ name.hashCode;
  }
}