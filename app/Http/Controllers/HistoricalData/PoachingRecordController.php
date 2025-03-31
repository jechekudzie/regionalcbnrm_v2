<?php

namespace App\Http\Controllers\HistoricalData;

use App\Http\Controllers\Controller;
use App\Models\HistoricalData\PoachingRecord;
use App\Models\Admin\Organisation;
use App\Models\Organisation\Species;
use App\Models\Admin\PoachingMethod;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PoachingRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Organisation $organisation)
    {
        $poachingRecords = PoachingRecord::where('organisation_id', $organisation->id)
            ->orderBy('period', 'desc')
            ->get();
            
        return view('organisation.historical-data.poaching-records.index', compact('organisation', 'poachingRecords'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Organisation $organisation)
    {
        $species = Species::orderBy('name')->get();
        $poachingMethods = PoachingMethod::orderBy('name')->get();
        
        return view('organisation.historical-data.poaching-records.create', compact('organisation', 'species', 'poachingMethods'));
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
                Rule::unique('poaching_records')->where(function ($query) use ($request, $organisation) {
                    return $query->where('organisation_id', $organisation->id)
                                ->where('species_id', $request->species_id)
                                ->where('poaching_method_id', $request->poaching_method_id);
                }),
            ],
            'species_id' => 'required|exists:species,id',
            'poaching_method_id' => 'required|exists:poaching_methods,id',
            'number' => 'required|integer|min:0',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ], [
            'period.unique' => 'A record for this species and poaching method in this year already exists for this organisation.'
        ]);

        $validated['organisation_id'] = $organisation->id;

        PoachingRecord::create($validated);

        return redirect()->route('poaching_records.index', $organisation->slug)
            ->with('success', 'Poaching record has been added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Organisation $organisation, PoachingRecord $poachingRecord)
    {
        return view('organisation.historical-data.poaching-records.show', compact('organisation', 'poachingRecord'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Organisation $organisation, PoachingRecord $poachingRecord)
    {
        $species = Species::orderBy('name')->get();
        $poachingMethods = PoachingMethod::orderBy('name')->get();
        
        return view('organisation.historical-data.poaching-records.edit', compact('organisation', 'poachingRecord', 'species', 'poachingMethods'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Organisation $organisation, PoachingRecord $poachingRecord)
    {
        $validated = $request->validate([
            'period' => [
                'required',
                'integer',
                'min:1900',
                'max:' . date('Y'),
                Rule::unique('poaching_records')->where(function ($query) use ($request, $organisation) {
                    return $query->where('organisation_id', $organisation->id)
                                ->where('species_id', $request->species_id)
                                ->where('poaching_method_id', $request->poaching_method_id);
                })->ignore($poachingRecord->id),
            ],
            'species_id' => 'required|exists:species,id',
            'poaching_method_id' => 'required|exists:poaching_methods,id',
            'number' => 'required|integer|min:0',
            'location' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ], [
            'period.unique' => 'A record for this species and poaching method in this year already exists for this organisation.'
        ]);

        $poachingRecord->update($validated);

        return redirect()->route('poaching_records.index', $organisation->slug)
            ->with('success', 'Poaching record has been updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organisation $organisation, PoachingRecord $poachingRecord)
    {
        $poachingRecord->delete();

        return redirect()->route('poaching_records.index', $organisation->slug)
            ->with('success', 'Poaching record has been deleted successfully.');
    }
}
