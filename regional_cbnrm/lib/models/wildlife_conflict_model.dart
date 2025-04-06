import 'package:flutter/foundation.dart';

class WildlifeConflictIncident {
  final int? id;
  final int organisationId;
  final String title;
  final String period;
  final DateTime incidentDate;
  final String incidentTime;
  final double? longitude;
  final double? latitude;
  final String? locationDescription;
  final String? description;
  final int? conflictTypeId;
  final DateTime? createdAt;
  final DateTime? updatedAt;
  final String? author;
  final String syncStatus;
  
  // Only maintaining the speciesList relation as it's present in the API
  final List<int>? speciesIds;

  WildlifeConflictIncident({
    this.id,
    required this.organisationId,
    required this.title,
    required this.period,
    required this.incidentDate,
    required this.incidentTime,
    this.longitude,
    this.latitude,
    this.locationDescription,
    this.description,
    this.conflictTypeId,
    this.createdAt,
    this.updatedAt,
    this.author,
    this.syncStatus = 'pending',
    this.speciesIds,
  });

  // From JSON that comes from the API
  factory WildlifeConflictIncident.fromApiJson(Map<String, dynamic> json) {
    // Parse species list if available
    List<int>? speciesIds;
    if (json['species'] is List) {
      speciesIds = (json['species'] as List)
          .map((speciesJson) => speciesJson['id'] as int)
          .toList();
    }

    return WildlifeConflictIncident(
      id: json['id'],
      organisationId: json['organisation_id'],
      title: json['title'],
      period: json['period']?.toString() ?? DateTime.now().year.toString(),
      incidentDate: DateTime.parse(json['incident_date']),
      incidentTime: json['incident_time'] is String 
          ? json['incident_time'] 
          : json['incident_time'].toString(),
      longitude: json['longitude'] is String 
          ? double.parse(json['longitude']) 
          : json['longitude']?.toDouble(),
      latitude: json['latitude'] is String 
          ? double.parse(json['latitude']) 
          : json['latitude']?.toDouble(),
      locationDescription: json['location_description'],
      description: json['description'],
      conflictTypeId: json['conflict_type_id'],
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : null,
      updatedAt: json['updated_at'] != null ? DateTime.parse(json['updated_at']) : null,
      author: json['author'],
      syncStatus: 'synced', // API returns synced items
      speciesIds: speciesIds,
    );
  }

  // From JSON that comes from the local SQLite database
  factory WildlifeConflictIncident.fromDatabaseJson(Map<String, dynamic> json) {
    return WildlifeConflictIncident(
      id: json['id'],
      organisationId: json['organisation_id'],
      title: json['title'],
      period: json['period'],
      incidentDate: DateTime.parse(json['incident_date']),
      incidentTime: json['incident_time'],
      longitude: json['longitude'],
      latitude: json['latitude'],
      locationDescription: json['location_description'],
      description: json['description'],
      conflictTypeId: json['conflict_type_id'],
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : null,
      updatedAt: json['updated_at'] != null ? DateTime.parse(json['updated_at']) : null,
      author: json['author'],
      syncStatus: json['sync_status'] ?? 'pending',
      // Species IDs would be retrieved from a related table
    );
  }

  // To JSON for the local SQLite database
  Map<String, dynamic> toDatabaseJson() {
    return {
      'id': id,
      'organisation_id': organisationId,
      'title': title,
      'period': period,
      'incident_date': incidentDate.toIso8601String().split('T')[0],
      'incident_time': incidentTime,
      'longitude': longitude,
      'latitude': latitude,
      'location_description': locationDescription,
      'description': description,
      'conflict_type_id': conflictTypeId,
      'created_at': createdAt?.toIso8601String(),
      'updated_at': updatedAt?.toIso8601String(),
      'author': author,
      'sync_status': syncStatus,
    };
  }

  // To JSON for sending to the API
  Map<String, dynamic> toApiJson() {
    final Map<String, dynamic> json = {
      'organisation_id': organisationId,
      'title': title,
      'period': period,
      'incident_date': incidentDate.toIso8601String().split('T')[0],
      'incident_time': incidentTime,
      'longitude': longitude,
      'latitude': latitude,
      'location_description': locationDescription ?? '',
      'description': description ?? '',
      'conflict_type_id': conflictTypeId,
    };
    
    // Add species IDs if available
    if (speciesIds != null && speciesIds!.isNotEmpty) {
      json['species_ids'] = speciesIds;
    }
    
    return json;
  }

  // Create a copy with updated fields
  WildlifeConflictIncident copyWith({
    int? id,
    int? organisationId,
    String? title,
    String? period,
    DateTime? incidentDate,
    String? incidentTime,
    double? longitude,
    double? latitude,
    String? locationDescription,
    String? description,
    int? conflictTypeId,
    DateTime? createdAt,
    DateTime? updatedAt,
    String? author,
    String? syncStatus,
    List<int>? speciesIds,
  }) {
    return WildlifeConflictIncident(
      id: id ?? this.id,
      organisationId: organisationId ?? this.organisationId,
      title: title ?? this.title,
      period: period ?? this.period,
      incidentDate: incidentDate ?? this.incidentDate,
      incidentTime: incidentTime ?? this.incidentTime,
      longitude: longitude ?? this.longitude,
      latitude: latitude ?? this.latitude,
      locationDescription: locationDescription ?? this.locationDescription,
      description: description ?? this.description,
      conflictTypeId: conflictTypeId ?? this.conflictTypeId,
      createdAt: createdAt ?? this.createdAt,
      updatedAt: updatedAt ?? this.updatedAt,
      author: author ?? this.author,
      syncStatus: syncStatus ?? this.syncStatus,
      speciesIds: speciesIds ?? this.speciesIds,
    );
  }
  
  // Helper method to parse a species list from the API response
  static List<int> _parseSpeciesIds(List<dynamic> speciesList) {
    return speciesList
        .map((species) => species['id'] as int)
        .toList();
  }

  @override
  String toString() {
    return 'WildlifeConflictIncident(id: $id, title: $title, date: $incidentDate, species: $speciesIds)';
  }

  @override
  bool operator ==(Object other) {
    if (identical(this, other)) return true;
    
    return other is WildlifeConflictIncident &&
        other.id == id &&
        other.organisationId == organisationId &&
        other.title == title &&
        other.period == period &&
        other.incidentDate == incidentDate &&
        other.incidentTime == incidentTime &&
        other.longitude == longitude &&
        other.latitude == latitude &&
        other.description == description &&
        other.conflictTypeId == conflictTypeId &&
        listEquals(other.speciesIds, speciesIds);
  }

  @override
  int get hashCode {
    return id.hashCode ^
        organisationId.hashCode ^
        title.hashCode ^
        period.hashCode ^
        incidentDate.hashCode ^
        incidentTime.hashCode ^
        longitude.hashCode ^
        latitude.hashCode ^
        description.hashCode ^
        conflictTypeId.hashCode ^
        speciesIds.hashCode;
  }
}