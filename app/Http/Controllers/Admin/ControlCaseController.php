<?php

namespace App\Http\Controllers;
namespace App\Http\Controllers;

use App\Models\ControlCase;
use App\Models\Admin\OrganisationType;
use App\Models\Species;
use App\Models\Organisation;
use Illuminate\Http\Request;

class ControlCaseController extends Controller
{
    public function index()
    {
        $controlCases = ControlCase::all();
        return view('reports.control_cases.index', compact('controlCases'));
    }

    public function create()
    {
        $species = Species::all();
        $years = range(date('Y'), 2018);

        $organisationType = OrganisationType::where('name', 'like', '%Rural District Council%')->first();
        $organisations = Organisation::where('organisation_type_id', $organisationType->id)->get();

        return view('reports.control_cases.create', compact('species', 'organisations', 'years'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'organisation_id' => 'required|exists:organisations,id',
            'period' => 'required|integer|min:2018|max:' . (date('Y') + 1),
            'records.*.species_id' => 'sometimes|required|exists:species,id',
            'records.*.cases' => 'sometimes|required|integer',
            'records.*.killed' => 'sometimes|required|integer',
            'records.*.scared' => 'sometimes|required|integer',
            'records.*.relocated' => 'sometimes|required|integer',
        ]);

        foreach ($data['records'] as $recordData) {
            if (isset($recordData['species_id'])) {
                ControlCase::create([
                    'organisation_id' => $data['organisation_id'],
                    'species_id' => $recordData['species_id'],
                    'period' => $data['period'],
                    'cases' => $recordData['cases'] ?? 0,
                    'killed' => $recordData['killed'] ?? 0,
                    'scared' => $recordData['scared'] ?? 0,
                    'relocated' => $recordData['relocated'] ?? 0,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Control cases saved successfully.');
    }

    public function show(ControlCase $controlCase)
    {
        //
    }

    public function edit(ControlCase $controlCase)
    {
        //
    }

    public function update(Request $request, ControlCase $controlCase)
    {
        //
    }

    public function destroy(ControlCase $controlCase)
    {
        $controlCase->delete();
        return redirect()->back()->with('success', 'Control case deleted successfully.');
    }
}
