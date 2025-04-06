<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Admin\ConflictOutCome;
use App\Models\Admin\ConflictType;
use App\Models\Admin\Organisation;
use App\Models\Organisation\WildlifeConflictIncident;
use App\Models\Organisation\WildlifeConflictOutcome;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class WildlifeConflictController extends Controller
{
    /**
     * Get all wildlife conflict incidents for an organisation
     *
     * @param Request $request
     * @param Organisation $organisation
     * @return \Illuminate\Http\JsonResponse
     */
    public function getIncidents(Request $request, $organisation)
    {
        // Check if user has access to this organisation
        $user = $request->user();
        $hasAccess = $user->organisations()->where('organisations.id', $organisation)->exists();

        if (!$hasAccess) {
            return response()->json([
                'status' => 'error',
                'message' => 'You do not have access to this organisation'
            ], 403);
        }

        $incidents = WildlifeConflictIncident::with(['conflictType', 'species'])
            ->where('organisation_id', $organisation)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $incidents
        ], 200);
    }

    /**
     * Get a specific wildlife conflict incident
     *
     * @param Request $request
     * @param WildlifeConflictIncident $incident
     * @return \Illuminate\Http\JsonResponse
     */
    public function getIncident(Request $request, WildlifeConflictIncident $incident)
    {
        // Check if user has access to this incident's organisation
        $user = $request->user();
        $hasAccess = $user->organisations()->where('organisations.id', $incident->organisation_id)->exists();

        if (!$hasAccess) {
            return response()->json([
                'status' => 'error',
                'message' => 'You do not have access to this incident'
            ], 403);
        }

        $incident->load(['conflictType', 'species', 'outcomes']);

        return response()->json([
            'status' => 'success',
            'data' => [
                'incident' => $incident
            ]
        ], 200);
    }

    /**
     * Create a new wildlife conflict incident
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createIncident(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'organisation_id' => 'required|exists:organisations,id',
            'title' => 'required|string|max:255',
            'period' => 'required|integer',
            'incident_date' => 'required|date',
            'incident_time' => 'required',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'location_description' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'conflict_type_id' => 'required|exists:conflict_types,id',
            'species_id' => 'required|exists:species,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if user has access to this organisation
        $user = $request->user();
        $hasAccess = $user->organisations()->where('organisations.id', $request->organisation_id)->exists();

        if (!$hasAccess) {
            return response()->json([
                'status' => 'error',
                'message' => 'You do not have access to this organisation'
            ], 403);
        }

        try {
            DB::beginTransaction();

            $incident = WildlifeConflictIncident::create([
                'organisation_id' => $request->organisation_id,
                'title' => $request->title,
                'period' => $request->period,
                'incident_date' => $request->incident_date,
                'incident_time' => $request->incident_time,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'location_description' => $request->location_description,
                'description' => $request->description,
                'conflict_type_id' => $request->conflict_type_id,
            ]);

            // If dynamic values are provided, store them
            if ($request->has('dynamic_values') && is_array($request->dynamic_values)) {
                foreach ($request->dynamic_values as $value) {
                    $incident->dynamicValues()->create($value);
                }
            }

            // Sync species IDs to pivot table if provided
            if ($request->has('species_ids') && is_array($request->species_ids)) {
                $incident->species()->sync($request->species_ids);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Incident created successfully',
                'data' => [
                    'incident' => $incident->load(['conflictType', 'species'])
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create incident',
                'debug' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a wildlife conflict incident
     *
     * @param Request $request
     * @param WildlifeConflictIncident $incident
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateIncident(Request $request, WildlifeConflictIncident $incident)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'string|max:255',
            'date' => 'date',
            'time' => 'string',
            'latitude' => 'numeric',
            'longitude' => 'numeric',
            'description' => 'string',
            'conflict_type_id' => 'exists:conflict_types,id',
            'species_id' => 'exists:species,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if user has access to this incident's organisation
        $user = $request->user();
        $hasAccess = $user->organisations()->where('organisations.id', $incident->organisation_id)->exists();

        if (!$hasAccess) {
            return response()->json([
                'status' => 'error',
                'message' => 'You do not have access to this incident'
            ], 403);
        }

        try {
            DB::beginTransaction();

            $incident->update($request->all());

            // If dynamic values are provided, update them
            if ($request->has('dynamic_values')) {
                // Clear existing values and add new ones
                $incident->dynamicValues()->delete();
                foreach ($request->dynamic_values as $value) {
                    $incident->dynamicValues()->create($value);
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Incident updated successfully',
                'data' => [
                    'incident' => $incident->load(['conflictType', 'species'])
                ]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update incident',
                'debug' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get conflict types
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getConflictTypes()
    {
        $conflictTypes = ConflictType::all();

        return response()->json([
            'status' => 'success',
            'data' => [
                'conflict_types' => $conflictTypes
            ]
        ], 200);
    }

    /**
     * Get conflict outcomes
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getConflictOutcomes()
    {
        $conflictOutcomes = ConflictOutCome::with('dynamicFields')->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'conflict_outcomes' => $conflictOutcomes
            ]
        ], 200);
    }

    /**
     * Add an outcome to a wildlife conflict incident
     *
     * @param Request $request
     * @param WildlifeConflictIncident $incident
     * @return \Illuminate\Http\JsonResponse
     */
    public function addOutcome(Request $request, WildlifeConflictIncident $incident)
    {
        $validator = Validator::make($request->all(), [
            'conflict_outcome_id' => 'required|exists:conflict_out_comes,id',
            'notes' => 'nullable|string',
            'date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if user has access to this incident's organisation
        $user = $request->user();
        $hasAccess = $user->organisations()->where('organisations.id', $incident->organisation_id)->exists();

        if (!$hasAccess) {
            return response()->json([
                'status' => 'error',
                'message' => 'You do not have access to this incident'
            ], 403);
        }

        try {
            DB::beginTransaction();

            $outcome = WildlifeConflictOutcome::create([
                'wildlife_conflict_incident_id' => $incident->id,
                'conflict_outcome_id' => $request->conflict_outcome_id,
                'notes' => $request->notes,
                'date' => $request->date,
            ]);

            // If dynamic values are provided, store them
            if ($request->has('dynamic_values')) {
                foreach ($request->dynamic_values as $value) {
                    $outcome->dynamicValues()->create($value);
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Outcome added successfully',
                'data' => [
                    'outcome' => $outcome->load('conflictOutcome')
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to add outcome',
                'debug' => $e->getMessage()
            ], 500);
        }
    }
}
