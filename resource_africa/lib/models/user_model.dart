import 'organisation_model.dart';

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

  factory User.fromJson(Map<String, dynamic> json) {
    return User(
      id: json['id'],
      name: json['name'],
      email: json['email'],
      roles: json['roles'] != null
          ? (json['roles'] as List).map((role) => Role.fromJson(role)).toList()
          : [],
      organisations: json['organisations'] != null
          ? (json['organisations'] as List)
              .map((org) => Organisation.fromJson(org))
              .toList()
          : [],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'email': email,
      'roles': roles.map((role) => role.toJson()).toList(),
      'organisations': organisations.map((org) => org.toJson()).toList(),
    };
  }
}

class Role {
  final int id;
  final String name;
  final String slug;

  Role({
    required this.id,
    required this.name,
    required this.slug,
  });

  factory Role.fromJson(Map<String, dynamic> json) {
    return Role(
      id: json['id'],
      name: json['name'],
      slug: json['slug'] ?? json['name'].toString().toLowerCase().replaceAll(' ', '-'),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'slug': slug,
    };
  }
}

class Organisation {
  final int id;
  final String name;
  final String slug;
  final int? organisationTypeId;
  final int? parentId;
  final DateTime? createdAt;
  final DateTime? updatedAt;
  final List<Role>? userRoles;
  final List<String>? userPermissions;
  final OrganisationType? organisationType;

  Organisation({
    required this.id,
    required this.name,
    required this.slug,
    this.organisationTypeId,
    this.parentId,
    this.createdAt,
    this.updatedAt,
    this.userRoles,
    this.userPermissions,
    this.organisationType,
  });

  factory Organisation.fromJson(Map<String, dynamic> json) {
    return Organisation(
      id: json['id'],
      name: json['name'],
      slug: json['slug'] ?? json['name'].toString().toLowerCase().replaceAll(' ', '-'),
      organisationTypeId: json['organisation_type_id'] ?? json['type_id'],
      parentId: json['parent_id'] ?? json['organisation_id'],
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : null,
      updatedAt: json['updated_at'] != null ? DateTime.parse(json['updated_at']) : null,
      userRoles: json['user_roles'] != null
          ? (json['user_roles'] as List).map((role) => Role.fromJson(role)).toList()
          : null,
      userPermissions: json['user_permissions'] != null
          ? (json['user_permissions'] as List).map((permission) => permission.toString()).toList()
          : null,
      organisationType: json['organisation_type'] != null ? OrganisationType.fromJson(json['organisation_type']) : null,
    );
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = {
      'id': id,
      'name': name,
      'slug': slug,
      'organisation_type_id': organisationTypeId,
      'parent_id': parentId,
      'created_at': createdAt?.toIso8601String(),
      'updated_at': updatedAt?.toIso8601String(),
    };

    if (userRoles != null) {
      data['user_roles'] = userRoles!.map((role) => role.toJson()).toList();
    }

    if (userPermissions != null) {
      data['user_permissions'] = userPermissions;
    }

    if (organisationType != null) {
      data['organisation_type'] = organisationType?.toJson();
    }

    return data;
  }

  @override
  bool operator ==(Object other) {
    if (identical(this, other)) return true;
    return other is Organisation && other.id == id;
  }

  @override
  int get hashCode => id.hashCode;
}
