<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Admin\Organisation;
use App\Models\Admin\Species;
use App\Models\Organisation\HuntingActivity;
use App\Models\Organisation\HuntingActivitySpecies;
use App\Models\Organisation\HuntingConcession;
use App\Models\Organisation\QuotaAllocation;
use App\Models\Organisation\QuotaAllocationBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class HuntingController extends Controller
{
    /**
     * Get all hunting activities for an organisation
     *
     * @param Request $request
     * @param Organisation $organisation
     * @return \Illuminate\Http\JsonResponse
     */
    public function getActivities(Request $request, Organisation $organisation)
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

        $activities = HuntingActivity::with(['huntingConcession', 'species', 'professionalHunterLicenses'])
            ->where('organisation_id', $organisation->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $activities
        ], 200);
    }

    /**
     * Get a specific hunting activity
     *
     * @param Request $request
     * @param HuntingActivity $activity
     * @return \Illuminate\Http\JsonResponse
     */
    public function getActivity(Request $request, HuntingActivity $activity)
    {
        // Check if user has access to this activity's organisation
        $user = $request->user();
        $hasAccess = $user->organisations()->where('organisations.id', $activity->organisation_id)->exists();

        if (!$hasAccess) {
            return response()->json([
                'status' => 'error',
                'message' => 'You do not have access to this activity'
            ], 403);
        }

        $activity->load(['huntingConcession', 'species', 'professionalHunterLicenses']);

        return response()->json([
            'status' => 'success',
            'data' => [
                'activity' => $activity
            ]
        ], 200);
    }

    /**
     * Create a new hunting activity
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createActivity(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'organisation_id' => 'required|exists:organisations,id',
            'hunting_concession_id' => 'required|exists:hunting_concessions,id',
            'safari_operator_id' => 'required|exists:organisations,id',
            'period' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'client_name' => 'required|string|max:255',
            'client_nationality' => 'required|string|max:255',
            'client_country_of_residence' => 'required|string|max:255',
            'species' => 'required|array|min:1',
            'species.*.species_id' => 'required|exists:species,id',
            'species.*.quantity' => 'required|integer|min:1',
            'professional_hunters' => 'array',
            'professional_hunters.*.name' => 'required|string|max:255',
            'professional_hunters.*.license_number' => 'required|string|max:255',
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

            // Check quota availability for each species
            foreach ($request->species as $speciesData) {
                $species = Species::findOrFail($speciesData['species_id']);
                $quantity = $speciesData['quantity'];

                // Get the latest active quota allocation for this species
                $quotaAllocation = QuotaAllocation::where('organisation_id', $request->organisation_id)
                    ->where('species_id', $species->id)
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now())
                    ->first();

                if (!$quotaAllocation) {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'error',
                        'message' => "No active quota allocation found for {$species->name}"
                    ], 400);
                }

                // Get the quota balance
                $quotaBalance = QuotaAllocationBalance::where('quota_allocation_id', $quotaAllocation->id)->first();

                if (!$quotaBalance || $quotaBalance->remaining < $quantity) {
                    DB::rollBack();
                    $remainingQuota = $quotaBalance ? $quotaBalance->remaining : 0;
                    return response()->json([
                        'status' => 'error',
                        'message' => "Insufficient quota for {$species->name}. Available: {$remainingQuota}, Requested: {$quantity}"
                    ], 400);
                }
            }

            // Create the hunting activity
            $activity = HuntingActivity::create([
                'organisation_id' => $request->organisation_id,
                'hunting_concession_id' => $request->hunting_concession_id,
                'safari_operator_id' => $request->safari_operator_id,
                'period' => $request->period,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'client_name' => $request->client_name,
                'client_nationality' => $request->client_nationality,
                'client_country_of_residence' => $request->client_country_of_residence,
            ]);

            // Add species and update quota balances
            foreach ($request->species as $speciesData) {
                $speciesId = $speciesData['species_id'];
                $quantity = $speciesData['quantity'];

                // Get the latest active quota allocation for this species
                $quotaAllocation = QuotaAllocation::where('organisation_id', $request->organisation_id)
                    ->where('species_id', $speciesId)
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now())
                    ->first();

                // Get the quota balance
                $quotaBalance = QuotaAllocationBalance::where('quota_allocation_id', $quotaAllocation->id)->first();

                // Update quota balance
                $quotaBalance->update([
                    'off_take' => $quotaBalance->off_take + $quantity,
                    'remaining' => $quotaBalance->remaining - $quantity
                ]);

                // Add species to hunting activity
                HuntingActivitySpecies::create([
                    'hunting_activity_id' => $activity->id,
                    'species_id' => $speciesId,
                    'quantity' => $quantity,
                    'quota_allocation_balance_id' => $quotaBalance->id
                ]);
            }

            // Add professional hunters if provided
            if ($request->has('professional_hunters') && is_array($request->professional_hunters)) {
                foreach ($request->professional_hunters as $hunterData) {
                    $activity->professionalHunterLicenses()->create([
                        'name' => $hunterData['name'],
                        'license_number' => $hunterData['license_number']
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Hunting activity created successfully',
                'data' => [
                    'activity' => $activity->load(['huntingConcession', 'species', 'professionalHunterLicenses'])
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create hunting activity',
                'debug' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get hunting concessions for an organisation
     *
     * @param Request $request
     * @param Organisation $organisation
     * @return \Illuminate\Http\JsonResponse
     */
    public function getConcessions(Request $request, Organisation $organisation)
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

        $concessions = HuntingConcession::where('organisation_id', $organisation->id)
            ->orderBy('name')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'concessions' => $concessions
            ]
        ], 200);
    }

    /**
     * Get quota allocations for an organisation
     *
     * @param Request $request
     * @param Organisation $organisation
     * @return \Illuminate\Http\JsonResponse
     */
    public function getQuotaAllocations(Request $request, Organisation $organisation)
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

        // Get active quota allocations with their balances
        $quotaAllocations = QuotaAllocation::with(['species', 'quotaAllocationBalance'])
            ->where('organisation_id', $organisation->id)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'quota_allocations' => $quotaAllocations
            ]
        ], 200);
    }
}