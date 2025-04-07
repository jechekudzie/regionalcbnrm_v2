<?php

namespace App\Http\Controllers\Organisation;

use App\Http\Controllers\Controller;
use App\Models\Admin\Organisation;
use App\Models\Admin\OrganisationType;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Models\Organisation\Organisation as OrganisationModel;
use App\Models\Organisation\QuotaAllocation;
use App\Models\Organisation\HuntingActivity;
use App\Models\Organisation\WildlifeConflictIncident;
use App\Models\Organisation\ProblemAnimalControl;
use App\Models\Organisation\PoachingIncident;
use App\Models\Organisation\HuntingConcession;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OrganisationDashboardController extends Controller
{
    //
    public function dashboard()
    {
        $user = Auth::user();
        $organisations = $user->organisations;

        return view('organisation.dashboard.dashboard', compact('organisations', 'user' ));
    }

    public function index(Organisation $organisation)
    {
        $user = Auth::user();
        
        // Check if the user has a role with the organization
        $userRole = $user ? $user->getFirstCommonRoleWithOrganization($organisation) : null;
        
        // Get species data
        $species = \App\Models\Organisation\Species::all();
        
        // Get hunting data
        $quotaAllocations = $organisation->quotaAllocations()
            ->with('species')
            ->whereYear('start_date', date('Y'))
            ->get();
            
        $huntingActivities = $organisation->huntingActivities()
            ->with(['species', 'huntingConcession'])
            ->whereYear('start_date', date('Y'))
            ->get();
        
        // Calculate utilized vs allocated quota
        $quotaUtilization = [];
        foreach ($species as $s) {
            $quotaUtilization[$s->id] = [
                'species' => $s->name,
                'allocated' => $quotaAllocations->where('species_id', $s->id)->sum('hunting_quota'),
                'utilized' => 0
            ];
        }
        
        foreach ($huntingActivities as $activity) {
            foreach ($activity->species as $s) {
                if (isset($quotaUtilization[$s->id])) {
                    $quotaUtilization[$s->id]['utilized'] += $s->pivot->off_take;
                }
            }
        }
        
        // Get wildlife conflicts data
        $wildlifeConflicts = \App\Models\Organisation\WildlifeConflictIncident::where('organisation_id', $organisation->id)
            ->with(['species', 'conflictType'])
            ->whereYear('incident_date', date('Y'))
            ->get();
            
        // Group conflicts by type
        $conflictsByType = $wildlifeConflicts->groupBy('conflictType.name');
        $conflictTypesData = [];
        foreach ($conflictsByType as $type => $conflicts) {
            $conflictTypesData[] = [
                'type' => $type ?: 'Unspecified',
                'count' => $conflicts->count()
            ];
        }
        
        // Group conflicts by species
        $conflictsBySpecies = [];
        foreach ($wildlifeConflicts as $conflict) {
            foreach ($conflict->species as $s) {
                if (!isset($conflictsBySpecies[$s->id])) {
                    $conflictsBySpecies[$s->id] = [
                        'species' => $s->name,
                        'count' => 0
                    ];
                }
                $conflictsBySpecies[$s->id]['count']++;
            }
        }
        
        // Get problem animal control data
        $problemAnimalControls = \App\Models\Organisation\ProblemAnimalControl::where('organisation_id', $organisation->id)
            ->with('wildlifeConflictIncident.species')
            ->whereYear('control_date', date('Y'))
            ->get();
        
        // Group PAC by species
        $pacBySpecies = [];
        foreach ($problemAnimalControls as $pac) {
            if ($pac->wildlifeConflictIncident) {
                foreach ($pac->wildlifeConflictIncident->species as $s) {
                    if (!isset($pacBySpecies[$s->id])) {
                        $pacBySpecies[$s->id] = [
                            'species' => $s->name,
                            'count' => 0
                        ];
                    }
                    $pacBySpecies[$s->id]['count']++;
                }
            }
        }
        
        // Get poaching incident data
        $poachingIncidents = \App\Models\Organisation\PoachingIncident::where('organisation_id', $organisation->id)
            ->with(['species', 'poachers'])
            ->whereYear('date', date('Y'))
            ->get();
            
        // Group poaching by species
        $poachingBySpecies = [];
        foreach ($poachingIncidents as $incident) {
            foreach ($incident->species as $s) {
                if (!isset($poachingBySpecies[$s->id])) {
                    $poachingBySpecies[$s->id] = [
                        'species' => $s->name,
                        'count' => 0,
                        'estimatedAnimals' => 0
                    ];
                }
                $poachingBySpecies[$s->id]['count']++;
                $poachingBySpecies[$s->id]['estimatedAnimals'] += $s->pivot->estimate_number;
            }
        }
        
        // Calculate monthly statistics for current year
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $monthlyHuntingData = array_fill_keys($months, 0);
        $monthlyConflictData = array_fill_keys($months, 0);
        $monthlyPoachingData = array_fill_keys($months, 0);
        
        foreach ($huntingActivities as $activity) {
            $month = date('M', strtotime($activity->start_date));
            $monthlyHuntingData[$month]++;
        }
        
        foreach ($wildlifeConflicts as $conflict) {
            $month = date('M', strtotime($conflict->incident_date));
            $monthlyConflictData[$month]++;
        }
        
        foreach ($poachingIncidents as $incident) {
            $month = date('M', strtotime($incident->date));
            $monthlyPoachingData[$month]++;
        }
        
        // Get hunting concessions
        $huntingConcessions = $organisation->huntingConcessions()->get();
        
        // Recent activities (combined and sorted)
        $recentActivities = collect();
        
        foreach ($huntingActivities->take(5) as $activity) {
            $recentActivities->push([
                'type' => 'Hunting Activity',
                'title' => 'Safari at ' . ($activity->huntingConcession->name ?? 'Unknown Concession'),
                'date' => $activity->start_date,
                'details' => $activity->species->count() . ' species, ' . $activity->species->sum('pivot.off_take') . ' animals'
            ]);
        }
        
        foreach ($wildlifeConflicts->take(5) as $conflict) {
            $recentActivities->push([
                'type' => 'Wildlife Conflict',
                'title' => $conflict->title,
                'date' => $conflict->incident_date,
                'details' => ($conflict->conflictType->name ?? 'Unspecified type') . ', ' . 
                             $conflict->species->pluck('name')->implode(', ')
            ]);
        }
        
        foreach ($poachingIncidents->take(5) as $incident) {
            $recentActivities->push([
                'type' => 'Poaching Incident',
                'title' => $incident->title,
                'date' => $incident->date,
                'details' => $incident->poachers->count() . ' poachers, ' . 
                             $incident->species->pluck('name')->implode(', ')
            ]);
        }
        
        $recentActivities = $recentActivities->sortByDesc('date')->take(10);
        
        return view('organisation.dashboard.show', compact(
            'organisation', 
            'user',
            'userRole',
            'species',
            'quotaAllocations',
            'huntingActivities',
            'quotaUtilization',
            'wildlifeConflicts',
            'conflictTypesData',
            'conflictsBySpecies',
            'problemAnimalControls',
            'pacBySpecies',
            'poachingIncidents',
            'poachingBySpecies',
            'months',
            'monthlyHuntingData',
            'monthlyConflictData',
            'monthlyPoachingData',
            'huntingConcessions',
            'recentActivities'
        ));
    }

    public function checkDashboardAccess(Organisation $organisation)
    {
        $user = Auth::user();

        $organisationType = OrganisationType::where('name', 'like', '%Rural District Council%')->first();
        $ruralDistrictCouncils = Organisation::where('organisation_type_id', $organisationType->id)->get();

        if ($user->hasPermissionTo('view-generic')) {
            return view('organisation.dashboard.organisations', compact('organisation', 'user', 'ruralDistrictCouncils'));
        } else {
            return view('organisation.dashboard.index', compact('organisation', 'user'));
        }
    }

    //ruralDistrictCouncils
    public function ruralDistrictCouncils()
    {
        $organisationType = OrganisationType::where('name', 'like', '%Rural District Council%')->first();
        $ruralDistrictCouncils = Organisation::where('organisation_type_id', $organisationType->id)->get();
        return view('organisation.dashboard.rural-district-councils', compact('ruralDistrictCouncils'));
    }

    /**
     * Display the historical dashboard with data from 2019-2023.
     */
    public function historicalDashboard(Organisation $organisation)
    {
        $user = Auth::user();
        
        // Check if the user has a role with the organization
        $userRole = $user ? $user->getFirstCommonRoleWithOrganization($organisation) : null;
        
        // Get species data
        $species = \App\Models\Organisation\Species::all();
        
        // Get years for historical data
        $years = [2019, 2020, 2021, 2022, 2023];
        $currentYear = date('Y');
        
        // Get hunting records data
        $huntingRecords = \App\Models\HistoricalData\HuntingRecord::where('organisation_id', $organisation->id)
            ->with('species')
            ->whereIn('period', $years)
            ->get();
            
        // Calculate allocated vs utilized quota by year
        $yearlyQuotaData = [];
        foreach ($years as $year) {
            $yearlyQuotaData[$year] = [
                'allocated' => $huntingRecords->where('period', $year)->sum('allocated'),
                'utilised' => $huntingRecords->where('period', $year)->sum('utilised')
            ];
        }
        
        // Get top species for hunting
        $topHuntingSpecies = $huntingRecords->groupBy('species_id')
            ->map(function ($records, $speciesId) {
                $species = $records->first()->species;
                return [
                    'species' => $species->name,
                    'allocated' => $records->sum('allocated'),
                    'utilised' => $records->sum('utilised')
                ];
            })
            ->sortByDesc('utilised')
            ->take(5)
            ->values()
            ->toArray();
        
        // Get conflict records data
        $conflictRecords = \App\Models\HistoricalData\ConflictRecord::where('organisation_id', $organisation->id)
            ->with('species')
            ->whereIn('period', $years)
            ->get();
            
        // Calculate conflict data by year
        $yearlyConflictData = [];
        foreach ($years as $year) {
            $yearRecords = $conflictRecords->where('period', $year);
            $yearlyConflictData[$year] = [
                'crop_damage' => $yearRecords->sum('crop_damage_cases'),
                'human_injured' => $yearRecords->sum('human_injured'),
                'human_death' => $yearRecords->sum('human_death'),
                'livestock_killed_injured' => $yearRecords->sum('livestock_killed_injured'),
                'infrastructure_destroyed' => $yearRecords->sum('infrastructure_destroyed')
            ];
        }
        
        // Get top species for conflicts
        $topConflictSpecies = $conflictRecords->groupBy('species_id')
            ->map(function ($records, $speciesId) {
                $species = $records->first()->species;
                return [
                    'species' => $species->name,
                    'crop_damage' => $records->sum('crop_damage_cases'),
                    'human_impact' => $records->sum('human_injured') + $records->sum('human_death'),
                    'livestock_impact' => $records->sum('livestock_killed_injured')
                ];
            })
            ->sortByDesc(function ($data) {
                return $data['crop_damage'] + $data['human_impact'] + $data['livestock_impact'];
            })
            ->take(5)
            ->values()
            ->toArray();
            
        // Get animal control records
        $animalControlRecords = \App\Models\HistoricalData\AnimalControlRecord::where('organisation_id', $organisation->id)
            ->with('species')
            ->whereIn('period', $years)
            ->get();
            
        // Calculate animal control data by year
        $yearlyControlData = [];
        foreach ($years as $year) {
            $yearlyControlData[$year] = $animalControlRecords->where('period', $year)->sum('total');
        }
        
        // Top control species
        $topControlSpecies = $animalControlRecords->groupBy('species_id')
            ->map(function ($records, $speciesId) {
                $species = $records->first()->species;
                return [
                    'species' => $species->name,
                    'total' => $records->sum('total')
                ];
            })
            ->sortByDesc('total')
            ->take(5)
            ->values()
            ->toArray();
            
        // Get poaching data
        $poachingRecords = \App\Models\HistoricalData\PoachingRecord::where('organisation_id', $organisation->id)
            ->with(['species', 'poachingMethod'])
            ->whereIn('period', $years)
            ->get();
            
        // Calculate poaching data by year
        $yearlyPoachingData = [];
        foreach ($years as $year) {
            $yearlyPoachingData[$year] = $poachingRecords->where('period', $year)->sum('number');
        }
        
        // Top poached species
        $topPoachingSpecies = $poachingRecords->groupBy('species_id')
            ->map(function ($records, $speciesId) {
                $species = $records->first()->species;
                return [
                    'species' => $species->name,
                    'number' => $records->sum('number')
                ];
            })
            ->sortByDesc('number')
            ->take(5)
            ->values()
            ->toArray();
            
        // Get income data
        $incomeRecords = \App\Models\HistoricalData\IncomeRecord::where('organisation_id', $organisation->id)
            ->whereIn('period', $years)
            ->get();
            
        // Get income by year
        $yearlyIncomeData = [];
        foreach ($years as $year) {
            $yearRecords = $incomeRecords->where('period', $year)->first();
            $yearlyIncomeData[$year] = [
                'rdc_share' => $yearRecords ? $yearRecords->rdc_share : 0,
                'community_share' => $yearRecords ? $yearRecords->community_share : 0,
                'ca_share' => $yearRecords ? $yearRecords->ca_share : 0,
                'total' => $yearRecords ? $yearRecords->rdc_share + $yearRecords->community_share + $yearRecords->ca_share : 0
            ];
        }
        
        // Get income sources
        $incomeSourceRecords = \App\Models\HistoricalData\SourceOfIncomeRecord::where('organisation_id', $organisation->id)
            ->whereIn('period', $years)
            ->get();
            
        // Calculate income sources data
        $incomeSourcesData = [
            'safari_hunting' => $incomeSourceRecords->sum('safari_hunting'),
            'tourism' => $incomeSourceRecords->sum('tourism'),
            'fishing' => $incomeSourceRecords->sum('fishing'),
            'problem_animal_control' => $incomeSourceRecords->sum('problem_animal_control'),
            'carbon_credits' => $incomeSourceRecords->sum('carbon_credits'),
            'other' => $incomeSourceRecords->sum('ivory_sales') + 
                        $incomeSourceRecords->sum('hide_sales') + 
                        $incomeSourceRecords->sum('meat_sales') + 
                        $incomeSourceRecords->sum('donations_grants') + 
                        $incomeSourceRecords->sum('miscellaneous')
        ];
        
        return view('organisation.dashboard.historical', compact(
            'organisation',
            'user',
            'userRole',
            'years',
            'currentYear',
            'species',
            'yearlyQuotaData',
            'topHuntingSpecies',
            'yearlyConflictData',
            'topConflictSpecies',
            'yearlyControlData',
            'topControlSpecies',
            'yearlyPoachingData',
            'topPoachingSpecies',
            'yearlyIncomeData',
            'incomeSourcesData'
        ));
    }
}
