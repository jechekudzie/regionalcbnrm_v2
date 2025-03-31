<?php

namespace App\Http\Controllers\Organisation;

use App\Http\Controllers\Controller;
use App\Models\Admin\Organisation;
use App\Models\Organisation\Species;
use App\Models\Organisation\HuntingActivity;
use App\Models\Organisation\HuntingConcession;
use App\Models\Organisation\QuotaAllocation;
use App\Models\Organisation\QuotaAllocationBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HuntingActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Organisation $organisation)
    {
        $activities = $organisation->huntingActivities()
            ->with(['huntingConcession', 'safariOperator', 'species'])
            ->latest()
            ->paginate(10);

        return view('organisation.hunting-activities.index', compact('organisation', 'activities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Organisation $organisation)
    {
        $huntingConcessions = $organisation->huntingConcessions;
        $species = Species::all();
        $safariOperators = $organisation->getSafariOperators();

        return view('organisation.hunting-activities.form', compact('organisation', 'huntingConcessions', 'species', 'safariOperators'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Organisation $organisation)
    {
        $request->validate([
            'hunting_concession_id' => 'required|exists:hunting_concessions,id',
            'safari_id' => 'required|exists:organisations,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'period' => 'required|integer',
            'professional_hunter_licenses' => 'required|array',
            'professional_hunter_licenses.*.license_number' => 'required|string',
            'professional_hunter_licenses.*.hunter_name' => 'required|string',
            'species' => 'required|array',
            'species.*.id' => 'required|exists:species,id',
            'species.*.off_take' => 'required|integer|min:0'
        ]);

        try {
            DB::beginTransaction();

            // First validate all species quotas before creating anything
            $speciesData = collect($request->species)->groupBy('id')->map(function($items) {
                return [
                    'id' => $items->first()['id'],
                    'off_take' => $items->sum('off_take')
                ];
            })->values();

            foreach ($speciesData as $species) {
                // Check quota allocation exists and has sufficient balance
                $quotaAllocation = QuotaAllocation::where('organisation_id', $organisation->id)
                    ->where('species_id', $species['id'])
                    ->where('period', $request->period)
                    ->first();

                if (!$quotaAllocation) {
                    throw new \Exception("No quota allocation found for species ID {$species['id']} in period {$request->period}");
                }

                // Get existing quota balance or calculate from quota allocation
                $quotaBalance = QuotaAllocationBalance::where('quota_allocation_id', $quotaAllocation->id)->first();
                
                if ($quotaBalance) {
                    $availableQuota = $quotaBalance->remaining_quota;
                } else {
                    $availableQuota = $quotaAllocation->hunting_quota;
                }

                if ($species['off_take'] > $availableQuota) {
                    $speciesName = Species::find($species['id'])->name;
                    throw new \Exception("Insufficient quota for {$speciesName}. Available: {$availableQuota}, Requested: {$species['off_take']}");
                }
            }

            // Create the hunting activity
            $huntingActivity = $organisation->huntingActivities()->create([
                'hunting_concession_id' => $request->hunting_concession_id,
                'safari_id' => $request->safari_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'period' => $request->period
            ]);

            // Add professional hunter licenses
            foreach ($request->professional_hunter_licenses as $license) {
                $huntingActivity->professionalHunterLicenses()->create([
                    'license_number' => $license['license_number'],
                    'hunter_name' => $license['hunter_name']
                ]);
            }

            // Process species and update quota balances
            foreach ($speciesData as $species) {
                $quotaAllocation = QuotaAllocation::where('organisation_id', $organisation->id)
                    ->where('species_id', $species['id'])
                    ->where('period', $request->period)
                    ->first();

                // Get or create quota balance for this quota allocation
                // Use firstOrCreate with only quota_allocation_id to ensure uniqueness
                $quotaBalance = QuotaAllocationBalance::where('quota_allocation_id', $quotaAllocation->id)->first();
                
                if (!$quotaBalance) {
                    // Only create a new balance record if one doesn't exist
                    $quotaBalance = QuotaAllocationBalance::create([
                        'quota_allocation_id' => $quotaAllocation->id,
                        'allocated_quota' => $quotaAllocation->hunting_quota,
                        'total_off_take' => 0,
                        'remaining_quota' => $quotaAllocation->hunting_quota
                    ]);
                }

                // Update the balance with new off-take value
                $newTotalOffTake = $quotaBalance->total_off_take + $species['off_take'];
                $newRemainingQuota = $quotaAllocation->hunting_quota - $newTotalOffTake;

                $quotaBalance->update([
                    'total_off_take' => $newTotalOffTake,
                    'remaining_quota' => $newRemainingQuota
                ]);

                // Attach species to hunting activity
                $huntingActivity->species()->attach($species['id'], [
                    'off_take' => $species['off_take']
                ]);
            }

            DB::commit();

            return redirect()
                ->route('organisation.hunting-activities.show', [$organisation->slug, $huntingActivity->id])
                ->with('success', 'Hunting activity created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to create hunting activity. ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Organisation $organisation, HuntingActivity $huntingActivity)
    {
        //
        return view('organisation.hunting-activities.show', compact('organisation', 'huntingActivity'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Organisation $organisation, HuntingActivity $huntingActivity)
    {
        $huntingConcessions = $organisation->huntingConcessions;
        $species = Species::all();
        $safariOperators = $organisation->getSafariOperators();
        $huntingActivity->load(['species', 'professionalHunterLicenses']);

        return view('organisation.hunting-activities.form', compact(
            'organisation', 
            'huntingActivity', 
            'huntingConcessions', 
            'species', 
            'safariOperators'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Organisation $organisation, HuntingActivity $huntingActivity)
    {
        $request->validate([
            'hunting_concession_id' => 'required|exists:hunting_concessions,id',
            'safari_id' => 'required|exists:organisations,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'period' => 'required|integer',
            'professional_hunter_licenses' => 'required|array',
            'professional_hunter_licenses.*.license_number' => 'required|string',
            'professional_hunter_licenses.*.hunter_name' => 'required|string',
            'species' => 'required|array',
            'species.*.id' => 'required|exists:species,id',
            'species.*.off_take' => 'required|integer|min:0'
        ]);

        try {
            DB::beginTransaction();

            // Get the old species data for updating quota balances
            $oldSpeciesData = $huntingActivity->species->mapWithKeys(function ($species) {
                return [$species->id => $species->pivot->off_take];
            })->toArray();

            // Update the hunting activity
            $huntingActivity->update([
                'hunting_concession_id' => $request->hunting_concession_id,
                'safari_id' => $request->safari_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'period' => $request->period
            ]);

            // Update professional hunter licenses
            $huntingActivity->professionalHunterLicenses()->delete();
            foreach ($request->professional_hunter_licenses as $license) {
                $huntingActivity->professionalHunterLicenses()->create([
                    'license_number' => $license['license_number'],
                    'hunter_name' => $license['hunter_name']
                ]);
            }

            // Group and process new species data
            $newSpeciesData = collect($request->species)->groupBy('id')->map(function($items) {
                return [
                    'id' => $items->first()['id'],
                    'off_take' => $items->sum('off_take')
                ];
            })->keyBy('id')->toArray();

            // Detach all current species
            $huntingActivity->species()->detach();

            // Process species and update quota balances
            foreach ($newSpeciesData as $speciesId => $speciesData) {
                $quotaAllocation = QuotaAllocation::where('organisation_id', $organisation->id)
                    ->where('species_id', $speciesId)
                    ->where('period', $request->period)
                    ->first();

                if (!$quotaAllocation) {
                    throw new \Exception("No quota allocation found for species ID {$speciesId} in period {$request->period}");
                }

                // Get the existing quota balance record
                $quotaBalance = QuotaAllocationBalance::where('quota_allocation_id', $quotaAllocation->id)->first();
                
                if (!$quotaBalance) {
                    // Create a new balance record if none exists
                    $quotaBalance = QuotaAllocationBalance::create([
                        'quota_allocation_id' => $quotaAllocation->id,
                        'allocated_quota' => $quotaAllocation->hunting_quota,
                        'total_off_take' => 0,
                        'remaining_quota' => $quotaAllocation->hunting_quota
                    ]);
                } else {
                    // Adjust for the old off-take if this species was previously attached
                    $oldOffTake = $oldSpeciesData[$speciesId] ?? 0;
                    $quotaBalance->total_off_take -= $oldOffTake; // Remove old off-take from total
                }

                // Update with new off-take
                $newTotalOffTake = $quotaBalance->total_off_take + $speciesData['off_take'];
                $newRemainingQuota = $quotaAllocation->hunting_quota - $newTotalOffTake;

                $quotaBalance->update([
                    'total_off_take' => $newTotalOffTake,
                    'remaining_quota' => $newRemainingQuota
                ]);

                // Attach species with new off-take
                $huntingActivity->species()->attach($speciesId, [
                    'off_take' => $speciesData['off_take']
                ]);
            }

            DB::commit();

            return redirect()
                ->route('organisation.hunting-activities.show', [$organisation->slug, $huntingActivity->id])
                ->with('success', 'Hunting activity updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to update hunting activity. ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organisation $organisation, HuntingActivity $huntingActivity)
    {
        //
        $huntingActivity->delete();
        return redirect()->route('organisation.hunting-activities.index')->with('success', 'Hunting Activity deleted successfully');
    }
}
