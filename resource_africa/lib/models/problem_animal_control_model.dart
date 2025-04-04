import 'wildlife_conflict_model.dart';

class ProblemAnimalControl {
  final int? id;
  final int wildlifeConflictIncidentId;
  final int controlMeasureId;
  final int organisationId;
  final DateTime date;
  final String time;
  final String description;
  final double latitude;
  final double longitude;
  final int numberOfAnimals;
  final DateTime? createdAt;
  final DateTime? updatedAt;
  final String? syncStatus;
  final int? remoteId;

  // Relations
  final ControlMeasure? controlMeasure;
  final WildlifeConflictIncident? wildlifeConflictIncident;

  ProblemAnimalControl({
    this.id,
    required this.wildlifeConflictIncidentId,
    required this.controlMeasureId,
    required this.organisationId,
    required this.date,
    required this.time,
    required this.description,
    required this.latitude,
    required this.longitude,
    required this.numberOfAnimals,
    this.createdAt,
    this.updatedAt,
    this.syncStatus = 'pending',
    this.remoteId,
    this.controlMeasure,
    this.wildlifeConflictIncident,
  });

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'wildlife_conflict_incident_id': wildlifeConflictIncidentId,
      'control_measure_id': controlMeasureId,
      'organisation_id': organisationId,
      'date': date.toIso8601String().split('T')[0],
      'time': time,
      'description': description,
      'latitude': latitude,
      'longitude': longitude,
      'number_of_animals': numberOfAnimals,
      'created_at': createdAt?.toIso8601String(),
      'updated_at': updatedAt?.toIso8601String(),
      'sync_status': syncStatus,
      'remote_id': remoteId,
      'control_measure': controlMeasure?.toJson(),
      'wildlife_conflict_incident': wildlifeConflictIncident?.toJson(),
    };
  }

  factory ProblemAnimalControl.fromJson(Map<String, dynamic> json) {
    return ProblemAnimalControl(
      id: json['id'],
      wildlifeConflictIncidentId: json['wildlife_conflict_incident_id'],
      controlMeasureId: json['control_measure_id'],
      organisationId: json['organisation_id'],
      date: DateTime.parse(json['date']),
      time: json['time'],
      description: json['description'],
      latitude: json['latitude'] is String ? double.parse(json['latitude']) : json['latitude'],
      longitude: json['longitude'] is String ? double.parse(json['longitude']) : json['longitude'],
      numberOfAnimals: json['number_of_animals'],
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : null,
      updatedAt: json['updated_at'] != null ? DateTime.parse(json['updated_at']) : null,
      syncStatus: json['sync_status'] ?? 'synced',
      remoteId: json['remote_id'] ?? json['id'],
      controlMeasure: json['control_measure'] != null ? ControlMeasure.fromJson(json['control_measure']) : null,
      wildlifeConflictIncident: json['wildlife_conflict_incident'] != null
          ? WildlifeConflictIncident.fromJson(json['wildlife_conflict_incident'])
          : null,
    );
  }

  Map<String, dynamic> toApiJson() {
    return {
      'wildlife_conflict_incident_id': wildlifeConflictIncidentId,
      'control_measure_id': controlMeasureId,
      'organisation_id': organisationId,
      'date': date.toIso8601String().split('T')[0],
      'time': time,
      'description': description,
      'latitude': latitude,
      'longitude': longitude,
      'number_of_animals': numberOfAnimals,
    };
  }

  ProblemAnimalControl copyWith({
    int? id,
    int? wildlifeConflictIncidentId,
    int? controlMeasureId,
    int? organisationId,
    DateTime? date,
    String? time,
    String? description,
    double? latitude,
    double? longitude,
    int? numberOfAnimals,
    DateTime? createdAt,
    DateTime? updatedAt,
    String? syncStatus,
    int? remoteId,
    ControlMeasure? controlMeasure,
    WildlifeConflictIncident? wildlifeConflictIncident,
  }) {
    return ProblemAnimalControl(
      id: id ?? this.id,
      wildlifeConflictIncidentId: wildlifeConflictIncidentId ?? this.wildlifeConflictIncidentId,
      controlMeasureId: controlMeasureId ?? this.controlMeasureId,
      organisationId: organisationId ?? this.organisationId,
      date: date ?? this.date,
      time: time ?? this.time,
      description: description ?? this.description,
      latitude: latitude ?? this.latitude,
      longitude: longitude ?? this.longitude,
      numberOfAnimals: numberOfAnimals ?? this.numberOfAnimals,
      createdAt: createdAt ?? this.createdAt,
      updatedAt: updatedAt ?? this.updatedAt,
      syncStatus: syncStatus ?? this.syncStatus,
      remoteId: remoteId ?? this.remoteId,
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
