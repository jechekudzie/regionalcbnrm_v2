<?php

namespace App\Http\Controllers\Organisation;

use App\Http\Controllers\Controller;
use App\Models\Admin\ConflictType;
use App\Models\Admin\Organisation;
use App\Models\Organisation\Species;
use App\Models\Organisation\WildlifeConflictIncident;
use Illuminate\Http\Request;

class WildlifeConflictIncidentController extends Controller
{
    /**
     * Display a listing of the incidents.
     */
    public function index(Organisation $organisation)
    {
        
        $wildlifeConflictIncidents = WildlifeConflictIncident::where('organisation_id', $organisation->id)
            ->with(['species', 'conflictType'])
            ->orderBy('incident_date', 'desc')
            ->paginate(10);

        

        return view('organisation.wildlife-conflicts.index', compact('organisation', 'wildlifeConflictIncidents'));
    }

    /**
     * Show the form for creating a new incident.
     */
    public function create(Organisation $organisation)
    {
        $conflictTypes = ConflictType::all();
        $species = Species::orderBy('name')->get();
        
        return view('organisation.wildlife-conflicts.create', compact('organisation', 'conflictTypes', 'species'));
    }

    /**
     * Store a newly created incident in storage.
     */
    public function store(Request $request, Organisation $organisation)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'period' => 'required|integer|min:1900|max:' . date('Y'),
            'incident_date' => 'required|date_format:Y-m-d',
            'incident_time' => 'required|date_format:H:i',
            'longitude' => 'nullable|numeric|between:-180,180',
            'latitude' => 'nullable|numeric|between:-90,90',
            'location_description' => 'required|string|max:255',
            'description' => 'required|string',
            'conflict_type_id' => 'required|exists:conflict_types,id',
            'species' => 'required|array|min:1',
            'species.*' => 'exists:species,id'
        ]);

     

        $wildlifeConflictIncident = WildlifeConflictIncident::create([
            'organisation_id' => $organisation->id,
            'title' => $validated['title'],
            'period' => $validated['period'],
            'incident_date' => \Carbon\Carbon::parse($validated['incident_date'])->format('Y-m-d'),
            'incident_time' => $validated['incident_time'],
            'longitude' => $validated['longitude'],
            'latitude' => $validated['latitude'],
            'location_description' => $validated['location_description'],
            'description' => $validated['description'],
            'conflict_type_id' => $validated['conflict_type_id']
        ]);

        $wildlifeConflictIncident->species()->attach($validated['species']);

        return redirect()
            ->route('organisation.wildlife-conflicts.index', $organisation->slug)
            ->with('success', 'Wildlife conflict incident has been recorded successfully.');
    }

    /**
     * Display the specified incident.
     */
    public function show(Organisation $organisation, WildlifeConflictIncident $wildlifeConflictIncident)
    {
        $wildlifeConflictIncident->load(['species', 'conflictType']);
        return view('organisation.wildlife-conflicts.show', [
            'organisation' => $organisation,
            'wildlifeConflictIncident' => $wildlifeConflictIncident
        ]);
    }

    /**
     * Show the form for editing the specified incident.
     */
    public function edit(Organisation $organisation, WildlifeConflictIncident $wildlifeConflictIncident)
    {
        $conflictTypes = ConflictType::all();
        $species = Species::orderBy('name')->get();
        $wildlifeConflictIncident->load('species');
        
        return view('organisation.wildlife-conflicts.edit', compact('organisation', 'wildlifeConflictIncident', 'conflictTypes', 'species'));
    }

    /**
     * Update the specified incident in storage.
     */
    public function update(Request $request, Organisation $organisation, WildlifeConflictIncident $wildlifeConflictIncident)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'period' => 'required|integer|min:1900|max:' . date('Y'),
            'incident_date' => 'required|date_format:Y-m-d H:i',
            'incident_time' => 'required|date_format:H:i',
            'longitude' => 'nullable|numeric|between:-180,180',
            'latitude' => 'nullable|numeric|between:-90,90',
            'location_description' => 'required|string|max:255',
            'description' => 'required|string',
            'conflict_type_id' => 'required|exists:conflict_types,id',
            'species' => 'required|array|min:1',
            'species.*' => 'exists:species,id'
        ]);

        $wildlifeConflictIncident->update([
            'title' => $validated['title'],
            'period' => $validated['period'],
            'incident_date' => \Carbon\Carbon::parse($validated['incident_date'])->format('Y-m-d'),
            'incident_time' => $validated['incident_time'],
            'longitude' => $validated['longitude'],
            'latitude' => $validated['latitude'],
            'location_description' => $validated['location_description'],
            'description' => $validated['description'],
            'conflict_type_id' => $validated['conflict_type_id']
        ]);

        $wildlifeConflictIncident->species()->sync($validated['species']);

        return redirect()
            ->route('organisation.wildlife-conflicts.index', $organisation->slug)
            ->with('success', 'Wildlife conflict incident has been updated successfully.');
    }

    /**
     * Remove the specified incident from storage.
     */
    public function destroy(Organisation $organisation, WildlifeConflictIncident $wildlifeConflictIncident)
    {
        $wildlifeConflictIncident->delete();

        return redirect()
            ->route('organisation.wildlife-conflicts.index', $organisation->slug)
            ->with('success', 'Wildlife conflict incident has been deleted successfully.');
    }
} 