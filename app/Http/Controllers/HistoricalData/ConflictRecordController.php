<?php
namespace App\Http\Controllers\HistoricalData;

use App\Http\Controllers\Controller;

use App\Models\Admin\Organisation;
use App\Models\Admin\OrganisationType;
use App\Models\Organisation\Species;
use App\Models\HistoricalData\ConflictRecord;
use Illuminate\Http\Request;

class ConflictRecordController extends Controller
{

    public function index()
    {
        $organisations = Organisation::all();
        $species = Species::all();
        $years = range(2018, date('Y') + 1);
        $conflictRecords = ConflictRecord::all();
        return view('reports.conflict_records.index', compact('organisations', 'species', 'years', 'conflictRecords'));
    }
    public function create()
    {
        $organisationType = OrganisationType::where('name', 'like', '%Rural District Council%')->first();
        $organisations = Organisation::where('organisation_type_id', $organisationType->id)->get();
        $species = Species::all();
        $years = range(2018, date('Y') + 1);
        $conflictRecords = ConflictRecord::all();
        return view('reports.conflict_records.create', compact('organisations', 'species', 'years', 'conflictRecords'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'organisation_id' => 'required|exists:organisations,id',
            'period' => 'required|integer|min:2018|max:' . (date('Y') + 1),
            'records.*.species_id' => 'sometimes|required|exists:species,id',
            'records.*.crop_damage_cases' => 'sometimes|required|integer',
            'records.*.human_injured' => 'sometimes|required|integer',
            'records.*.human_death' => 'sometimes|required|integer',
            'records.*.livestock_killed_injured' => 'sometimes|required|integer',
            'records.*.infrastructure_destroyed' => 'sometimes|required|integer',
            'records.*.threat_to_human_life' => 'sometimes|required|integer',
        ]);

        foreach ($data['records'] as $recordData) {
            if (isset($recordData['species_id'])) {
                ConflictRecord::create([
                    'organisation_id' => $data['organisation_id'],
                    'species_id' => $recordData['species_id'],
                    'period' => $data['period'],
                    'crop_damage_cases' => $recordData['crop_damage_cases'] ?? 0,
                    'human_injured' => $recordData['human_injured'] ?? 0,
                    'human_death' => $recordData['human_death'] ?? 0,
                    'livestock_killed_injured' => $recordData['livestock_killed_injured'] ?? 0,
                    'infrastructure_destroyed' => $recordData['infrastructure_destroyed'] ?? 0,
                    'threat_to_human_life' => $recordData['threat_to_human_life'] ?? 0,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Conflict records saved successfully.');
    }
}
