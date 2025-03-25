<?php

namespace App\Http\Controllers\HistoricalData;

use App\Http\Controllers\Controller;
use App\Models\HistoricalData\IncomeUseRecord;
use App\Models\Organisation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IncomeUseRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Models\Organisation  $organisation
     * @return \Illuminate\Http\Response
     */
    public function index(Organisation $organisation)
    {
        $incomeUseRecords = IncomeUseRecord::where('organisation_id', $organisation->id)
            ->orderBy('period', 'desc')
            ->get();

        return view('organisation.historical-data.income-use-records.index', compact('organisation', 'incomeUseRecords'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\Organisation  $organisation
     * @return \Illuminate\Http\Response
     */
    public function create(Organisation $organisation)
    {
        return view('organisation.historical-data.income-use-records.create', compact('organisation'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Organisation  $organisation
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Organisation $organisation)
    {
        $validated = $request->validate([
            'period' => 'required|integer|min:2000|max:' . date('Y'),
            'administration_amount' => 'required|numeric|min:0',
            'management_activities_amount' => 'required|numeric|min:0',
            'social_services_amount' => 'required|numeric|min:0',
            'law_enforcement_amount' => 'required|numeric|min:0',
            'other_amount' => 'required|numeric|min:0',
            'other_description' => 'nullable|string|max:500',
        ]);

        // Check if a record already exists for this period
        $existingRecord = IncomeUseRecord::where('organisation_id', $organisation->id)
            ->where('period', $validated['period'])
            ->first();

        if ($existingRecord) {
            return redirect()
                ->route('income_use_records.create', $organisation->slug)
                ->withInput()
                ->withErrors(['period' => 'An income utilization record already exists for this year.']);
        }

        $incomeUseRecord = new IncomeUseRecord();
        $incomeUseRecord->organisation_id = $organisation->id;
        $incomeUseRecord->period = $validated['period'];
        $incomeUseRecord->administration_amount = $validated['administration_amount'];
        $incomeUseRecord->management_activities_amount = $validated['management_activities_amount'];
        $incomeUseRecord->social_services_amount = $validated['social_services_amount'];
        $incomeUseRecord->law_enforcement_amount = $validated['law_enforcement_amount'];
        $incomeUseRecord->other_amount = $validated['other_amount'];
        $incomeUseRecord->other_description = $validated['other_description'];
        $incomeUseRecord->save();

        return redirect()
            ->route('income_use_records.index', $organisation->slug)
            ->with('success', 'Income utilization record has been created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Organisation  $organisation
     * @param  \App\Models\HistoricalData\IncomeUseRecord  $incomeUseRecord
     * @return \Illuminate\Http\Response
     */
    public function show(Organisation $organisation, IncomeUseRecord $incomeUseRecord)
    {
        // Ensure the record belongs to the organisation
        if ($incomeUseRecord->organisation_id !== $organisation->id) {
            abort(404);
        }

        return view('organisation.historical-data.income-use-records.show', compact('organisation', 'incomeUseRecord'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Organisation  $organisation
     * @param  \App\Models\HistoricalData\IncomeUseRecord  $incomeUseRecord
     * @return \Illuminate\Http\Response
     */
    public function edit(Organisation $organisation, IncomeUseRecord $incomeUseRecord)
    {
        // Ensure the record belongs to the organisation
        if ($incomeUseRecord->organisation_id !== $organisation->id) {
            abort(404);
        }

        return view('organisation.historical-data.income-use-records.edit', compact('organisation', 'incomeUseRecord'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Organisation  $organisation
     * @param  \App\Models\HistoricalData\IncomeUseRecord  $incomeUseRecord
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Organisation $organisation, IncomeUseRecord $incomeUseRecord)
    {
        // Ensure the record belongs to the organisation
        if ($incomeUseRecord->organisation_id !== $organisation->id) {
            abort(404);
        }

        $validated = $request->validate([
            'period' => 'required|integer|min:2000|max:' . date('Y'),
            'administration_amount' => 'required|numeric|min:0',
            'management_activities_amount' => 'required|numeric|min:0',
            'social_services_amount' => 'required|numeric|min:0',
            'law_enforcement_amount' => 'required|numeric|min:0',
            'other_amount' => 'required|numeric|min:0',
            'other_description' => 'nullable|string|max:500',
        ]);

        // Check if a record already exists for this period (excluding the current record)
        $existingRecord = IncomeUseRecord::where('organisation_id', $organisation->id)
            ->where('period', $validated['period'])
            ->where('id', '!=', $incomeUseRecord->id)
            ->first();

        if ($existingRecord) {
            return redirect()
                ->route('income_use_records.edit', [$organisation->slug, $incomeUseRecord->id])
                ->withInput()
                ->withErrors(['period' => 'Another income utilization record already exists for this year.']);
        }

        $incomeUseRecord->period = $validated['period'];
        $incomeUseRecord->administration_amount = $validated['administration_amount'];
        $incomeUseRecord->management_activities_amount = $validated['management_activities_amount'];
        $incomeUseRecord->social_services_amount = $validated['social_services_amount'];
        $incomeUseRecord->law_enforcement_amount = $validated['law_enforcement_amount'];
        $incomeUseRecord->other_amount = $validated['other_amount'];
        $incomeUseRecord->other_description = $validated['other_description'];
        $incomeUseRecord->save();

        return redirect()
            ->route('income_use_records.index', $organisation->slug)
            ->with('success', 'Income utilization record has been updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Organisation  $organisation
     * @param  \App\Models\HistoricalData\IncomeUseRecord  $incomeUseRecord
     * @return \Illuminate\Http\Response
     */
    public function destroy(Organisation $organisation, IncomeUseRecord $incomeUseRecord)
    {
        // Ensure the record belongs to the organisation
        if ($incomeUseRecord->organisation_id !== $organisation->id) {
            abort(404);
        }

        $incomeUseRecord->delete();

        return redirect()
            ->route('income_use_records.index', $organisation->slug)
            ->with('success', 'Income utilization record has been deleted successfully.');
    }
}
