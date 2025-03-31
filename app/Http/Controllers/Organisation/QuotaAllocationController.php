<?php

namespace App\Http\Controllers\Organisation;

use App\Http\Controllers\Controller;
use App\Models\Organisation\QuotaAllocation;
use App\Models\Admin\Organisation;
use App\Models\Organisation\Species;
use App\Models\Organisation\QuotaAllocationBalance;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class QuotaAllocationController extends Controller
{
    public function index(Organisation $organisation)
    {
        $quotaAllocations = QuotaAllocation::where('organisation_id', $organisation->id)
            ->orderBy('start_date', 'desc')
            ->get();
            
        return view('organisation.quota-allocations.index', compact('organisation', 'quotaAllocations'));
    }

    public function create(Organisation $organisation)
    {
        $species = Species::orderBy('name')->get();
        return view('organisation.quota-allocations.create', compact('organisation', 'species'));
    }

    public function store(Request $request, Organisation $organisation)
    {
        $validated = $request->validate([
            'species_id' => [
                'required',
                'exists:species,id',
                Rule::unique('quota_allocations')->where(function ($query) use ($request, $organisation) {
                    return $query->where('organisation_id', $organisation->id)
                                ->where('species_id', $request->species_id)
                                ->where('period', date('Y', strtotime($request->start_date)))
                                ->whereNull('deleted_at');
                }),
            ],
            'hunting_quota' => 'required|integer|min:0',
            'rational_killing_quota' => 'required|integer|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
        ], [
            'species_id.unique' => 'A quota allocation already exists for this species for the selected year.'
        ]);

        $validated['organisation_id'] = $organisation->id;
        $validated['period'] = date('Y', strtotime($request->start_date));

        $quotaAllocation = QuotaAllocation::create($validated);

        //create a quota allocation balance
        QuotaAllocationBalance::create([
            'quota_allocation_id' => $quotaAllocation->id,
            'remaining_quota' => $validated['hunting_quota']
        ]);

        return redirect()
            ->route('organisation.quota-allocations.index', $organisation->slug)
            ->with('success', 'Quota allocation has been created successfully.');
    }

    public function edit(Organisation $organisation, QuotaAllocation $quotaAllocation)
    {
        $species = Species::orderBy('name')->get();
        return view('organisation.quota-allocations.edit', compact('organisation', 'quotaAllocation', 'species'));
    }

    public function update(Request $request, Organisation $organisation, QuotaAllocation $quotaAllocation)
    {
        $validated = $request->validate([
            'species_id' => [
                'required',
                'exists:species,id',
                Rule::unique('quota_allocations')->where(function ($query) use ($request, $organisation) {
                    return $query->where('organisation_id', $organisation->id)
                                ->where('species_id', $request->species_id)
                                ->where('period', date('Y', strtotime($request->start_date)))
                                ->whereNull('deleted_at');
                })->ignore($quotaAllocation->id),
            ],
            'hunting_quota' => 'required|integer|min:0',
            'rational_killing_quota' => 'required|integer|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
        ], [
            'species_id.unique' => 'A quota allocation already exists for this species for the selected year.'
        ]);

        $validated['period'] = date('Y', strtotime($request->start_date));
        
        $quotaAllocation->update($validated);

        return redirect()
            ->route('organisation.quota-allocations.index', $organisation->slug)
            ->with('success', 'Quota allocation has been updated successfully.');
    }

    public function destroy(Organisation $organisation, QuotaAllocation $quotaAllocation)
    {
        $quotaAllocation->delete();

        return redirect()
            ->route('organisation.quota-allocations.index', $organisation->slug)
            ->with('success', 'Quota allocation has been deleted successfully.');
    }

    /**
     * Get quota allocation and balance for a species and period.
     * 
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Admin\Organisation $organisation
     * @return \Illuminate\Http\JsonResponse
     */
    public function getQuotaAllocation(Request $request, Organisation $organisation)
    {
        $request->validate([
            'species_id' => 'required|exists:species,id',
            'period' => 'required|integer',
        ]);

        $speciesId = $request->species_id;
        $period = $request->period;

        Log::info("Fetching quota allocation for org:{$organisation->id}, species:{$speciesId}, period:{$period}");

        $quotaAllocation = QuotaAllocation::where('organisation_id', $organisation->id)
            ->where('species_id', $speciesId)
            ->where('period', $period)
            ->first();

        if (!$quotaAllocation) {
            Log::info("No quota allocation found");
            return response()->json([
                'quota_allocation' => null,
                'quota_balance' => null,
                'debug' => [
                    'organisation_id' => $organisation->id,
                    'species_id' => $speciesId,
                    'period' => $period,
                ]
            ]);
        }

        Log::info("Found quota allocation: {$quotaAllocation->id}");

        $quotaBalance = QuotaAllocationBalance::where('quota_allocation_id', $quotaAllocation->id)
            ->first();

        if (!$quotaBalance) {
            Log::info("No quota balance found, creating default");
            $quotaBalance = [
                'allocated_quota' => $quotaAllocation->hunting_quota,
                'total_off_take' => 0,
                'remaining_quota' => $quotaAllocation->hunting_quota
            ];
        } else {
            Log::info("Found quota balance: {$quotaBalance->id}");
        }

        return response()->json([
            'quota_allocation' => $quotaAllocation,
            'quota_balance' => $quotaBalance->remaining_quota,
            'debug' => [
                'organisation_id' => $organisation->id,
                'species_id' => $speciesId,
                'period' => $period,
            ]
        ]);
    }
}
