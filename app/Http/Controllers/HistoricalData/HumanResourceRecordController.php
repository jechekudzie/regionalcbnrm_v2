<?php

namespace App\Http\Controllers\HistoricalData;

use App\Http\Controllers\Controller;
use App\Models\HistoricalData\HumanResourceRecord;
use App\Models\Organisation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HumanResourceRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Organisation $organisation)
    {
        $humanResourceRecords = HumanResourceRecord::where('organisation_id', $organisation->id)
            ->orderBy('period', 'desc')
            ->get();

        return view('organisation.historical-data.human-resource-records.index', [
            'organisation' => $organisation,
            'humanResourceRecords' => $humanResourceRecords,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Organisation $organisation)
    {
        return view('organisation.historical-data.human-resource-records.create', [
            'organisation' => $organisation,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Organisation $organisation)
    {
        $request->validate([
            'period' => 'required|integer',
            'employed_by' => 'required|in:community,organisation',
            'wildlife_managers' => 'required|integer|min:0',
            'game_scouts' => 'required|integer|min:0',
            'rangers' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $humanResourceRecord = new HumanResourceRecord();
        $humanResourceRecord->organisation_id = $organisation->id;
        $humanResourceRecord->period = $request->period;
        $humanResourceRecord->employed_by = $request->employed_by;
        $humanResourceRecord->wildlife_managers = $request->wildlife_managers;
        $humanResourceRecord->game_scouts = $request->game_scouts;
        $humanResourceRecord->rangers = $request->rangers;
        $humanResourceRecord->notes = $request->notes;
        $humanResourceRecord->save();

        return redirect()->route('organisations.historical-data.human-resource-records.index', $organisation)->with('success', 'Human Resource Record created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Organisation $organisation, HumanResourceRecord $humanResourceRecord)
    {
        return view('organisation.historical-data.human-resource-records.show', compact('organisation', 'humanResourceRecord'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Organisation $organisation, HumanResourceRecord $humanResourceRecord)
    {
        return view('organisation.historical-data.human-resource-records.edit', compact('organisation', 'humanResourceRecord'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Organisation $organisation, HumanResourceRecord $humanResourceRecord)
    {
        $request->validate([
            'period' => 'required|integer',
            'employed_by' => 'required|in:community,organisation',
            'wildlife_managers' => 'required|integer|min:0',
            'game_scouts' => 'required|integer|min:0',
            'rangers' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $humanResourceRecord->period = $request->period;
        $humanResourceRecord->employed_by = $request->employed_by;
        $humanResourceRecord->wildlife_managers = $request->wildlife_managers;
        $humanResourceRecord->game_scouts = $request->game_scouts;
        $humanResourceRecord->rangers = $request->rangers;
        $humanResourceRecord->notes = $request->notes;
        $humanResourceRecord->save();

        return redirect()->route('organisations.historical-data.human-resource-records.index', $organisation)->with('success', 'Human Resource Record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organisation $organisation, HumanResourceRecord $humanResourceRecord)
    {
        $humanResourceRecord->delete();

        return redirect()->route('organisations.historical-data.human-resource-records.index', $organisation)->with('success', 'Human Resource Record deleted successfully.');
    }
}
