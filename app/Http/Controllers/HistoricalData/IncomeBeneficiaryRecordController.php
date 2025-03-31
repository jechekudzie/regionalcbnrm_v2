<?php

namespace App\Http\Controllers\HistoricalData;

use App\Http\Controllers\Controller;
use App\Models\HistoricalData\IncomeBeneficiaryRecord;
use App\Models\Admin\Organisation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IncomeBeneficiaryRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Models\Organisation  $organisation
     * @return \Illuminate\Http\Response
     */
    public function index(Organisation $organisation)
    {
        $incomeBeneficiaryRecords = IncomeBeneficiaryRecord::where('organisation_id', $organisation->id)
            ->orderBy('period', 'desc')
            ->get();

        return view('organisation.historical-data.income-beneficiary-records.index', compact('organisation', 'incomeBeneficiaryRecords'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\Organisation  $organisation
     * @return \Illuminate\Http\Response
     */
    public function create(Organisation $organisation)
    {
        return view('organisation.historical-data.income-beneficiary-records.create', compact('organisation'));
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
            'households' => 'required|integer|min:0',
            'males' => 'required|integer|min:0',
            'females' => 'required|integer|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Check if a record already exists for this period
        $existingRecord = IncomeBeneficiaryRecord::where('organisation_id', $organisation->id)
            ->where('period', $validated['period'])
            ->first();

        if ($existingRecord) {
            return redirect()
                ->route('income_beneficiary_records.create', $organisation->slug)
                ->withInput()
                ->withErrors(['period' => 'An income beneficiary record already exists for this year.']);
        }

        $incomeBeneficiaryRecord = new IncomeBeneficiaryRecord();
        $incomeBeneficiaryRecord->organisation_id = $organisation->id;
        $incomeBeneficiaryRecord->period = $validated['period'];
        $incomeBeneficiaryRecord->households = $validated['households'];
        $incomeBeneficiaryRecord->males = $validated['males'];
        $incomeBeneficiaryRecord->females = $validated['females'];
        $incomeBeneficiaryRecord->notes = $validated['notes'];
        $incomeBeneficiaryRecord->save();

        return redirect()
            ->route('income_beneficiary_records.index', $organisation->slug)
            ->with('success', 'Income beneficiary record has been created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Organisation  $organisation
     * @param  \App\Models\HistoricalData\IncomeBeneficiaryRecord  $incomeBeneficiaryRecord
     * @return \Illuminate\Http\Response
     */
    public function show(Organisation $organisation, IncomeBeneficiaryRecord $incomeBeneficiaryRecord)
    {
        // Ensure the record belongs to the organisation
        if ($incomeBeneficiaryRecord->organisation_id !== $organisation->id) {
            abort(404);
        }

        return view('organisation.historical-data.income-beneficiary-records.show', compact('organisation', 'incomeBeneficiaryRecord'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Organisation  $organisation
     * @param  \App\Models\HistoricalData\IncomeBeneficiaryRecord  $incomeBeneficiaryRecord
     * @return \Illuminate\Http\Response
     */
    public function edit(Organisation $organisation, IncomeBeneficiaryRecord $incomeBeneficiaryRecord)
    {
        // Ensure the record belongs to the organisation
        if ($incomeBeneficiaryRecord->organisation_id !== $organisation->id) {
            abort(404);
        }

        return view('organisation.historical-data.income-beneficiary-records.edit', compact('organisation', 'incomeBeneficiaryRecord'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Organisation  $organisation
     * @param  \App\Models\HistoricalData\IncomeBeneficiaryRecord  $incomeBeneficiaryRecord
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Organisation $organisation, IncomeBeneficiaryRecord $incomeBeneficiaryRecord)
    {
        // Ensure the record belongs to the organisation
        if ($incomeBeneficiaryRecord->organisation_id !== $organisation->id) {
            abort(404);
        }

        $validated = $request->validate([
            'period' => 'required|integer|min:2000|max:' . date('Y'),
            'households' => 'required|integer|min:0',
            'males' => 'required|integer|min:0',
            'females' => 'required|integer|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Check if a record already exists for this period (excluding the current record)
        $existingRecord = IncomeBeneficiaryRecord::where('organisation_id', $organisation->id)
            ->where('period', $validated['period'])
            ->where('id', '!=', $incomeBeneficiaryRecord->id)
            ->first();

        if ($existingRecord) {
            return redirect()
                ->route('income_beneficiary_records.edit', [$organisation->slug, $incomeBeneficiaryRecord->id])
                ->withInput()
                ->withErrors(['period' => 'Another income beneficiary record already exists for this year.']);
        }

        $incomeBeneficiaryRecord->period = $validated['period'];
        $incomeBeneficiaryRecord->households = $validated['households'];
        $incomeBeneficiaryRecord->males = $validated['males'];
        $incomeBeneficiaryRecord->females = $validated['females'];
        $incomeBeneficiaryRecord->notes = $validated['notes'];
        $incomeBeneficiaryRecord->save();

        return redirect()
            ->route('income_beneficiary_records.index', $organisation->slug)
            ->with('success', 'Income beneficiary record has been updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Organisation  $organisation
     * @param  \App\Models\HistoricalData\IncomeBeneficiaryRecord  $incomeBeneficiaryRecord
     * @return \Illuminate\Http\Response
     */
    public function destroy(Organisation $organisation, IncomeBeneficiaryRecord $incomeBeneficiaryRecord)
    {
        // Ensure the record belongs to the organisation
        if ($incomeBeneficiaryRecord->organisation_id !== $organisation->id) {
            abort(404);
        }

        $incomeBeneficiaryRecord->delete();

        return redirect()
            ->route('income_beneficiary_records.index', $organisation->slug)
            ->with('success', 'Income beneficiary record has been deleted successfully.');
    }
}
