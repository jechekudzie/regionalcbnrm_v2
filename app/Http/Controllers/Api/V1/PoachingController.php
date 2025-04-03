<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Admin\Organisation;
use App\Models\Admin\PoachingMethod;
use App\Models\Admin\PoachingReason;
use App\Models\Admin\Species;
use App\Models\Organisation\Poacher;
use App\Models\Organisation\PoachingIncident;
use App\Models\Organisation\PoachingIncidentMethod;
use App\Models\Organisation\PoachingIncidentSpecies;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PoachingController extends Controller
{
    /**
     * Get all poaching incidents for an organisation
     *
     * @param Request $request
     * @param Organisation $organisation
     * @return \Illuminate\Http\JsonResponse
     */
    public function getIncidents(Request $request, Organisation $organisation)
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

        $incidents = PoachingIncident::with(['species', 'methods'])
            ->where('organisation_id', $organisation->id)
            ->orderBy('date', 'desc')
            ->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $incidents
        ], 200);
    }

    /**
     * Get a specific poaching incident
     *
     * @param Request $request
     * @param PoachingIncident $incident
     * @return \Illuminate\Http\JsonResponse
     */
    public function getIncident(Request $request, PoachingIncident $incident)
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

        $incident->load(['species', 'methods', 'poachers']);

        return response()->json([
            'status' => 'success',
            'data' => [
                'incident' => $incident
            ]
        ], 200);
    }

    /**
     * Create a new poaching incident
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createIncident(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'organisation_id' => 'required|exists:organisations,id',
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'time' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'description' => 'required|string',
            'species' => 'required|array|min:1',
            'species.*.species_id' => 'required|exists:species,id',
            'species.*.quantity' => 'required|integer|min:1',
            'methods' => 'required|array|min:1',
            'methods.*.method_id' => 'required|exists:poaching_methods,id',
            'docket_number' => 'nullable|string',
            'docket_status' => 'nullable|string',
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

            // Create the poaching incident
            $incident = PoachingIncident::create([
                'organisation_id' => $request->organisation_id,
                'title' => $request->title,
                'date' => $request->date,
                'time' => $request->time,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'description' => $request->description,
                'docket_number' => $request->docket_number,
                'docket_status' => $request->docket_status,
            ]);

            // Add species
            foreach ($request->species as $speciesData) {
                PoachingIncidentSpecies::create([
                    'poaching_incident_id' => $incident->id,
                    'species_id' => $speciesData['species_id'],
                    'quantity' => $speciesData['quantity'],
                ]);
            }

            // Add methods
            foreach ($request->methods as $methodData) {
                PoachingIncidentMethod::create([
                    'poaching_incident_id' => $incident->id,
                    'poaching_method_id' => $methodData['method_id'],
                ]);
            }

            // Add poachers if provided
            if ($request->has('poachers') && is_array($request->poachers)) {
                foreach ($request->poachers as $poacherData) {
                    $poacher = Poacher::create([
                        'poaching_incident_id' => $incident->id,
                        'name' => $poacherData['name'],
                        'id_number' => $poacherData['id_number'] ?? null,
                        'identification_type_id' => $poacherData['identification_type_id'] ?? null,
                        'gender' => $poacherData['gender'] ?? null,
                        'age' => $poacherData['age'] ?? null,
                        'nationality' => $poacherData['nationality'] ?? null,
                        'status' => $poacherData['status'] ?? 'apprehended',
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Poaching incident created successfully',
                'data' => [
                    'incident' => $incident->load(['species', 'methods', 'poachers'])
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create poaching incident',
                'debug' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a poaching incident
     *
     * @param Request $request
     * @param PoachingIncident $incident
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateIncident(Request $request, PoachingIncident $incident)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'string|max:255',
            'date' => 'date',
            'time' => 'string',
            'latitude' => 'numeric',
            'longitude' => 'numeric',
            'description' => 'string',
            'species' => 'array',
            'species.*.species_id' => 'exists:species,id',
            'species.*.quantity' => 'integer|min:1',
            'methods' => 'array',
            'methods.*.method_id' => 'exists:poaching_methods,id',
            'docket_number' => 'nullable|string',
            'docket_status' => 'nullable|string',
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

            // Update basic incident details
            $incident->update([
                'title' => $request->title ?? $incident->title,
                'date' => $request->date ?? $incident->date,
                'time' => $request->time ?? $incident->time,
                'latitude' => $request->latitude ?? $incident->latitude,
                'longitude' => $request->longitude ?? $incident->longitude,
                'description' => $request->description ?? $incident->description,
                'docket_number' => $request->docket_number ?? $incident->docket_number,
                'docket_status' => $request->docket_status ?? $incident->docket_status,
            ]);

            // Update species if provided
            if ($request->has('species') && is_array($request->species)) {
                // Delete existing species
                PoachingIncidentSpecies::where('poaching_incident_id', $incident->id)->delete();
                
                // Add new species
                foreach ($request->species as $speciesData) {
                    PoachingIncidentSpecies::create([
                        'poaching_incident_id' => $incident->id,
                        'species_id' => $speciesData['species_id'],
                        'quantity' => $speciesData['quantity'],
                    ]);
                }
            }

            // Update methods if provided
            if ($request->has('methods') && is_array($request->methods)) {
                // Delete existing methods
                PoachingIncidentMethod::where('poaching_incident_id', $incident->id)->delete();
                
                // Add new methods
                foreach ($request->methods as $methodData) {
                    PoachingIncidentMethod::create([
                        'poaching_incident_id' => $incident->id,
                        'poaching_method_id' => $methodData['method_id'],
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Poaching incident updated successfully',
                'data' => [
                    'incident' => $incident->load(['species', 'methods', 'poachers'])
                ]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update poaching incident',
                'debug' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get poaching methods
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPoachingMethods()
    {
        $methods = PoachingMethod::all();

        return response()->json([
            'status' => 'success',
            'data' => [
                'methods' => $methods
            ]
        ], 200);
    }

    /**
     * Get poaching reasons
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPoachingReasons()
    {
        $reasons = PoachingReason::all();

        return response()->json([
            'status' => 'success',
            'data' => [
                'reasons' => $reasons
            ]
        ], 200);
    }

    /**
     * Add a poacher to a poaching incident
     *
     * @param Request $request
     * @param PoachingIncident $incident
     * @return \Illuminate\Http\JsonResponse
     */
    public function addPoacher(Request $request, PoachingIncident $incident)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'id_number' => 'nullable|string|max:255',
            'identification_type_id' => 'nullable|exists:identification_types,id',
            'gender' => 'nullable|string|in:Male,Female',
            'age' => 'nullable|integer',
            'nationality' => 'nullable|string|max:255',
            'status' => 'nullable|string|in:apprehended,at large',
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
            $poacher = Poacher::create([
                'poaching_incident_id' => $incident->id,
                'name' => $request->name,
                'id_number' => $request->id_number,
                'identification_type_id' => $request->identification_type_id,
                'gender' => $request->gender,
                'age' => $request->age,
                'nationality' => $request->nationality,
                'status' => $request->status ?? 'apprehended',
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Poacher added successfully',
                'data' => [
                    'poacher' => $poacher
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to add poacher',
                'debug' => $e->getMessage()
            ], 500);
        }
    }
}