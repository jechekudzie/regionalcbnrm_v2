import 'user_model.dart';

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
      'slug': slug ?? name.toLowerCase().replaceAll(' ', '-'),
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

class OrganisationDetail {
  final int id;
  final String name;
  final int organisationTypeId;
  final String? slug;
  final int? parentId;
  final DateTime? createdAt;
  final DateTime? updatedAt;
  final OrganisationType? organisationType;
  final Organisation? parentOrganisation;
  final List<Organisation>? childOrganisations;
  final List<Role>? roles;

  OrganisationDetail({
    required this.id,
    required this.name,
    required this.organisationTypeId,
    this.slug,
    this.parentId,
    this.createdAt,
    this.updatedAt,
    this.organisationType,
    this.parentOrganisation,
    this.childOrganisations,
    this.roles,
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
      'parent_organisation': parentOrganisation?.toJson(),
      'child_organisations': childOrganisations?.map((child) => child.toJson()).toList(),
      'organisation_roles': roles?.map((role) => role.toJson()).toList(),
    };
  }

  factory OrganisationDetail.fromJson(Map<String, dynamic> json) {
    return OrganisationDetail(
      id: json['id'],
      name: json['name'],
      organisationTypeId: json['organisation_type_id'],
      slug: json['slug'],
      parentId: json['organisation_id'],
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : null,
      updatedAt: json['updated_at'] != null ? DateTime.parse(json['updated_at']) : null,
      organisationType: json['organisation_type'] != null ? OrganisationType.fromJson(json['organisation_type']) : null,
      parentOrganisation: json['parent_organisation'] != null ? Organisation.fromJson(json['parent_organisation']) : null,
      childOrganisations: json['child_organisations'] != null
          ? (json['child_organisations'] as List).map((child) => Organisation.fromJson(child)).toList()
          : null,
      roles: json['organisation_roles'] != null
          ? (json['organisation_roles'] as List).map((role) => Role.fromJson(role)).toList()
          : null,
    );
  }

  @override
  String toString() {
    return 'OrganisationDetail{id: $id, name: $name}';
  }
}
