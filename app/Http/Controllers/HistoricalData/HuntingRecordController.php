<?php

namespace App\Http\Controllers\HistoricalData;

use App\Http\Controllers\Controller;
use App\Models\Organisation;
use App\Models\Species;
use App\Models\HistoricalData\HuntingRecord;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HuntingRecordController extends Controller
{
    public function index(Organisation $organisation)
    {
        $huntingRecords = HuntingRecord::where('organisation_id', $organisation->id)
            ->orderBy('period', 'desc')
            ->get();
            
        return view('organisation.historical-data.hunting-records.index', compact('organisation', 'huntingRecords'));
    }

    public function create(Organisation $organisation)
    {
        $species = Species::orderBy('name')->get();
        return view('organisation.historical-data.hunting-records.create', compact('organisation', 'species'));
    }

    public function store(Request $request, Organisation $organisation)
    {
        $validated = $request->validate([
            'period' => [
                'required',
                'integer',
                'min:1900',
                'max:' . date('Y'),
                Rule::unique('hunting_records')->where(function ($query) use ($request, $organisation) {
                    return $query->where('organisation_id', $organisation->id)
                                ->where('species_id', $request->species_id);
                }),
            ],
            'species_id' => 'required|exists:species,id',
            'allocated' => 'required|integer|min:0',
            'utilised' => 'required|integer|min:0',
        ], [
            'period.unique' => 'A record for this species in this year already exists for this organisation.'
        ]);

        $validated['organisation_id'] = $organisation->id;

        HuntingRecord::create($validated);

        return redirect()->route('hunting_records.index', $organisation->slug)
            ->with('success', 'Hunting record has been added successfully.');
    }

    public function edit(Organisation $organisation, HuntingRecord $record)
    {
        $huntingRecord = $record;
        $species = Species::orderBy('name')->get();
        return view('organisation.historical-data.hunting-records.edit', compact('organisation', 'huntingRecord', 'species'));
    }

    public function update(Request $request, Organisation $organisation, HuntingRecord $record)
    {
        $validated = $request->validate([
            'period' => [
                'required',
                'integer',
                'min:1900',
                'max:' . date('Y'),
                Rule::unique('hunting_records')->where(function ($query) use ($request, $organisation) {
                    return $query->where('organisation_id', $organisation->id)
                                ->where('species_id', $request->species_id);
                })->ignore($record->id),
            ],
            'species_id' => 'required|exists:species,id',
            'allocated' => 'required|integer|min:0',
            'utilised' => 'required|integer|min:0',
        ], [
            'period.unique' => 'A record for this species in this year already exists for this organisation.'
        ]);

        $record->update($validated);

        return redirect()
            ->route('hunting_records.index', $organisation->slug)
            ->with('success', 'Hunting record has been updated successfully.');
    }

    public function destroy(Organisation $organisation, HuntingRecord $record)
    {
        $record->delete();

        return redirect()
            ->route('hunting_records.index', $organisation->slug)
            ->with('success', 'Hunting record has been deleted successfully.');
    }
}
