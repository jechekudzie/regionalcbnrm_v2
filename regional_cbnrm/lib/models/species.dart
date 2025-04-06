class Species {
  final int id;
  final String name;
  final String? scientific;
  final String? maleName;
  final String? femaleName;
  final String? avatar;
  final int? isSpecial;
  final String? slug;
  final DateTime? createdAt;
  final DateTime? updatedAt;

  Species({
    required this.id,
    required this.name,
    this.scientific,
    this.maleName,
    this.femaleName,
    this.avatar,
    this.isSpecial,
    this.slug,
    this.createdAt,
    this.updatedAt,
  });

  // From API JSON
  factory Species.fromApiJson(Map<String, dynamic> json) {
    return Species(
      id: json['id'],
      name: json['name'],
      scientific: json['scientific'],
      maleName: json['male_name'],
      femaleName: json['female_name'],
      avatar: json['avatar'],
      isSpecial: json['is_special'],
      slug: json['slug'],
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : null,
      updatedAt: json['updated_at'] != null ? DateTime.parse(json['updated_at']) : null,
    );
  }

  // From database JSON
  factory Species.fromDatabaseJson(Map<String, dynamic> json) {
    return Species(
      id: json['id'],
      name: json['name'],
      scientific: json['scientific'],
      maleName: json['male_name'],
      femaleName: json['female_name'],
      avatar: json['avatar'],
      isSpecial: json['is_special'],
      slug: json['slug'],
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : null,
      updatedAt: json['updated_at'] != null ? DateTime.parse(json['updated_at']) : null,
    );
  }

  // To database JSON
  Map<String, dynamic> toDatabaseJson() {
    return {
      'id': id,
      'name': name,
      'scientific': scientific,
      'male_name': maleName,
      'female_name': femaleName,
      'avatar': avatar,
      'is_special': isSpecial,
      'slug': slug,
      'created_at': createdAt?.toIso8601String(),
      'updated_at': updatedAt?.toIso8601String(),
    };
  }

  // To API JSON
  Map<String, dynamic> toApiJson() {
    return {
      'id': id,
      'name': name,
      'scientific': scientific,
      'male_name': maleName,
      'female_name': femaleName,
      'avatar': avatar,
      'is_special': isSpecial,
      'slug': slug,
    };
  }

  // Copy with
  Species copyWith({
    int? id,
    String? name,
    String? scientific,
    String? maleName,
    String? femaleName,
    String? avatar,
    int? isSpecial,
    String? slug,
    DateTime? createdAt,
    DateTime? updatedAt,
  }) {
    return Species(
      id: id ?? this.id,
      name: name ?? this.name,
      scientific: scientific ?? this.scientific,
      maleName: maleName ?? this.maleName,
      femaleName: femaleName ?? this.femaleName,
      avatar: avatar ?? this.avatar,
      isSpecial: isSpecial ?? this.isSpecial,
      slug: slug ?? this.slug,
      createdAt: createdAt ?? this.createdAt,
      updatedAt: updatedAt ?? this.updatedAt,
    );
  }

  @override
  String toString() {
    return 'Species(id: $id, name: $name, scientific: $scientific)';
  }

  @override
  bool operator ==(Object other) {
    if (identical(this, other)) return true;
    
    return other is Species &&
        other.id == id &&
        other.name == name;
  }

  @override
  int get hashCode {
    return id.hashCode ^ name.hashCode;
  }
}