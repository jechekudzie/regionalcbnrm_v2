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
  });

  // From API JSON
  factory ConflictOutcome.fromApiJson(Map<String, dynamic> json) {
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
    );
  }

  // From database JSON
  factory ConflictOutcome.fromDatabaseJson(Map<String, dynamic> json) {
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
    );
  }

  // To database JSON
  Map<String, dynamic> toDatabaseJson() {
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
    };
  }

  // To API JSON
  Map<String, dynamic> toApiJson() {
    return {
      'conflict_type_id': conflictTypeId,
      'name': name,
      'description': description,
      'slug': slug,
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
    );
  }

  @override
  String toString() {
    return 'ConflictOutcome(id: $id, name: $name, conflictTypeId: $conflictTypeId)';
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