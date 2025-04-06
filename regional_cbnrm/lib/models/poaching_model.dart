
import 'package:regional_cbnrm/models/species.dart';

class PoachingIncident {
  final int? id;
  final int organisationId;
  final String title;
  final DateTime date;
  final String time;
  final double latitude;
  final double longitude;
  final String description;
  final String? docketNumber;
  final String? docketStatus;
  final DateTime? createdAt;
  final DateTime? updatedAt;
  final String? syncStatus;
  final int? remoteId;
  
  // Relations
  final List<PoachingIncidentSpecies>? species;
  final List<PoachingIncidentMethod>? methods;
  final List<Poacher>? poachers;

  PoachingIncident({
    this.id,
    required this.organisationId,
    required this.title,
    required this.date,
    required this.time,
    required this.latitude,
    required this.longitude,
    required this.description,
    this.docketNumber,
    this.docketStatus,
    this.createdAt,
    this.updatedAt,
    this.syncStatus = 'pending',
    this.remoteId,
    this.species,
    this.methods,
    this.poachers,
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
      'docket_number': docketNumber,
      'docket_status': docketStatus,
      'created_at': createdAt?.toIso8601String(),
      'updated_at': updatedAt?.toIso8601String(),
      'sync_status': syncStatus,
      'remote_id': remoteId,
      'species': species?.map((s) => s.toJson()).toList(),
      'methods': methods?.map((m) => m.toJson()).toList(),
      'poachers': poachers?.map((p) => p.toJson()).toList(),
    };
  }

  factory PoachingIncident.fromJson(Map<String, dynamic> json) {
    return PoachingIncident(
      id: json['id'],
      organisationId: json['organisation_id'],
      title: json['title'],
      date: DateTime.parse(json['date']),
      time: json['time'],
      latitude: json['latitude'] is String ? double.parse(json['latitude']) : json['latitude'],
      longitude: json['longitude'] is String ? double.parse(json['longitude']) : json['longitude'],
      description: json['description'],
      docketNumber: json['docket_number'],
      docketStatus: json['docket_status'],
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : null,
      updatedAt: json['updated_at'] != null ? DateTime.parse(json['updated_at']) : null,
      syncStatus: json['sync_status'] ?? 'synced',
      remoteId: json['remote_id'] ?? json['id'],
      species: json['species'] != null
          ? (json['species'] as List).map((s) => PoachingIncidentSpecies.fromJson(s)).toList()
          : null,
      methods: json['methods'] != null
          ? (json['methods'] as List).map((m) => PoachingIncidentMethod.fromJson(m)).toList()
          : null,
      poachers: json['poachers'] != null
          ? (json['poachers'] as List).map((p) => Poacher.fromJson(p)).toList()
          : null,
    );
  }

  Map<String, dynamic> toApiJson() {
    return {
      'organisation_id': organisationId,
      'title': title,
      'date': date.toIso8601String().split('T')[0],
      'time': time,
      'latitude': latitude,
      'longitude': longitude,
      'description': description,
      'docket_number': docketNumber,
      'docket_status': docketStatus,
      'species': species?.map((s) => s.toApiJson()).toList(),
      'methods': methods?.map((m) => m.toApiJson()).toList(),
    };
  }

  PoachingIncident copyWith({
    int? id,
    int? organisationId,
    String? title,
    DateTime? date,
    String? time,
    double? latitude,
    double? longitude,
    String? description,
    String? docketNumber,
    String? docketStatus,
    DateTime? createdAt,
    DateTime? updatedAt,
    String? syncStatus,
    int? remoteId,
    List<PoachingIncidentSpecies>? species,
    List<PoachingIncidentMethod>? methods,
    List<Poacher>? poachers,
  }) {
    return PoachingIncident(
      id: id ?? this.id,
      organisationId: organisationId ?? this.organisationId,
      title: title ?? this.title,
      date: date ?? this.date,
      time: time ?? this.time,
      latitude: latitude ?? this.latitude,
      longitude: longitude ?? this.longitude,
      description: description ?? this.description,
      docketNumber: docketNumber ?? this.docketNumber,
      docketStatus: docketStatus ?? this.docketStatus,
      createdAt: createdAt ?? this.createdAt,
      updatedAt: updatedAt ?? this.updatedAt,
      syncStatus: syncStatus ?? this.syncStatus,
      remoteId: remoteId ?? this.remoteId,
      species: species ?? this.species,
      methods: methods ?? this.methods,
      poachers: poachers ?? this.poachers,
    );
  }
}

class PoachingIncidentSpecies {
  final int? id;
  final int poachingIncidentId;
  final int speciesId;
  final int quantity;
  final DateTime? createdAt;
  final DateTime? updatedAt;
  
  // Relations
  final Species? species;

  PoachingIncidentSpecies({
    this.id,
    required this.poachingIncidentId,
    required this.speciesId,
    required this.quantity,
    this.createdAt,
    this.updatedAt,
    this.species,
  });

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'poaching_incident_id': poachingIncidentId,
      'species_id': speciesId,
      'quantity': quantity,
      'created_at': createdAt?.toIso8601String(),
      'updated_at': updatedAt?.toIso8601String(),
      'species': species?.toApiJson(),
    };
  }

  factory PoachingIncidentSpecies.fromJson(Map<String, dynamic> json) {
    return PoachingIncidentSpecies(
      id: json['id'],
      poachingIncidentId: json['poaching_incident_id'],
      speciesId: json['species_id'],
      quantity: json['quantity'],
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : null,
      updatedAt: json['updated_at'] != null ? DateTime.parse(json['updated_at']) : null,
      species: json['species'] != null ? Species.fromApiJson(json['species']) : null,
    );
  }

  Map<String, dynamic> toApiJson() {
    return {
      'species_id': speciesId,
      'quantity': quantity,
    };
  }
}

class PoachingIncidentMethod {
  final int? id;
  final int poachingIncidentId;
  final int poachingMethodId;
  final DateTime? createdAt;
  final DateTime? updatedAt;
  
  // Relations
  final PoachingMethod? poachingMethod;

  PoachingIncidentMethod({
    this.id,
    required this.poachingIncidentId,
    required this.poachingMethodId,
    this.createdAt,
    this.updatedAt,
    this.poachingMethod,
  });

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'poaching_incident_id': poachingIncidentId,
      'poaching_method_id': poachingMethodId,
      'created_at': createdAt?.toIso8601String(),
      'updated_at': updatedAt?.toIso8601String(),
      'poaching_method': poachingMethod?.toJson(),
    };
  }

  factory PoachingIncidentMethod.fromJson(Map<String, dynamic> json) {
    return PoachingIncidentMethod(
      id: json['id'],
      poachingIncidentId: json['poaching_incident_id'],
      poachingMethodId: json['poaching_method_id'],
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : null,
      updatedAt: json['updated_at'] != null ? DateTime.parse(json['updated_at']) : null,
      poachingMethod: json['poaching_method'] != null ? PoachingMethod.fromJson(json['poaching_method']) : null,
    );
  }

  Map<String, dynamic> toApiJson() {
    return {
      'method_id': poachingMethodId,
    };
  }
}

class PoachingMethod {
  final int id;
  final String name;
  final DateTime? createdAt;
  final DateTime? updatedAt;

  PoachingMethod({
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

  factory PoachingMethod.fromJson(Map<String, dynamic> json) {
    return PoachingMethod(
      id: json['id'],
      name: json['name'],
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : null,
      updatedAt: json['updated_at'] != null ? DateTime.parse(json['updated_at']) : null,
    );
  }
}

class PoachingReason {
  final int id;
  final String name;
  final DateTime? createdAt;
  final DateTime? updatedAt;

  PoachingReason({
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

  factory PoachingReason.fromJson(Map<String, dynamic> json) {
    return PoachingReason(
      id: json['id'],
      name: json['name'],
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : null,
      updatedAt: json['updated_at'] != null ? DateTime.parse(json['updated_at']) : null,
    );
  }
}

class Poacher {
  final int? id;
  final int poachingIncidentId;
  final String name;
  final String? idNumber;
  final int? identificationTypeId;
  final String? gender;
  final int? age;
  final String? nationality;
  final String status;
  final DateTime? createdAt;
  final DateTime? updatedAt;
  final String? syncStatus;
  final int? remoteId;

  Poacher({
    this.id,
    required this.poachingIncidentId,
    required this.name,
    this.idNumber,
    this.identificationTypeId,
    this.gender,
    this.age,
    this.nationality,
    this.status = 'apprehended',
    this.createdAt,
    this.updatedAt,
    this.syncStatus = 'pending',
    this.remoteId,
  });

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'poaching_incident_id': poachingIncidentId,
      'name': name,
      'id_number': idNumber,
      'identification_type_id': identificationTypeId,
      'gender': gender,
      'age': age,
      'nationality': nationality,
      'status': status,
      'created_at': createdAt?.toIso8601String(),
      'updated_at': updatedAt?.toIso8601String(),
      'sync_status': syncStatus,
      'remote_id': remoteId,
    };
  }

  factory Poacher.fromJson(Map<String, dynamic> json) {
    return Poacher(
      id: json['id'],
      poachingIncidentId: json['poaching_incident_id'],
      name: json['name'],
      idNumber: json['id_number'],
      identificationTypeId: json['identification_type_id'],
      gender: json['gender'],
      age: json['age'],
      nationality: json['nationality'],
      status: json['status'] ?? 'apprehended',
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : null,
      updatedAt: json['updated_at'] != null ? DateTime.parse(json['updated_at']) : null,
      syncStatus: json['sync_status'] ?? 'synced',
      remoteId: json['remote_id'] ?? json['id'],
    );
  }

  Map<String, dynamic> toApiJson() {
    return {
      'name': name,
      'id_number': idNumber,
      'identification_type_id': identificationTypeId,
      'gender': gender,
      'age': age,
      'nationality': nationality,
      'status': status,
    };
  }
}