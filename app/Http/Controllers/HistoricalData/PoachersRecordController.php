<?php

namespace App\Http\Controllers\HistoricalData;

use App\Http\Controllers\Controller;
use App\Models\HistoricalData\PoachersRecord;
use App\Models\Organisation;
use App\Models\Species;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PoachersRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Organisation $organisation)
    {
        $poachersRecords = PoachersRecord::where('organisation_id', $organisation->id)
            ->orderBy('period', 'desc')
            ->get();
            
        return view('organisation.historical-data.poachers-records.index', compact('organisation', 'poachersRecords'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Organisation $organisation)
    {
        $species = Species::orderBy('name')->get();
        
        return view('organisation.historical-data.poachers-records.create', compact('organisation', 'species'));
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
                Rule::unique('poachers_records')->where(function ($query) use ($request, $organisation) {
                    return $query->where('organisation_id', $organisation->id)
                                ->where('species_id', $request->species_id);
                }),
            ],
            'species_id' => 'required|exists:species,id',
            'arrested' => 'required|integer|min:0',
            'bailed' => 'required|integer|min:0',
            'sentenced' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ], [
            'period.unique' => 'A record for this species in this year already exists for this organisation.'
        ]);

        $validated['organisation_id'] = $organisation->id;

        PoachersRecord::create($validated);

        return redirect()->route('poachers_records.index', $organisation->slug)
            ->with('success', 'Poachers record has been added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Organisation $organisation, PoachersRecord $poachersRecord)
    {
        return view('organisation.historical-data.poachers-records.show', compact('organisation', 'poachersRecord'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Organisation $organisation, PoachersRecord $poachersRecord)
    {
        $species = Species::orderBy('name')->get();
        
        return view('organisation.historical-data.poachers-records.edit', compact('organisation', 'poachersRecord', 'species'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Organisation $organisation, PoachersRecord $poachersRecord)
    {
        $validated = $request->validate([
            'period' => [
                'required',
                'integer',
                'min:1900',
                'max:' . date('Y'),
                Rule::unique('poachers_records')->where(function ($query) use ($request, $organisation) {
                    return $query->where('organisation_id', $organisation->id)
                                ->where('species_id', $request->species_id);
                })->ignore($poachersRecord->id),
            ],
            'species_id' => 'required|exists:species,id',
            'arrested' => 'required|integer|min:0',
            'bailed' => 'required|integer|min:0',
            'sentenced' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ], [
            'period.unique' => 'A record for this species in this year already exists for this organisation.'
        ]);

        $poachersRecord->update($validated);

        return redirect()->route('poachers_records.index', $organisation->slug)
            ->with('success', 'Poachers record has been updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organisation $organisation, PoachersRecord $poachersRecord)
    {
        $poachersRecord->delete();

        return redirect()->route('poachers_records.index', $organisation->slug)
            ->with('success', 'Poachers record has been deleted successfully.');
    }
}
