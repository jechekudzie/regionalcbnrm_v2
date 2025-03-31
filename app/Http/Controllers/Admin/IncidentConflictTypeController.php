<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\Organisation;
use App\Models\ConflictType;
use Illuminate\Http\Request;

class IncidentConflictTypeController extends Controller
{
    //

    public function index(Organisation $organisation, Incident $incident)
    {
        $incidentConflictTypes = $incident->conflictTypes()->get();
        $conflictTypes = ConflictType::all();
        return view('organisation.incidents.add_incident_conflicts', compact('conflictTypes','incidentConflictTypes', 'incident', 'organisation'));
    }

    public function store(Request $request, Organisation $organisation, Incident $incident)
    {

        // Validate the request
        $validated = $request->validate([
            'conflict_types' => 'required|array',
            'conflict_types.*' => 'exists:conflict_types,id', // Ensure each conflict_types ID exists in the database
        ]);

        $incident->conflictTypes()->sync($validated['conflict_types']); // Where $validated['conflict_types'] is an array of conflict_types IDs.

        // Redirect back with a success message
        return redirect()->route('organisation.incident-conflict-types.index', [$organisation->slug,$incident->slug])->with('success', 'Conflict Type added to incident successfully.');
    }

    public function destroy(Organisation $organisation, Incident $incident, $incidentConflictType)
    {
        $incident->conflictTypes()->detach($incidentConflictType);
        return redirect()->route('organisation.incident-conflict-types.index', [$organisation->slug,$incident->slug])->with('success', 'Conflict Type removed from incident successfully.');
    }
}
