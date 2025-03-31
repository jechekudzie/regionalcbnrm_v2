<?php

namespace App\Http\Controllers\HistoricalData;

use App\Http\Controllers\Controller;
use App\Models\HistoricalData\CropConflictRecord;
use App\Models\Admin\CropType;
use App\Models\Admin\Organisation;
use App\Models\Organisation\Species;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CropConflictRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Organisation $organisation)
    {
        $cropConflictRecords = CropConflictRecord::where('organisation_id', $organisation->id)
            ->orderBy('period', 'desc')
            ->get();
            
        return view('organisation.historical-data.crop-conflicts.index', compact('organisation', 'cropConflictRecords'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Organisation $organisation)
    {
        $species = Species::orderBy('name')->get();
        $cropTypes = CropType::orderBy('name')->get();
        
        return view('organisation.historical-data.crop-conflicts.create', compact('organisation', 'species', 'cropTypes'));
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
                Rule::unique('crop_conflict_records')->where(function ($query) use ($request, $organisation) {
                    return $query->where('organisation_id', $organisation->id)
                                ->where('species_id', $request->species_id)
                                ->where('crop_type_id', $request->crop_type_id);
                }),
            ],
            'species_id' => 'required|exists:species,id',
            'crop_type_id' => 'required|exists:crop_types,id',
            'hectrage_destroyed' => 'required|numeric|min:0',
        ], [
            'period.unique' => 'A record for this species and crop type in this year already exists for this organisation.'
        ]);

        $validated['organisation_id'] = $organisation->id;

        CropConflictRecord::create($validated);

        return back()->with('success', 'Crop conflict record has been added successfully.');

        //return redirect()->route('crop_conflict_records.index', $organisation->slug)->with('success', 'Crop conflict record has been added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Organisation $organisation, CropConflictRecord $cropConflict)
    {
        return view('organisation.historical-data.crop-conflicts.show', compact('organisation', 'cropConflict'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Organisation $organisation, CropConflictRecord $cropConflict)
    {
        $species = Species::orderBy('name')->get();
        $cropTypes = CropType::orderBy('name')->get();
        
        return view('organisation.historical-data.crop-conflicts.edit', compact('organisation', 'cropConflict', 'species', 'cropTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Organisation $organisation, CropConflictRecord $cropConflict)
    {
        $validated = $request->validate([
            'period' => [
                'required',
                'integer',
                'min:1900',
                'max:' . date('Y'),
                Rule::unique('crop_conflict_records')->where(function ($query) use ($request, $organisation) {
                    return $query->where('organisation_id', $organisation->id)
                                ->where('species_id', $request->species_id)
                                ->where('crop_type_id', $request->crop_type_id);
                })->ignore($cropConflict->id),
            ],
            'species_id' => 'required|exists:species,id',
            'crop_type_id' => 'required|exists:crop_types,id',
            'hectrage_destroyed' => 'required|numeric|min:0',
        ], [
            'period.unique' => 'A record for this species and crop type in this year already exists for this organisation.'
        ]);

        $cropConflict->update($validated);

        return redirect()->route('crop_conflict_records.index', $organisation->slug)
            ->with('success', 'Crop conflict record has been updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organisation $organisation, CropConflictRecord $cropConflict)
    {
        $cropConflict->delete();

        return redirect()->route('crop_conflict_records.index', $organisation->slug)
            ->with('success', 'Crop conflict record has been deleted successfully.');
    }
}
