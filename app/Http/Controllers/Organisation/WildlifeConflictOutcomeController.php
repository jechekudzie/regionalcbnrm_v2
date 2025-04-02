<?php

namespace App\Http\Controllers\Organisation;

use App\Http\Controllers\Controller;
use App\Models\Admin\ConflictOutCome;
use App\Models\Admin\Organisation;
use App\Models\DynamicField;
use App\Models\Organisation\WildlifeConflictIncident;
use App\Models\Organisation\WildlifeConflictOutcome;
use App\Models\Organisation\WildlifeConflictDynamicValue;
use Illuminate\Http\Request;

class WildlifeConflictOutcomeController extends Controller
{
    /**
     * Show the form for creating a new outcome.
     */
    public function create(Organisation $organisation, WildlifeConflictIncident $wildlifeConflictIncident)
    {
        // Get all available conflict outcomes
        $conflictOutcomes = ConflictOutCome::all();
        
        // Get existing outcomes for this incident to exclude them
        $existingOutcomeIds = $wildlifeConflictIncident->outcomes->pluck('conflict_out_come_id')->toArray();
        
        return view('organisation.wildlife-conflicts.outcomes.create', [
            'organisation' => $organisation,
            'wildlifeConflictIncident' => $wildlifeConflictIncident,
            'conflictOutcomes' => $conflictOutcomes,
            'existingOutcomeIds' => $existingOutcomeIds
        ]);
    }

    /**
     * Store a newly created outcome in storage.
     */
    public function store(Request $request, Organisation $organisation, WildlifeConflictIncident $wildlifeConflictIncident)
    {
        $validated = $request->validate([
            'conflict_out_come_id' => 'required|exists:conflict_out_comes,id',
        ]);

        // Create the outcome
        $outcome = WildlifeConflictOutcome::create([
            'wildlife_conflict_incident_id' => $wildlifeConflictIncident->id,
            'conflict_out_come_id' => $validated['conflict_out_come_id'],
        ]);

        // Get dynamic fields for this outcome type
        $dynamicFields = DynamicField::where('conflict_outcome_id', $validated['conflict_out_come_id'])->get();
        
        // Process dynamic field values if any
        foreach ($dynamicFields as $field) {
            $fieldName = 'dynamic_field_' . $field->id;
            if ($request->has($fieldName)) {
                WildlifeConflictDynamicValue::create([
                    'wildlife_conflict_outcome_id' => $outcome->id,
                    'dynamic_field_id' => $field->id,
                    'field_value' => $request->input($fieldName),
                ]);
            }
        }

        return redirect()->route('organisation.wildlife-conflicts.show', [
            'organisation' => $organisation->slug,
            'wildlifeConflictIncident' => $wildlifeConflictIncident->id
        ])->with('success', 'Conflict outcome added successfully.');
    }

    /**
     * Display the specified outcome.
     */
    public function show(Organisation $organisation, WildlifeConflictIncident $wildlifeConflictIncident, WildlifeConflictOutcome $outcome)
    {
        // Load the outcome with its dynamic values and their associated fields
        $outcome->load(['conflictOutCome', 'dynamicValues.dynamicField']);
        
        return view('organisation.wildlife-conflicts.outcomes.show', [
            'organisation' => $organisation,
            'wildlifeConflictIncident' => $wildlifeConflictIncident,
            'outcome' => $outcome
        ]);
    }

    /**
     * Show the form for editing the specified outcome.
     */
    public function edit(Organisation $organisation, WildlifeConflictIncident $wildlifeConflictIncident, WildlifeConflictOutcome $outcome)
    {
        // Get dynamic fields for this outcome type
        $dynamicFields = DynamicField::where('conflict_outcome_id', $outcome->conflict_out_come_id)->get();
        
        // Get existing values
        $dynamicValues = $outcome->dynamicValues->pluck('field_value', 'dynamic_field_id')->toArray();
        
        return view('organisation.wildlife-conflicts.outcomes.edit', [
            'organisation' => $organisation,
            'wildlifeConflictIncident' => $wildlifeConflictIncident,
            'outcome' => $outcome,
            'dynamicFields' => $dynamicFields,
            'dynamicValues' => $dynamicValues
        ]);
    }

    /**
     * Update the specified outcome in storage.
     */
    public function update(Request $request, Organisation $organisation, WildlifeConflictIncident $wildlifeConflictIncident, WildlifeConflictOutcome $outcome)
    {
        // Get dynamic fields for this outcome type
        $dynamicFields = DynamicField::where('conflict_outcome_id', $outcome->conflict_out_come_id)->get();
        
        // Process dynamic field values
        foreach ($dynamicFields as $field) {
            $fieldName = 'dynamic_field_' . $field->id;
            if ($request->has($fieldName)) {
                $value = $request->input($fieldName);
                
                // Update or create the dynamic value
                WildlifeConflictDynamicValue::updateOrCreate(
                    [
                        'wildlife_conflict_outcome_id' => $outcome->id,
                        'dynamic_field_id' => $field->id,
                    ],
                    [
                        'field_value' => $value,
                    ]
                );
            }
        }

        return redirect()->route('organisation.wildlife-conflicts.show', [
            'organisation' => $organisation->slug,
            'wildlifeConflictIncident' => $wildlifeConflictIncident->id
        ])->with('success', 'Conflict outcome updated successfully.');
    }

    /**
     * Remove the specified outcome from storage.
     */
    public function destroy(Organisation $organisation, WildlifeConflictIncident $wildlifeConflictIncident, WildlifeConflictOutcome $outcome)
    {
        // Delete associated dynamic values first (should be handled by cascade, but just to be safe)
        $outcome->dynamicValues()->delete();
        
        // Delete the outcome
        $outcome->delete();

        return redirect()->route('organisation.wildlife-conflicts.show', [
            'organisation' => $organisation->slug,
            'wildlifeConflictIncident' => $wildlifeConflictIncident->id
        ])->with('success', 'Conflict outcome removed successfully.');
    }
} 