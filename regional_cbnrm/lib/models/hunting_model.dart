import 'package:regional_cbnrm/models/species.dart';

import 'user_model.dart';

class HuntingActivity {
  final int? id;
  final int organisationId;
  final int huntingConcessionId;
  final int safariOperatorId;
  final String period;
  final DateTime startDate;
  final DateTime endDate;
  final String clientName;
  final String clientNationality;
  final String clientCountryOfResidence;
  final DateTime? createdAt;
  final DateTime? updatedAt;
  final String? syncStatus;
  final int? remoteId;
  
  // Relations
  final HuntingConcession? huntingConcession;
  final Organisation? safariOperator;
  final List<HuntingActivitySpecies>? species;
  final List<ProfessionalHunterLicense>? professionalHunterLicenses;

  HuntingActivity({
    this.id,
    required this.organisationId,
    required this.huntingConcessionId,
    required this.safariOperatorId,
    required this.period,
    required this.startDate,
    required this.endDate,
    required this.clientName,
    required this.clientNationality,
    required this.clientCountryOfResidence,
    this.createdAt,
    this.updatedAt,
    this.syncStatus = 'pending',
    this.remoteId,
    this.huntingConcession,
    this.safariOperator,
    this.species,
    this.professionalHunterLicenses,
  });

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'organisation_id': organisationId,
      'hunting_concession_id': huntingConcessionId,
      'safari_operator_id': safariOperatorId,
      'period': period,
      'start_date': startDate.toIso8601String().split('T')[0],
      'end_date': endDate.toIso8601String().split('T')[0],
      'client_name': clientName,
      'client_nationality': clientNationality,
      'client_country_of_residence': clientCountryOfResidence,
      'created_at': createdAt?.toIso8601String(),
      'updated_at': updatedAt?.toIso8601String(),
      'sync_status': syncStatus,
      'remote_id': remoteId,
      'hunting_concession': huntingConcession?.toJson(),
      'safari_operator': safariOperator?.toJson(),
      'species': species?.map((s) => s.toJson()).toList(),
      'professional_hunter_licenses': professionalHunterLicenses?.map((p) => p.toJson()).toList(),
    };
  }

  factory HuntingActivity.fromJson(Map<String, dynamic> json) {
    return HuntingActivity(
      id: json['id'],
      organisationId: json['organisation_id'],
      huntingConcessionId: json['hunting_concession_id'],
      safariOperatorId: json['safari_operator_id'],
      period: json['period'],
      startDate: DateTime.parse(json['start_date']),
      endDate: DateTime.parse(json['end_date']),
      clientName: json['client_name'],
      clientNationality: json['client_nationality'],
      clientCountryOfResidence: json['client_country_of_residence'],
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : null,
      updatedAt: json['updated_at'] != null ? DateTime.parse(json['updated_at']) : null,
      syncStatus: json['sync_status'] ?? 'synced',
      remoteId: json['remote_id'] ?? json['id'],
      huntingConcession: json['hunting_concession'] != null
          ? HuntingConcession.fromJson(json['hunting_concession'])
          : null,
      safariOperator: json['safari_operator'] != null ? Organisation.fromJson(json['safari_operator']) : null,
      species: json['species'] != null
          ? (json['species'] as List).map((s) => HuntingActivitySpecies.fromJson(s)).toList()
          : null,
      professionalHunterLicenses: json['professional_hunter_licenses'] != null
          ? (json['professional_hunter_licenses'] as List)
              .map((p) => ProfessionalHunterLicense.fromJson(p))
              .toList()
          : null,
    );
  }

  Map<String, dynamic> toApiJson() {
    return {
      'organisation_id': organisationId,
      'hunting_concession_id': huntingConcessionId,
      'safari_operator_id': safariOperatorId,
      'period': period,
      'start_date': startDate.toIso8601String().split('T')[0],
      'end_date': endDate.toIso8601String().split('T')[0],
      'client_name': clientName,
      'client_nationality': clientNationality,
      'client_country_of_residence': clientCountryOfResidence,
      'species': species?.map((s) => s.toApiJson()).toList(),
      'professional_hunters': professionalHunterLicenses?.map((p) => p.toApiJson()).toList(),
    };
  }

  HuntingActivity copyWith({
    int? id,
    int? organisationId,
    int? huntingConcessionId,
    int? safariOperatorId,
    String? period,
    DateTime? startDate,
    DateTime? endDate,
    String? clientName,
    String? clientNationality,
    String? clientCountryOfResidence,
    DateTime? createdAt,
    DateTime? updatedAt,
    String? syncStatus,
    int? remoteId,
    HuntingConcession? huntingConcession,
    Organisation? safariOperator,
    List<HuntingActivitySpecies>? species,
    List<ProfessionalHunterLicense>? professionalHunterLicenses,
  }) {
    return HuntingActivity(
      id: id ?? this.id,
      organisationId: organisationId ?? this.organisationId,
      huntingConcessionId: huntingConcessionId ?? this.huntingConcessionId,
      safariOperatorId: safariOperatorId ?? this.safariOperatorId,
      period: period ?? this.period,
      startDate: startDate ?? this.startDate,
      endDate: endDate ?? this.endDate,
      clientName: clientName ?? this.clientName,
      clientNationality: clientNationality ?? this.clientNationality,
      clientCountryOfResidence: clientCountryOfResidence ?? this.clientCountryOfResidence,
      createdAt: createdAt ?? this.createdAt,
      updatedAt: updatedAt ?? this.updatedAt,
      syncStatus: syncStatus ?? this.syncStatus,
      remoteId: remoteId ?? this.remoteId,
      huntingConcession: huntingConcession ?? this.huntingConcession,
      safariOperator: safariOperator ?? this.safariOperator,
      species: species ?? this.species,
      professionalHunterLicenses: professionalHunterLicenses ?? this.professionalHunterLicenses,
    );
  }
}

class HuntingActivitySpecies {
  final int? id;
  final int huntingActivityId;
  final int speciesId;
  final int quantity;
  final int? quotaAllocationBalanceId;
  final DateTime? createdAt;
  final DateTime? updatedAt;
  
  // Relations
  final Species? species;
  final QuotaAllocationBalance? quotaAllocationBalance;

  HuntingActivitySpecies({
    this.id,
    required this.huntingActivityId,
    required this.speciesId,
    required this.quantity,
    this.quotaAllocationBalanceId,
    this.createdAt,
    this.updatedAt,
    this.species,
    this.quotaAllocationBalance,
  });

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'hunting_activity_id': huntingActivityId,
      'species_id': speciesId,
      'quantity': quantity,
      'quota_allocation_balance_id': quotaAllocationBalanceId,
      'created_at': createdAt?.toIso8601String(),
      'updated_at': updatedAt?.toIso8601String(),
      'species': species?.toApiJson(),
      'quota_allocation_balance': quotaAllocationBalance?.toJson(),
    };
  }

  factory HuntingActivitySpecies.fromJson(Map<String, dynamic> json) {
    return HuntingActivitySpecies(
      id: json['id'],
      huntingActivityId: json['hunting_activity_id'],
      speciesId: json['species_id'],
      quantity: json['quantity'],
      quotaAllocationBalanceId: json['quota_allocation_balance_id'],
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : null,
      updatedAt: json['updated_at'] != null ? DateTime.parse(json['updated_at']) : null,
      species: json['species'] != null ? Species.fromApiJson(json['species']) : null,
      quotaAllocationBalance: json['quota_allocation_balance'] != null
          ? QuotaAllocationBalance.fromJson(json['quota_allocation_balance'])
          : null,
    );
  }

  Map<String, dynamic> toApiJson() {
    return {
      'species_id': speciesId,
      'quantity': quantity,
    };
  }
}

class ProfessionalHunterLicense {
  final int? id;
  final int huntingActivityId;
  final String name;
  final String licenseNumber;
  final DateTime? createdAt;
  final DateTime? updatedAt;

  ProfessionalHunterLicense({
    this.id,
    required this.huntingActivityId,
    required this.name,
    required this.licenseNumber,
    this.createdAt,
    this.updatedAt,
  });

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'hunting_activity_id': huntingActivityId,
      'name': name,
      'license_number': licenseNumber,
      'created_at': createdAt?.toIso8601String(),
      'updated_at': updatedAt?.toIso8601String(),
    };
  }

  factory ProfessionalHunterLicense.fromJson(Map<String, dynamic> json) {
    return ProfessionalHunterLicense(
      id: json['id'],
      huntingActivityId: json['hunting_activity_id'],
      name: json['name'],
      licenseNumber: json['license_number'],
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : null,
      updatedAt: json['updated_at'] != null ? DateTime.parse(json['updated_at']) : null,
    );
  }

  Map<String, dynamic> toApiJson() {
    return {
      'name': name,
      'license_number': licenseNumber,
    };
  }
}

class HuntingConcession {
  final int id;
  final int organisationId;
  final int? safariId;
  final String name;
  final double? hectarage;
  final String? description;
  final double? latitude;
  final double? longitude;
  final DateTime? createdAt;
  final DateTime? updatedAt;
  
  // Relations
  final Organisation? safariOperator;

  HuntingConcession({
    required this.id,
    required this.organisationId,
    this.safariId,
    required this.name,
    this.hectarage,
    this.description,
    this.latitude,
    this.longitude,
    this.createdAt,
    this.updatedAt,
    this.safariOperator,
  });

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'organisation_id': organisationId,
      'safari_id': safariId,
      'name': name,
      'hectarage': hectarage,
      'description': description,
      'latitude': latitude,
      'longitude': longitude,
      'created_at': createdAt?.toIso8601String(),
      'updated_at': updatedAt?.toIso8601String(),
      'safari_operator': safariOperator?.toJson(),
    };
  }

  factory HuntingConcession.fromJson(Map<String, dynamic> json) {
    return HuntingConcession(
      id: json['id'],
      organisationId: json['organisation_id'],
      safariId: json['safari_id'],
      name: json['name'],
      hectarage: json['hectarage'] != null
          ? (json['hectarage'] is String ? double.parse(json['hectarage']) : json['hectarage'])
          : null,
      description: json['description'],
      latitude: json['latitude'] != null
          ? (json['latitude'] is String ? double.parse(json['latitude']) : json['latitude'])
          : null,
      longitude: json['longitude'] != null
          ? (json['longitude'] is String ? double.parse(json['longitude']) : json['longitude'])
          : null,
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : null,
      updatedAt: json['updated_at'] != null ? DateTime.parse(json['updated_at']) : null,
      safariOperator: json['safari_operator'] != null ? Organisation.fromJson(json['safari_operator']) : null,
    );
  }
}

class QuotaAllocation {
  final int id;
  final int organisationId;
  final int speciesId;
  final int huntingQuota;
  final int rationalKillingQuota;
  final String period;
  final DateTime startDate;
  final DateTime endDate;
  final DateTime? createdAt;
  final DateTime? updatedAt;
  
  // Relations
  final Species? species;
  final QuotaAllocationBalance? quotaAllocationBalance;

  QuotaAllocation({
    required this.id,
    required this.organisationId,
    required this.speciesId,
    required this.huntingQuota,
    required this.rationalKillingQuota,
    required this.period,
    required this.startDate,
    required this.endDate,
    this.createdAt,
    this.updatedAt,
    this.species,
    this.quotaAllocationBalance,
  });

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'organisation_id': organisationId,
      'species_id': speciesId,
      'hunting_quota': huntingQuota,
      'rational_killing_quota': rationalKillingQuota,
      'period': period,
      'start_date': startDate.toIso8601String().split('T')[0],
      'end_date': endDate.toIso8601String().split('T')[0],
      'created_at': createdAt?.toIso8601String(),
      'updated_at': updatedAt?.toIso8601String(),
      'species': species?.toApiJson(),
      'quota_allocation_balance': quotaAllocationBalance?.toJson(),
    };
  }

  factory QuotaAllocation.fromJson(Map<String, dynamic> json) {
    return QuotaAllocation(
      id: json['id'],
      organisationId: json['organisation_id'],
      speciesId: json['species_id'],
      huntingQuota: json['hunting_quota'],
      rationalKillingQuota: json['rational_killing_quota'],
      period: json['period'],
      startDate: DateTime.parse(json['start_date']),
      endDate: DateTime.parse(json['end_date']),
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : null,
      updatedAt: json['updated_at'] != null ? DateTime.parse(json['updated_at']) : null,
      species: json['species'] != null ? Species.fromApiJson(json['species']) : null,
      quotaAllocationBalance: json['quota_allocation_balance'] != null
          ? QuotaAllocationBalance.fromJson(json['quota_allocation_balance'])
          : null,
    );
  }
  
  QuotaAllocation copyWith({
    int? id,
    int? organisationId,
    int? speciesId,
    int? huntingQuota,
    int? rationalKillingQuota,
    String? period,
    DateTime? startDate,
    DateTime? endDate,
    DateTime? createdAt,
    DateTime? updatedAt,
    Species? species,
    QuotaAllocationBalance? quotaAllocationBalance,
  }) {
    return QuotaAllocation(
      id: id ?? this.id,
      organisationId: organisationId ?? this.organisationId,
      speciesId: speciesId ?? this.speciesId,
      huntingQuota: huntingQuota ?? this.huntingQuota,
      rationalKillingQuota: rationalKillingQuota ?? this.rationalKillingQuota,
      period: period ?? this.period,
      startDate: startDate ?? this.startDate,
      endDate: endDate ?? this.endDate,
      createdAt: createdAt ?? this.createdAt,
      updatedAt: updatedAt ?? this.updatedAt,
      species: species ?? this.species,
      quotaAllocationBalance: quotaAllocationBalance ?? this.quotaAllocationBalance,
    );
  }
}

class QuotaAllocationBalance {
  final int id;
  final int quotaAllocationId;
  final int allocated;
  final int offTake;
  final int remaining;
  final DateTime? createdAt;
  final DateTime? updatedAt;

  QuotaAllocationBalance({
    required this.id,
    required this.quotaAllocationId,
    required this.allocated,
    required this.offTake,
    required this.remaining,
    this.createdAt,
    this.updatedAt,
  });

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'quota_allocation_id': quotaAllocationId,
      'allocated': allocated,
      'off_take': offTake,
      'remaining': remaining,
      'created_at': createdAt?.toIso8601String(),
      'updated_at': updatedAt?.toIso8601String(),
    };
  }

  factory QuotaAllocationBalance.fromJson(Map<String, dynamic> json) {
    return QuotaAllocationBalance(
      id: json['id'],
      quotaAllocationId: json['quota_allocation_id'],
      allocated: json['allocated'],
      offTake: json['off_take'],
      remaining: json['remaining'],
      createdAt: json['created_at'] != null ? DateTime.parse(json['created_at']) : null,
      updatedAt: json['updated_at'] != null ? DateTime.parse(json['updated_at']) : null,
    );
  }
}