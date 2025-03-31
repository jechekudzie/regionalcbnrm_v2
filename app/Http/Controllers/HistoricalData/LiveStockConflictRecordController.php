<?php

namespace App\Http\Controllers\HistoricalData;

use App\Http\Controllers\Controller;
use App\Models\HistoricalData\LiveStockConflictRecord;
use App\Models\Admin\LiveStockType;
use App\Models\Admin\Organisation;
use App\Models\Organisation\Species;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LiveStockConflictRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Organisation $organisation)
    {
        $liveStockConflictRecords = LiveStockConflictRecord::where('organisation_id', $organisation->id)
            ->orderBy('period', 'desc')
            ->get();
            
        return view('organisation.historical-data.livestock-conflicts.index', compact('organisation', 'liveStockConflictRecords'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Organisation $organisation)
    {
        $species = Species::orderBy('name')->get();
        $liveStockTypes = LiveStockType::orderBy('name')->get();
        
        return view('organisation.historical-data.livestock-conflicts.create', compact('organisation', 'species', 'liveStockTypes'));
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
                Rule::unique('live_stock_conflict_records')->where(function ($query) use ($request, $organisation) {
                    return $query->where('organisation_id', $organisation->id)
                                ->where('species_id', $request->species_id)
                                ->where('live_stock_type_id', $request->live_stock_type_id);
                }),
            ],
            'species_id' => 'required|exists:species,id',
            'live_stock_type_id' => 'required|exists:live_stock_types,id',
            'killed' => 'required|integer|min:0',
            'injured' => 'required|integer|min:0',
        ], [
            'period.unique' => 'A record for this species and livestock type in this year already exists for this organisation.'
        ]);

        $validated['organisation_id'] = $organisation->id;

        LiveStockConflictRecord::create($validated);

        return redirect()->route('livestock_conflict_records.index', $organisation->slug)
            ->with('success', 'Livestock conflict record has been added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Organisation $organisation, LiveStockConflictRecord $liveStockConflict)
    {
        return view('organisation.historical-data.livestock-conflicts.show', compact('organisation', 'liveStockConflict'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Organisation $organisation, LiveStockConflictRecord $liveStockConflict)
    {
        $species = Species::orderBy('name')->get();
        $liveStockTypes = LiveStockType::orderBy('name')->get();
        
        return view('organisation.historical-data.livestock-conflicts.edit', compact('organisation', 'liveStockConflict', 'species', 'liveStockTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Organisation $organisation, LiveStockConflictRecord $liveStockConflict)
    {
        $validated = $request->validate([
            'period' => [
                'required',
                'integer',
                'min:1900',
                'max:' . date('Y'),
                Rule::unique('live_stock_conflict_records')->where(function ($query) use ($request, $organisation) {
                    return $query->where('organisation_id', $organisation->id)
                                ->where('species_id', $request->species_id)
                                ->where('live_stock_type_id', $request->live_stock_type_id);
                })->ignore($liveStockConflict->id),
            ],
            'species_id' => 'required|exists:species,id',
            'live_stock_type_id' => 'required|exists:live_stock_types,id',
            'killed' => 'required|integer|min:0',
            'injured' => 'required|integer|min:0',
        ], [
            'period.unique' => 'A record for this species and livestock type in this year already exists for this organisation.'
        ]);

        $liveStockConflict->update($validated);

        return redirect()->route('livestock_conflict_records.index', $organisation->slug)
            ->with('success', 'Livestock conflict record has been updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organisation $organisation, LiveStockConflictRecord $liveStockConflict)
    {
        $liveStockConflict->delete();

        return redirect()->route('livestock_conflict_records.index', $organisation->slug)
            ->with('success', 'Livestock conflict record has been deleted successfully.');
    }
}
