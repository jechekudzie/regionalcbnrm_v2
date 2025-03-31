<?php

namespace App\Http\Controllers\HistoricalData;

use App\Http\Controllers\Controller;
use App\Models\HistoricalData\AnimalControlRecord;
use App\Models\Admin\Organisation;
use App\Models\Organisation\Species;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AnimalControlRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Organisation $organisation)
    {
        $animalControlRecords = AnimalControlRecord::where('organisation_id', $organisation->id)
            ->orderBy('period', 'desc')
            ->get();
            
        return view('organisation.historical-data.animal-controls.index', compact('organisation', 'animalControlRecords'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Organisation $organisation)
    {
        $species = Species::orderBy('name')->get();
        
        return view('organisation.historical-data.animal-controls.create', compact('organisation', 'species'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Organisation $organisation)
    {
        $validated = $request->validate([
            'period' => [
                'required',
                'integer',
                'min:1900',
                'max:' . date('Y'),
                Rule::unique('animal_control_records')->where(function ($query) use ($request, $organisation) {
                    return $query->where('organisation_id', $organisation->id)
                                ->where('species_id', $request->species_id);
                }),
            ],
            'species_id' => 'required|exists:species,id',
            'number_of_cases' => 'required|integer|min:0',
            'killed' => 'required|integer|min:0',
            'relocated' => 'required|integer|min:0',
            'scared' => 'required|integer|min:0',
            'injured' => 'required|integer|min:0',
        ], [
            'period.unique' => 'A record for this species in this year already exists for this organisation.'
        ]);

        $validated['organisation_id'] = $organisation->id;

        AnimalControlRecord::create($validated);

        return redirect()->route('animal_control_records.index', $organisation->slug)
            ->with('success', 'Animal control record has been added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Organisation $organisation, AnimalControlRecord $animalControl)
    {
        return view('organisation.historical-data.animal-controls.show', compact('organisation', 'animalControl'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Organisation $organisation, AnimalControlRecord $animalControl)
    {
        $species = Species::orderBy('name')->get();
        
        return view('organisation.historical-data.animal-controls.edit', compact('organisation', 'animalControl', 'species'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Organisation $organisation, AnimalControlRecord $animalControl)
    {
        $validated = $request->validate([
            'period' => [
                'required',
                'integer',
                'min:1900',
                'max:' . date('Y'),
                Rule::unique('animal_control_records')->where(function ($query) use ($request, $organisation) {
                    return $query->where('organisation_id', $organisation->id)
                                ->where('species_id', $request->species_id);
                })->ignore($animalControl->id),
            ],
            'species_id' => 'required|exists:species,id',
            'number_of_cases' => 'required|integer|min:0',
            'killed' => 'required|integer|min:0',
            'relocated' => 'required|integer|min:0',
            'scared' => 'required|integer|min:0',
            'injured' => 'required|integer|min:0',
        ], [
            'period.unique' => 'A record for this species in this year already exists for this organisation.'
        ]);

        $animalControl->update($validated);

        return redirect()->route('animal_control_records.index', $organisation->slug)
            ->with('success', 'Animal control record has been updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organisation $organisation, AnimalControlRecord $animalControl)
    {
        $animalControl->delete();

        return redirect()->route('animal_control_records.index', $organisation->slug)
            ->with('success', 'Animal control record has been deleted successfully.');
    }
}
