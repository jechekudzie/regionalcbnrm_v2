class AppRoutes {
  // Core routes
  static const String splash = '/';
  static const String login = '/login';
  static const String register = '/register';
  static const String dashboard = '/dashboard';
  static const String profile = '/profile';

  // Organisation routes
  static const String organisations = '/organisations';
  static const String organisationDetails = '/organisations/details';

  // Wildlife conflict routes
  static const String wildlifeConflicts = '/wildlife-conflicts';
  static const String wildlifeConflictDetails = '/wildlife-conflicts/details';
  static const String createWildlifeConflict = '/wildlife-conflicts/create';
  static const String addConflictOutcome = '/wildlife-conflicts/add-outcome';

  // Problem animal control routes
  static const String problemAnimalControls = '/problem-animal-controls';
  static const String problemAnimalControlDetails = '/problem-animal-controls/details';
  static const String createProblemAnimalControl = '/problem-animal-controls/create';

  // Poaching routes
  static const String poachingIncidents = '/poaching-incidents';
  static const String poachingIncidentDetails = '/poaching-incidents/details';
  static const String createPoachingIncident = '/poaching-incidents/create';
  static const String poachingDetails = '/poaching-incidents/details';
  static const String poachingCreate = '/poaching-incidents/create';
  static const String poachingAddPoacher = '/poaching-incidents/add-poacher';

  // Hunting routes
  static const String huntingActivities = '/hunting-activities';
  static const String huntingActivityDetails = '/hunting-activities/details';
  static const String createHuntingActivity = '/hunting-activities/create';
  static const String huntingConcessions = '/hunting-concessions';
  static const String huntingConcessionDetails = '/hunting-concessions/details';
  static const String createHuntingConcession = '/hunting-concessions/create';
  static const String quotaAllocations = '/quota-allocations';
  static const String quotaAllocationDetails = '/quota-allocations/details';
  static const String createQuotaAllocation = '/quota-allocations/create';

  // Settings and utilities
  static const String settings = '/settings';
  static const String syncData = '/sync-data';

  // Debug routes
  static const String debugSettings = '/debug/settings';
}
