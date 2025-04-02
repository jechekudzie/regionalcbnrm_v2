<?php

namespace App\Http\Controllers\Organisation;

use App\Http\Controllers\Controller;
use App\Models\Admin\ControlMeasure;
use App\Models\Admin\Organisation;
use App\Models\Organisation\ProblemAnimalControl;
use App\Models\Organisation\WildlifeConflictIncident;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProblemAnimalControlController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Organisation $organisation)
    {
        $problemAnimalControls = ProblemAnimalControl::where('organisation_id', $organisation->id)
            ->with(['wildlifeConflictIncident', 'controlMeasures'])
            ->orderBy('control_date', 'desc')
            ->paginate(10);

        return view('organisation.problem-animal-controls.index', compact('organisation', 'problemAnimalControls'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Organisation $organisation, Request $request)
    {
        $wildlifeConflictIncidentId = $request->query('wildlife_conflict_incident_id');
        $wildlifeConflictIncident = null;
        
        if ($wildlifeConflictIncidentId) {
            $wildlifeConflictIncident = WildlifeConflictIncident::where('id', $wildlifeConflictIncidentId)
                ->where('organisation_id', $organisation->id)
                ->with('species')
                ->first();
        }

        $wildlifeConflictIncidents = WildlifeConflictIncident::where('organisation_id', $organisation->id)
            ->with('species')
            ->orderBy('incident_date', 'desc')
            ->get();

        $controlMeasures = ControlMeasure::orderBy('name')->get();
        
        return view('organisation.problem-animal-controls.create', compact(
            'organisation', 
            'wildlifeConflictIncidents', 
            'wildlifeConflictIncident',
            'controlMeasures'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Organisation $organisation)
    {
        $validated = $request->validate([
            'wildlife_conflict_incident_id' => 'required|exists:wildlife_conflict_incidents,id',
            'control_date' => 'required|date_format:Y-m-d',
            'control_time' => 'nullable|date_format:H:i',
            'period' => 'required|integer|min:1900|max:' . date('Y'),
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'estimated_number' => 'required|integer|min:1',
            'control_measures' => 'required|array|min:1',
            'control_measures.*' => 'exists:control_measures,id',
        ]);

        // Verify the wildlife conflict incident belongs to this organisation
        $wildlifeConflictIncident = WildlifeConflictIncident::where('id', $validated['wildlife_conflict_incident_id'])
            ->where('organisation_id', $organisation->id)
            ->firstOrFail();

        $problemAnimalControl = ProblemAnimalControl::create([
            'organisation_id' => $organisation->id,
            'wildlife_conflict_incident_id' => $validated['wildlife_conflict_incident_id'],
            'control_date' => Carbon::parse($validated['control_date'])->format('Y-m-d'),
            'control_time' => $validated['control_time'] ?? null,
            'period' => $validated['period'],
            'location' => $validated['location'],
            'description' => $validated['description'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'estimated_number' => $validated['estimated_number'],
        ]);

        $problemAnimalControl->controlMeasures()->attach($validated['control_measures']);

        return redirect()
            ->route('organisation.problem-animal-controls.show', [$organisation->slug, $problemAnimalControl])
            ->with('success', 'Problem animal control record has been created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Organisation $organisation, ProblemAnimalControl $problemAnimalControl)
    {
        $problemAnimalControl->load([
            'wildlifeConflictIncident.species',
            'controlMeasures',
        ]);
        
        return view('organisation.problem-animal-controls.show', compact('organisation', 'problemAnimalControl'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Organisation $organisation, ProblemAnimalControl $problemAnimalControl)
    {
        $problemAnimalControl->load(['wildlifeConflictIncident.species', 'controlMeasures']);
        
        $wildlifeConflictIncidents = WildlifeConflictIncident::where('organisation_id', $organisation->id)
            ->with('species')
            ->orderBy('incident_date', 'desc')
            ->get();

        $controlMeasures = ControlMeasure::orderBy('name')->get();
        
        return view('organisation.problem-animal-controls.edit', compact(
            'organisation', 
            'problemAnimalControl',
            'wildlifeConflictIncidents',
            'controlMeasures'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Organisation $organisation, ProblemAnimalControl $problemAnimalControl)
    {
        $validated = $request->validate([
            'wildlife_conflict_incident_id' => 'required|exists:wildlife_conflict_incidents,id',
            'control_date' => 'required|date_format:Y-m-d',
            'control_time' => 'nullable|date_format:H:i',
            'period' => 'required|integer|min:1900|max:' . date('Y'),
            'location' => 'required|string|max:255',
            'description' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'estimated_number' => 'required|integer|min:1',
            'control_measures' => 'required|array|min:1',
            'control_measures.*' => 'exists:control_measures,id',
        ]);

        // Verify the wildlife conflict incident belongs to this organisation
        $wildlifeConflictIncident = WildlifeConflictIncident::where('id', $validated['wildlife_conflict_incident_id'])
            ->where('organisation_id', $organisation->id)
            ->firstOrFail();

        $problemAnimalControl->update([
            'wildlife_conflict_incident_id' => $validated['wildlife_conflict_incident_id'],
            'control_date' => Carbon::parse($validated['control_date'])->format('Y-m-d'),
            'control_time' => $validated['control_time'] ?? null,
            'period' => $validated['period'],
            'location' => $validated['location'],
            'description' => $validated['description'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'estimated_number' => $validated['estimated_number'],
        ]);

        $problemAnimalControl->controlMeasures()->sync($validated['control_measures']);

        return redirect()
            ->route('organisation.problem-animal-controls.show', [$organisation->slug, $problemAnimalControl])
            ->with('success', 'Problem animal control record has been updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organisation $organisation, ProblemAnimalControl $problemAnimalControl)
    {
        $problemAnimalControl->delete();

        return redirect()
            ->route('organisation.problem-animal-controls.index', $organisation->slug)
            ->with('success', 'Problem animal control record has been deleted successfully.');
    }
}
