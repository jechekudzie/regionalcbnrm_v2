
class User {
  final int id;
  final String name;
  final String email;
  final List<Role> roles;
  final List<Organisation> organisations;
  
  User({
    required this.id,
    required this.name,
    required this.email,
    required this.roles,
    required this.organisations,
  });

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'email': email,
      'roles': roles.map((role) => role.toJson()).toList(),
      'organisations': organisations.map((org) => org.toJson()).toList(),
    };
  }

  factory User.fromJson(Map<String, dynamic> json) {
    return User(
      id: json['id'],
      name: json['name'],
      email: json['email'],
      roles: (json['roles'] as List).map((role) => Role.fromJson(role)).toList(),
      organisations: (json['organisations'] as List).map((org) => Organisation.fromJson(org)).toList(),
    );
  }

  @override
  String toString() {
    return 'User{id: $id, name: $name, email: $email}';
  }
}

class Role {
  final int id;
  final String name;
  final int? organisationId;

  Role({
    required this.id,
    required this.name,
    this.organisationId,
  });

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'organisation_id': organisationId,
    };
  }

  factory Role.fromJson(Map<String, dynamic> json) {
    return Role(
      id: json['id'],
      name: json['name'],
      organisationId: json['pivot'] != null ? json['pivot']['organisation_id'] : null,
    );
  }

  @override
  String toString() {
    return 'Role{id: $id, name: $name}';
  }
}

class Organisation {
  final int id;
  final String name;
  final int organisationTypeId;
  final String? slug;
  final int? parentId;
  final DateTime? createdAt;
  final DateTime? updatedAt;
  final OrganisationType? organisationType;

  Organisation({
    required this.id,
    required this.name,
    required this.organisationTypeId,
    this.slug,
    this.parentId,
    this.createdAt,
    this.updatedAt,
    this.organisationType,
  });

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'organisation_type_id': organisationTypeId,
      'slug': slug,
      'organisation_id': parentId,
      'created_at': createdAt?.toIso8601String(),
      'updated_at': updatedAt?.toIso8601String(),
      'organisation_type': organisationType?.toJson(),
    };
  }

  factory Organisation.fromJson(Map<String, dynamic> json) {
    return Organisation(
      id: json['id'],
      name: json['name'],
      organisationTypeId: json['organisation_type_id'],
      slug: json['slug'],
      parentId: json['organisation_id'],
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : null,
      updatedAt: json['updated_at'] != null ? DateTime.parse(json['updated_at']) : null,
      organisationType: json['organisation_type'] != null ? OrganisationType.fromJson(json['organisation_type']) : null,
    );
  }

  @override
  String toString() {
    return 'Organisation{id: $id, name: $name}';
  }
}

class OrganisationType {
  final int id;
  final String name;
  final String? slug;
  final DateTime? createdAt;
  final DateTime? updatedAt;

  OrganisationType({
    required this.id,
    required this.name,
    this.slug,
    this.createdAt,
    this.updatedAt,
  });

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'slug': slug,
      'created_at': createdAt?.toIso8601String(),
      'updated_at': updatedAt?.toIso8601String(),
    };
  }

  factory OrganisationType.fromJson(Map<String, dynamic> json) {
    return OrganisationType(
      id: json['id'],
      name: json['name'],
      slug: json['slug'],
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : null,
      updatedAt: json['updated_at'] != null ? DateTime.parse(json['updated_at']) : null,
    );
  }

  @override
  String toString() {
    return 'OrganisationType{id: $id, name: $name}';
  }
}