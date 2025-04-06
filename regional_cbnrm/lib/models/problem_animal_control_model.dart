import 'package:regional_cbnrm/models/wildlife_conflict_model.dart';

class ProblemAnimalControl {
  final int? id;
  final int wildlifeConflictIncidentId;
  final int organisationId;
  final DateTime date;
  final String time;
  final String description;
  final String period;
  final String location;
  final double latitude;
  final double longitude;
  final int estimatedNumber;
  final DateTime? createdAt;
  final DateTime? updatedAt;
  final String? syncStatus;
  final String? author;

  // Relations
  final ControlMeasure? controlMeasure;
  final WildlifeConflictIncident? wildlifeConflictIncident;

  ProblemAnimalControl({
    this.id,
    required this.wildlifeConflictIncidentId,
    required this.organisationId,
    required this.date,
    required this.time,
    required this.description,
    required this.period,
    required this.location,
    required this.latitude,
    required this.longitude,
    required this.estimatedNumber,
    this.createdAt,
    this.updatedAt,
    this.syncStatus = 'pending',
    this.author,
    this.controlMeasure,
    this.wildlifeConflictIncident,
  });

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'wildlife_conflict_incident_id': wildlifeConflictIncidentId,
      'control_measure_id': controlMeasure?.id,
      'organisation_id': organisationId,
      'date': date.toIso8601String().split('T')[0],
      'time': time,
      'description': description,
      'latitude': latitude,
      'longitude': longitude,
      'period': period,
      'location': location,
      'estimated_number': estimatedNumber,
      'created_at': createdAt?.toIso8601String(),
      'updated_at': updatedAt?.toIso8601String(),
      'sync_status': syncStatus,
      'author': author,
      'control_measure': controlMeasure?.toJson(),
      'wildlife_conflict_incident': wildlifeConflictIncident?.toApiJson(),
    };
  }

  factory ProblemAnimalControl.fromJson(Map<String, dynamic> json) {
    return ProblemAnimalControl(
      id: json['id'],
      wildlifeConflictIncidentId: json['wildlife_conflict_incident_id'],
      organisationId: json['organisation_id'],
      date: DateTime.parse(json['date']),
      time: json['time'],
      description: json['description'],
      period: json['period'],
      location: json['location'],
      latitude: json['latitude'] is String ? double.parse(json['latitude']) : json['latitude'],
      longitude: json['longitude'] is String ? double.parse(json['longitude']) : json['longitude'],
      estimatedNumber: json['estimated_number'],
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : null,
      updatedAt: json['updated_at'] != null ? DateTime.parse(json['updated_at']) : null,
      syncStatus: json['sync_status'] ?? 'synced',
      author: json['author'],
      controlMeasure: json['control_measure'] != null ? ControlMeasure.fromJson(json['control_measure'] as Map<String, dynamic>) : null,
      wildlifeConflictIncident: json['wildlife_conflict_incident'] != null
          ? WildlifeConflictIncident.fromApiJson(json['wildlife_conflict_incident'] as Map<String, dynamic>)
          : null,
    );
  }

  Map<String, dynamic> toApiJson() {
    return {
      'wildlife_conflict_incident_id': wildlifeConflictIncidentId,
      'organisation_id': organisationId,
      'date': date.toIso8601String().split('T')[0],
      'time': time,
      'description': description,
      'period': period,
      'location': location,
      'latitude': latitude,
      'longitude': longitude,
      'estimated_number': estimatedNumber,
    };
  }

  ProblemAnimalControl copyWith({
    int? id,
    int? wildlifeConflictIncidentId,
    int? organisationId,
    DateTime? date,
    String? time,
    String? description,
    String? period,
    String? location,
    double? latitude,
    double? longitude,
    int? estimatedNumber,
    DateTime? createdAt,
    DateTime? updatedAt,
    String? syncStatus,
    String? author,
    ControlMeasure? controlMeasure,
    WildlifeConflictIncident? wildlifeConflictIncident,
  }) {
    return ProblemAnimalControl(
      id: id ?? this.id,
      wildlifeConflictIncidentId: wildlifeConflictIncidentId ?? this.wildlifeConflictIncidentId,
      organisationId: organisationId ?? this.organisationId,
      date: date ?? this.date,
      time: time ?? this.time,
      description: description ?? this.description,
      period: period ?? this.period,
      location: location ?? this.location,
      latitude: latitude ?? this.latitude,
      longitude: longitude ?? this.longitude,
      estimatedNumber: estimatedNumber ?? this.estimatedNumber,
      createdAt: createdAt ?? this.createdAt,
      updatedAt: updatedAt ?? this.updatedAt,
      syncStatus: syncStatus ?? this.syncStatus,
      author: author ?? this.author,
      controlMeasure: controlMeasure ?? this.controlMeasure,
      wildlifeConflictIncident: wildlifeConflictIncident ?? this.wildlifeConflictIncident,
    );
  }
}

class ControlMeasure {
  final int id;
  final String name;
  final DateTime? createdAt;
  final DateTime? updatedAt;

  ControlMeasure({
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

  factory ControlMeasure.fromJson(Map<String, dynamic> json) {
    return ControlMeasure(
      id: json['id'],
      name: json['name'],
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : null,
      updatedAt: json['updated_at'] != null ? DateTime.parse(json['updated_at']) : null,
    );
  }
}
