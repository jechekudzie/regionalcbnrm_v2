<?php

namespace App\Http\Controllers;

use App\Models\IncomeRecord;
use App\Models\Organisation;
use App\Models\Admin\OrganisationType;
use App\Models\Species;
use Illuminate\Http\Request;
use App\Models\HuntingRecord;
use App\Models\ConflictRecord;
use App\Models\ControlCase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{

    public function index()
    {
        // Fetch and prepare hunting records data
        $huntingRecords = HuntingRecord::with('species')->get();
        $formattedHuntingRecords = $this->formatData($huntingRecords, ['allocated', 'utilised']);

        // Fetch and prepare conflict data
        $conflictRecords = ConflictRecord::with('species')->get();
        $formattedConflictRecords = $this->formatData($conflictRecords, [
            'crop_damage_cases','hectarage_destroyed', 'human_injured', 'human_death',
            'livestock_killed_injured', 'infrastructure_destroyed', 'threat_to_human_life'
        ]);

        // Fetch and prepare control cases data
        $controlCases = ControlCase::with('species')->get();
        $formattedControlCases = $this->formatData($controlCases, [
            'cases', 'killed', 'scared', 'relocated'
        ]);

        return view('reports.main_dashboard', compact('formattedHuntingRecords', 'formattedConflictRecords', 'formattedControlCases'));
    }

    public function index2()
    {
        // Fetch and prepare hunting records data
        $huntingRecords = HuntingRecord::with('species')
            ->select('period', 'species_id', 'allocated', 'utilised')
            ->get()
            ->groupBy('period')
            ->map(function ($period) {
                return $period->groupBy('species.name')->map(function ($species) {
                    return [
                        'allocated' => $species->sum('allocated'),
                        'utilised' => $species->sum('utilised'),
                    ];
                });
            });

        // Fetch and prepare conflict data
        $conflictRecords = ConflictRecord::with('species')
            ->select('period', 'species_id', 'crop_damage_cases', 'hectarage_destroyed','human_injured', 'human_death', 'livestock_killed_injured', 'infrastructure_destroyed', 'threat_to_human_life')
            ->get()
            ->groupBy('period')
            ->map(function ($period) {
                return $period->groupBy('species.name')->map(function ($species) {
                    return [
                        'crop_damage_cases' => $species->sum('crop_damage_cases'),
                        'hectarage_destroyed' => $species->sum('hectarage_destroyed'),
                        'human_injured' => $species->sum('human_injured'),
                        'human_death' => $species->sum('human_death'),
                        'livestock_killed_injured' => $species->sum('livestock_killed_injured'),
                        'infrastructure_destroyed' => $species->sum('infrastructure_destroyed'),
                        'threat_to_human_life' => $species->sum('threat_to_human_life'),
                    ];
                });
            });

        // Fetch and prepare control cases data
        $controlCases = ControlCase::with('species')
            ->select('period', 'species_id', 'cases', 'killed', 'scared', 'relocated')
            ->get()
            ->groupBy('period')
            ->map(function ($period) {
                return $period->groupBy('species.name')->map(function ($species) {
                    return [
                        'cases' => $species->sum('cases'),
                        'killed' => $species->sum('killed'),
                        'scared' => $species->sum('scared'),
                        'relocated' => $species->sum('relocated'),
                    ];
                });
            });

        return view('reports.main_dashboard2', compact('huntingRecords', 'conflictRecords', 'controlCases'));
    }

    private function formatData($data, $metrics)
    {
        return $data->groupBy('period')->map(function ($period) use ($metrics) {
            return $period->groupBy('species.name')->map(function ($speciesData) use ($metrics) {
                $result = [];
                foreach ($metrics as $metric) {
                    $result[$metric] = $speciesData->sum($metric);
                }
                return $result;
            });
        });
    }

    public function incomeRecordsDashboard()
    {
        // Fetch and prepare income records data
        $incomeRecords = IncomeRecord::with('organisation')
            ->get()
            ->groupBy('period')
            ->map(function ($period) {
                return $period->groupBy('organisation.name')->map(function ($orgData) {
                    return [
                        'rdc_share' => $orgData->sum('rdc_share'),
                        'community_share' => $orgData->sum('community_share'),
                        'ca_share' => $orgData->sum('ca_share'),
                    ];
                });
            });

        return view('reports.income_dashboard', compact('incomeRecords'));
    }

    //incomeRecordDashboardBarChart
    public function incomeRecordDashboardBarChart()
    {
        // Fetch and prepare income records data
        $incomeRecords = IncomeRecord::with('organisation')
            ->get()
            ->groupBy('period')
            ->map(function ($period) {
                return $period->groupBy('organisation.name')->map(function ($orgData) {
                    return [
                        'rdc_share' => $orgData->sum('rdc_share'),
                        'community_share' => $orgData->sum('community_share'),
                        'ca_share' => $orgData->sum('ca_share'),
                    ];
                });
            });

        return view('reports.income_dashboard_bar', compact('incomeRecords'));
    }


    public function huntingDashboard()
    {
        $periods = HuntingRecord::select('period')->distinct()->orderBy('period', 'asc')->pluck('period');
        $chartData = [];

        foreach ($periods as $period) {
            $records = HuntingRecord::with('organisation', 'species')
                ->where('period', $period)
                ->get();

            $dataByDistrict = $records->groupBy(function ($item) {
                return $item->organisation->name; // Grouping by district name
            })->map(function ($districtRecords) {
                return $districtRecords->groupBy(function ($item) {
                    return $item->species->name; // Sub-grouping by species name
                })->map(function ($speciesRecords) {
                    return [
                        'allocated' => $speciesRecords->sum('allocated'),
                        'utilised' => $speciesRecords->sum('utilised')
                    ];
                });
            });

            $chartData[$period] = $dataByDistrict;
        }

        return view('reports.hunting_dashboard', compact('chartData', 'periods'));
    }
    public function huntingDashboardByDistrict(Request $request)
    {
        $organisations = Organisation::whereHas('organisationType', function($query) {
            $query->where('name', 'like', '%Rural District Council%');
        })->get();

        $selectedOrganisationId = $request->input('organisation_id', $organisations->first()->id ?? null);
        $periods = HuntingRecord::select('period')->distinct()->orderBy('period', 'asc')->pluck('period');

        $chartData = [];
        if ($selectedOrganisationId) {
            foreach ($periods as $period) {
                $records = HuntingRecord::with('species')
                    ->where('organisation_id', $selectedOrganisationId)
                    ->where('period', $period)
                    ->get();

                $dataBySpecies = $records->groupBy('species.name')->map(function ($items) {
                    return [
                        'allocated' => $items->sum('allocated'),
                        'utilised' => $items->sum('utilised')
                    ];
                });

                $chartData[$period] = $dataBySpecies;
            }
        }

        return view('reports.hunting_dashboard_district', compact('organisations', 'selectedOrganisationId', 'chartData', 'periods'));
    }

    public function huntingDashboardBySpecies(Request $request)
    {
        $species = Species::all(); // Assuming you have a Species model
        $selectedSpeciesId = $request->get('species_id', $species->first()->id);
        $periods = HuntingRecord::select('period')->distinct()->orderBy('period', 'asc')->pluck('period');
        $chartData = [];

        foreach ($periods as $period) {
            $records = HuntingRecord::with('organisation', 'species')
                ->where('species_id', $selectedSpeciesId)
                ->where('period', $period)
                ->get();

            $dataByDistrict = $records->groupBy(function ($item) {
                return $item->organisation->name; // Grouping by district name
            })->map(function ($districtRecords) {
                return [
                    'allocated' => $districtRecords->sum('allocated'),
                    'utilised' => $districtRecords->sum('utilised')
                ];
            });

            $chartData[$period] = $dataByDistrict;
        }

        return view('reports.hunting_dashboard_species', compact('chartData', 'periods', 'species', 'selectedSpeciesId'));
    }

    public function conflictDashboard()
    {
        $periods = ConflictRecord::select('period')->distinct()->orderBy('period', 'asc')->pluck('period');
        $districts = Organisation::whereHas('organisationType', function ($query) {
            $query->where('name', 'like', '%Rural District Council%');
        })->get();

        $chartData = [];

        foreach ($periods as $period) {
            $chartData[$period] = [];

            foreach ($districts as $district) {
                $records = ConflictRecord::where('period', $period)
                    ->where('organisation_id', $district->id)
                    ->get();

                $dataByDistrict = [
                    'crop_damage_cases' => $records->sum('crop_damage_cases'),
                    'hectarage_destroyed' => $records->sum('hectarage_destroyed'),
                    'human_injured' => $records->sum('human_injured'),
                    'human_death' => $records->sum('human_death'),
                    'livestock_killed_injured' => $records->sum('livestock_killed_injured'),
                    'infrastructure_destroyed' => $records->sum('infrastructure_destroyed'),
                    'threat_to_human_life' => $records->sum('threat_to_human_life'),
                ];

                $chartData[$period][$district->name] = $dataByDistrict;
            }
        }

        return view('reports.conflict_dashboard', compact('chartData', 'periods', 'districts'));
    }

    public function conflictDashboardBySpecies(Request $request)
    {
        $species = Species::all();
        $selectedSpeciesId = $request->get('species_id', $species->first()->id ?? null);

        $periods = ConflictRecord::select('period')->distinct()->orderBy('period', 'asc')->pluck('period');
        $districts = Organisation::whereHas('organisationType', function ($query) {
            $query->where('name', 'like', '%Rural District Council%');
        })->get();

        $chartData = [];

        foreach ($periods as $period) {
            $chartData[$period] = [];

            foreach ($districts as $district) {
                $records = ConflictRecord::where('period', $period)
                    ->where('organisation_id', $district->id)
                    ->where('species_id', $selectedSpeciesId)
                    ->get();

                $dataByDistrict = [
                    'crop_damage_cases' => $records->sum('crop_damage_cases'),
                    'hectarage_destroyed' => $records->sum('hectarage_destroyed'),
                    'human_injured' => $records->sum('human_injured'),
                    'human_death' => $records->sum('human_death'),
                    'livestock_killed_injured' => $records->sum('livestock_killed_injured'),
                    'infrastructure_destroyed' => $records->sum('infrastructure_destroyed'),
                    'threat_to_human_life' => $records->sum('threat_to_human_life'),
                ];

                $chartData[$period][$district->name] = $dataByDistrict;
            }
        }

        return view('reports.conflict_dashboard_species', compact('chartData', 'periods', 'species', 'selectedSpeciesId'));
    }

    public function conflictDashboardByDistrict(Request $request)
    {
        $organisationType = OrganisationType::where('name', 'like', '%Rural District Council%')->first();
        $organisations = Organisation::where('organisation_type_id', $organisationType->id)->get();
        $selectedOrganisationId = $request->get('organisation_id', $organisations->first()->id ?? null);

        $periods = ConflictRecord::select('period')->distinct()->orderBy('period', 'asc')->pluck('period');

        $chartData = [];

        foreach ($periods as $period) {
            $records = ConflictRecord::where('period', $period)
                ->where('organisation_id', $selectedOrganisationId)
                ->get();

            $dataBySpecies = $records->groupBy(function ($item) {
                return $item->species->name; // Grouping by species name
            })->map(function ($speciesRecords) {
                return [
                    'crop_damage_cases' => $speciesRecords->sum('crop_damage_cases'),
                    'hectarage_destroyed' => $speciesRecords->sum('hectarage_destroyed'),
                    'human_injured' => $speciesRecords->sum('human_injured'),
                    'human_death' => $speciesRecords->sum('human_death'),
                    'livestock_killed_injured' => $speciesRecords->sum('livestock_killed_injured'),
                    'infrastructure_destroyed' => $speciesRecords->sum('infrastructure_destroyed'),
                    'threat_to_human_life' => $speciesRecords->sum('threat_to_human_life'),
                ];
            });

            $chartData[$period] = $dataBySpecies;
        }

        return view('reports.conflict_dashboard_district', compact('chartData', 'periods', 'organisations', 'selectedOrganisationId'));
    }


    public function controlDashboard()
    {
        $periods = ControlCase::select('period')->distinct()->orderBy('period', 'asc')->pluck('period');
        $districts = Organisation::whereHas('organisationType', function($query) {
            $query->where('name', 'like', '%Rural District Council%');
        })->get();

        $chartData = [];

        foreach ($periods as $period) {
            $chartData[$period] = [];

            foreach ($districts as $district) {
                $records = ControlCase::where('period', $period)
                    ->where('organisation_id', $district->id)
                    ->get();

                // Create a single entry for each district, summing up all necessary fields
                $dataByDistrict = [
                    'cases' => $records->sum('cases'),
                    'killed' => $records->sum('killed'),
                    'scared' => $records->sum('scared'),
                    'relocated' => $records->sum('relocated'),
                ];

                $chartData[$period][$district->name] = $dataByDistrict;
            }
        }

        return view('reports.control_dashboard', compact('chartData', 'periods', 'districts'));
    }

    public function controlDashboardByDistrict(Request $request)
    {
        $periods = ControlCase::select('period')->distinct()->orderBy('period', 'asc')->pluck('period');
        $districts = Organisation::whereHas('organisationType', function($query) {
            $query->where('name', 'like', '%Rural District Council%');
        })->get();

        $selectedOrganisationId = $request->input('organisation_id') ?? $districts->first()->id;

        $species = Species::all();
        $speciesNames = $species->pluck('name', 'id');

        $chartData = [];

        foreach ($periods as $period) {
            $records = ControlCase::where('period', $period)
                ->where('organisation_id', $selectedOrganisationId)
                ->get();

            $dataBySpecies = $records->groupBy('species_id')->map(function ($speciesRecords) {
                return [
                    'cases' => $speciesRecords->sum('cases'),
                    'killed' => $speciesRecords->sum('killed'),
                    'scared' => $speciesRecords->sum('scared'),
                    'relocated' => $speciesRecords->sum('relocated'),
                ];
            });

            $chartData[$period] = $dataBySpecies;
        }

        return view('reports.control_dashboard_district', compact('chartData', 'periods', 'districts', 'selectedOrganisationId', 'speciesNames'));
    }

    public function controlDashboardBySpecies(Request $request)
    {
        $periods = ControlCase::select('period')->distinct()->orderBy('period', 'asc')->pluck('period');
        $species = Species::all();

        $selectedSpeciesId = $request->input('species_id') ?? $species->first()->id;

        $chartData = [];

        foreach ($periods as $period) {
            $records = ControlCase::where('period', $period)
                ->where('species_id', $selectedSpeciesId)
                ->get();

            $dataByDistrict = $records->groupBy('organisation_id')->map(function ($districtRecords) {
                return [
                    'cases' => $districtRecords->sum('cases'),
                    'killed' => $districtRecords->sum('killed'),
                    'scared' => $districtRecords->sum('scared'),
                    'relocated' => $districtRecords->sum('relocated'),
                ];
            });

            $chartData[$period] = $dataByDistrict;
        }

        $districts = Organisation::whereHas('organisationType', function($query) {
            $query->where('name', 'like', '%Rural District Council%');
        })->pluck('name', 'id');

        return view('reports.control_dashboard_species', compact('chartData', 'periods', 'species', 'selectedSpeciesId', 'districts'));
    }


}
