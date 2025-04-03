<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Admin\ControlMeasure;
use App\Models\Admin\Organisation;
use App\Models\Organisation\ProblemAnimalControl;
use App\Models\Organisation\WildlifeConflictIncident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProblemAnimalControlController extends Controller
{
    /**
     * Get all problem animal control records for an organisation
     *
     * @param Request $request
     * @param Organisation $organisation
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRecords(Request $request, Organisation $organisation)
    {
        // Check if user has access to this organisation
        $user = $request->user();
        $hasAccess = $user->organisations()->where('organisations.id', $organisation->id)->exists();

        if (!$hasAccess) {
            return response()->json([
                'status' => 'error',
                'message' => 'You do not have access to this organisation'
            ], 403);
        }

        $records = ProblemAnimalControl::with(['wildlifeConflictIncident', 'controlMeasure'])
            ->whereHas('wildlifeConflictIncident', function ($query) use ($organisation) {
                $query->where('organisation_id', $organisation->id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $records
        ], 200);
    }

    /**
     * Get a specific problem animal control record
     *
     * @param Request $request
     * @param ProblemAnimalControl $record
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRecord(Request $request, ProblemAnimalControl $record)
    {
        // Check if user has access to this record's organisation
        $user = $request->user();
        $hasAccess = $user->organisations()
            ->whereHas('wildlifeConflictIncidents', function ($query) use ($record) {
                $query->where('id', $record->wildlife_conflict_incident_id);
            })
            ->exists();

        if (!$hasAccess) {
            return response()->json([
                'status' => 'error',
                'message' => 'You do not have access to this record'
            ], 403);
        }

        $record->load(['wildlifeConflictIncident', 'controlMeasure']);

        return response()->json([
            'status' => 'success',
            'data' => [
                'record' => $record
            ]
        ], 200);
    }

    /**
     * Create a new problem animal control record
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createRecord(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'wildlife_conflict_incident_id' => 'required|exists:wildlife_conflict_incidents,id',
            'control_measure_id' => 'required|exists:control_measures,id',
            'date' => 'required|date',
            'time' => 'required',
            'description' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'number_of_animals' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if user has access to the incident's organisation
        $incident = WildlifeConflictIncident::findOrFail($request->wildlife_conflict_incident_id);
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

            $record = ProblemAnimalControl::create($request->all());

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Problem animal control record created successfully',
                'data' => [
                    'record' => $record->load(['wildlifeConflictIncident', 'controlMeasure'])
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create problem animal control record',
                'debug' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a problem animal control record
     *
     * @param Request $request
     * @param ProblemAnimalControl $record
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateRecord(Request $request, ProblemAnimalControl $record)
    {
        $validator = Validator::make($request->all(), [
            'control_measure_id' => 'exists:control_measures,id',
            'date' => 'date',
            'time' => 'string',
            'description' => 'string',
            'latitude' => 'numeric',
            'longitude' => 'numeric',
            'number_of_animals' => 'integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if user has access to this record's incident organisation
        $incident = WildlifeConflictIncident::findOrFail($record->wildlife_conflict_incident_id);
        $user = $request->user();
        $hasAccess = $user->organisations()->where('organisations.id', $incident->organisation_id)->exists();

        if (!$hasAccess) {
            return response()->json([
                'status' => 'error',
                'message' => 'You do not have access to this record'
            ], 403);
        }

        try {
            DB::beginTransaction();

            $record->update($request->all());

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Problem animal control record updated successfully',
                'data' => [
                    'record' => $record->load(['wildlifeConflictIncident', 'controlMeasure'])
                ]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update problem animal control record',
                'debug' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get control measures
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getControlMeasures()
    {
        $controlMeasures = ControlMeasure::all();

        return response()->json([
            'status' => 'success',
            'data' => [
                'control_measures' => $controlMeasures
            ]
        ], 200);
    }
}