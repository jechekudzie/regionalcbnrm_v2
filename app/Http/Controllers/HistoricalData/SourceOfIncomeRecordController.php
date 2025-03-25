<?php

namespace App\Http\Controllers\HistoricalData;

use App\Http\Controllers\Controller;
use App\Models\HistoricalData\SourceOfIncomeRecord;
use App\Models\Organisation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SourceOfIncomeRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Models\Organisation  $organisation
     * @return \Illuminate\Http\Response
     */
    public function index(Organisation $organisation)
    {
        $sourceOfIncomeRecords = SourceOfIncomeRecord::where('organisation_id', $organisation->id)
            ->orderBy('period', 'desc')
            ->get();

        return view('organisation.historical-data.source-of-income-records.index', compact('organisation', 'sourceOfIncomeRecords'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\Organisation  $organisation
     * @return \Illuminate\Http\Response
     */
    public function create(Organisation $organisation)
    {
        return view('organisation.historical-data.source-of-income-records.create', compact('organisation'));
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
            'trophy_fee_amount' => 'required|numeric|min:0',
            'hides_amount' => 'required|numeric|min:0',
            'meat_amount' => 'required|numeric|min:0',
            'hunting_concession_fee_amount' => 'required|numeric|min:0',
            'photographic_fee_amount' => 'required|numeric|min:0',
            'other_amount' => 'required|numeric|min:0',
            'other_description' => 'nullable|string|max:500',
        ]);

        // Check if a record already exists for this period
        $existingRecord = SourceOfIncomeRecord::where('organisation_id', $organisation->id)
            ->where('period', $validated['period'])
            ->first();

        if ($existingRecord) {
            return redirect()
                ->route('source_of_income_records.create', $organisation->slug)
                ->withInput()
                ->withErrors(['period' => 'A source of income record already exists for this year.']);
        }

        $sourceOfIncomeRecord = new SourceOfIncomeRecord();
        $sourceOfIncomeRecord->organisation_id = $organisation->id;
        $sourceOfIncomeRecord->period = $validated['period'];
        $sourceOfIncomeRecord->trophy_fee_amount = $validated['trophy_fee_amount'];
        $sourceOfIncomeRecord->hides_amount = $validated['hides_amount'];
        $sourceOfIncomeRecord->meat_amount = $validated['meat_amount'];
        $sourceOfIncomeRecord->hunting_concession_fee_amount = $validated['hunting_concession_fee_amount'];
        $sourceOfIncomeRecord->photographic_fee_amount = $validated['photographic_fee_amount'];
        $sourceOfIncomeRecord->other_amount = $validated['other_amount'];
        $sourceOfIncomeRecord->other_description = $validated['other_description'];
        $sourceOfIncomeRecord->save();

        return redirect()
            ->route('source_of_income_records.index', $organisation->slug)
            ->with('success', 'Source of income record has been created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Organisation  $organisation
     * @param  \App\Models\HistoricalData\SourceOfIncomeRecord  $sourceOfIncomeRecord
     * @return \Illuminate\Http\Response
     */
    public function show(Organisation $organisation, SourceOfIncomeRecord $sourceOfIncomeRecord)
    {
        // Ensure the record belongs to the organisation
        if ($sourceOfIncomeRecord->organisation_id !== $organisation->id) {
            abort(404);
        }

        return view('organisation.historical-data.source-of-income-records.show', compact('organisation', 'sourceOfIncomeRecord'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Organisation  $organisation
     * @param  \App\Models\HistoricalData\SourceOfIncomeRecord  $sourceOfIncomeRecord
     * @return \Illuminate\Http\Response
     */
    public function edit(Organisation $organisation, SourceOfIncomeRecord $sourceOfIncomeRecord)
    {
        // Ensure the record belongs to the organisation
        if ($sourceOfIncomeRecord->organisation_id !== $organisation->id) {
            abort(404);
        }

        return view('organisation.historical-data.source-of-income-records.edit', compact('organisation', 'sourceOfIncomeRecord'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Organisation  $organisation
     * @param  \App\Models\HistoricalData\SourceOfIncomeRecord  $sourceOfIncomeRecord
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Organisation $organisation, SourceOfIncomeRecord $sourceOfIncomeRecord)
    {
        // Ensure the record belongs to the organisation
        if ($sourceOfIncomeRecord->organisation_id !== $organisation->id) {
            abort(404);
        }

        $validated = $request->validate([
            'period' => 'required|integer|min:2000|max:' . date('Y'),
            'trophy_fee_amount' => 'required|numeric|min:0',
            'hides_amount' => 'required|numeric|min:0',
            'meat_amount' => 'required|numeric|min:0',
            'hunting_concession_fee_amount' => 'required|numeric|min:0',
            'photographic_fee_amount' => 'required|numeric|min:0',
            'other_amount' => 'required|numeric|min:0',
            'other_description' => 'nullable|string|max:500',
        ]);

        // Check if a record already exists for this period (excluding the current record)
        $existingRecord = SourceOfIncomeRecord::where('organisation_id', $organisation->id)
            ->where('period', $validated['period'])
            ->where('id', '!=', $sourceOfIncomeRecord->id)
            ->first();

        if ($existingRecord) {
            return redirect()
                ->route('source_of_income_records.edit', [$organisation->slug, $sourceOfIncomeRecord->id])
                ->withInput()
                ->withErrors(['period' => 'Another source of income record already exists for this year.']);
        }

        $sourceOfIncomeRecord->period = $validated['period'];
        $sourceOfIncomeRecord->trophy_fee_amount = $validated['trophy_fee_amount'];
        $sourceOfIncomeRecord->hides_amount = $validated['hides_amount'];
        $sourceOfIncomeRecord->meat_amount = $validated['meat_amount'];
        $sourceOfIncomeRecord->hunting_concession_fee_amount = $validated['hunting_concession_fee_amount'];
        $sourceOfIncomeRecord->photographic_fee_amount = $validated['photographic_fee_amount'];
        $sourceOfIncomeRecord->other_amount = $validated['other_amount'];
        $sourceOfIncomeRecord->other_description = $validated['other_description'];
        $sourceOfIncomeRecord->save();

        return redirect()
            ->route('source_of_income_records.index', $organisation->slug)
            ->with('success', 'Source of income record has been updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Organisation  $organisation
     * @param  \App\Models\HistoricalData\SourceOfIncomeRecord  $sourceOfIncomeRecord
     * @return \Illuminate\Http\Response
     */
    public function destroy(Organisation $organisation, SourceOfIncomeRecord $sourceOfIncomeRecord)
    {
        // Ensure the record belongs to the organisation
        if ($sourceOfIncomeRecord->organisation_id !== $organisation->id) {
            abort(404);
        }

        $sourceOfIncomeRecord->delete();

        return redirect()
            ->route('source_of_income_records.index', $organisation->slug)
            ->with('success', 'Source of income record has been deleted successfully.');
    }
} 