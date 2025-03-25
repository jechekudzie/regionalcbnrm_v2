<?php

namespace App\Http\Controllers\HistoricalData;

use App\Http\Controllers\Controller;
use App\Models\Gender;
use App\Models\HistoricalData\HumanConflictRecord;
use App\Models\Organisation;
use App\Models\Species;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HumanConflictRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Organisation $organisation)
    {
        $humanConflictRecords = HumanConflictRecord::where('organisation_id', $organisation->id)
            ->orderBy('period', 'desc')
            ->get();
            
        return view('organisation.historical-data.human-conflicts.index', compact('organisation', 'humanConflictRecords'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Organisation $organisation)
    {
        $species = Species::orderBy('name')->get();
        $genders = Gender::orderBy('name')->get();
        
        return view('organisation.historical-data.human-conflicts.create', compact('organisation', 'species', 'genders'));
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
                Rule::unique('human_conflict_records')->where(function ($query) use ($request, $organisation) {
                    return $query->where('organisation_id', $organisation->id)
                                ->where('species_id', $request->species_id)
                                ->where('gender_id', $request->gender_id);
                }),
            ],
            'species_id' => 'required|exists:species,id',
            'gender_id' => 'required|exists:genders,id',
            'deaths' => 'required|integer|min:0',
            'injured' => 'required|integer|min:0',
        ], [
            'period.unique' => 'A record for this species and gender in this year already exists for this organisation.'
        ]);

        $validated['organisation_id'] = $organisation->id;

        HumanConflictRecord::create($validated);

        return back()->with('success', 'Human conflict record has been added successfully.');

        //return redirect()->route('human_conflict_records.index', $organisation->slug)->with('success', 'Human conflict record has been added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Organisation $organisation, HumanConflictRecord $humanConflict)
    {
        return view('organisation.historical-data.human-conflicts.show', compact('organisation', 'humanConflict'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Organisation $organisation, HumanConflictRecord $humanConflict)
    {
        $species = Species::orderBy('name')->get();
        $genders = Gender::orderBy('name')->get();
        
        return view('organisation.historical-data.human-conflicts.edit', compact('organisation', 'humanConflict', 'species', 'genders'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Organisation $organisation, HumanConflictRecord $humanConflict)
    {
        $validated = $request->validate([
            'period' => [
                'required',
                'integer',
                'min:1900',
                'max:' . date('Y'),
                Rule::unique('human_conflict_records')->where(function ($query) use ($request, $organisation) {
                    return $query->where('organisation_id', $organisation->id)
                                ->where('species_id', $request->species_id)
                                ->where('gender_id', $request->gender_id);
                })->ignore($humanConflict->id),
            ],
            'species_id' => 'required|exists:species,id',
            'gender_id' => 'required|exists:genders,id',
            'deaths' => 'required|integer|min:0',
            'injured' => 'required|integer|min:0',
        ], [
            'period.unique' => 'A record for this species and gender in this year already exists for this organisation.'
        ]);

        $humanConflict->update($validated);

        return redirect()->route('human_conflict_records.index', $organisation->slug)
            ->with('success', 'Human conflict record has been updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organisation $organisation, HumanConflictRecord $humanConflict)
    {
        $humanConflict->delete();

        return redirect()->route('human_conflict_records.index', $organisation->slug)
            ->with('success', 'Human conflict record has been deleted successfully.');
    }
}
