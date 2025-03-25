<?php

namespace App\Http\Controllers\HistoricalData;

use App\Http\Controllers\Controller;
use App\Models\Organisation;
use App\Models\HistoricalData\IncomeRecord;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class IncomeRecordController extends Controller
{
    public function index(Organisation $organisation)
    {
        $incomeRecords = IncomeRecord::where('organisation_id', $organisation->id)
            ->orderBy('period', 'desc')
            ->get();
            
        return view('organisation.historical-data.income-records.index', compact('organisation', 'incomeRecords'));
    }

    public function create(Organisation $organisation)
    {
        return view('organisation.historical-data.income-records.create', compact('organisation'));
    }

    public function store(Request $request, Organisation $organisation)
    {
        $validated = $request->validate([
            'period' => [
                'required',
                'integer',
                'min:1900',
                'max:' . date('Y'),
                Rule::unique('income_records')->where(function ($query) use ($organisation) {
                    return $query->where('organisation_id', $organisation->id);
                }),
            ],
            'rdc_share' => 'required|numeric|min:0',
            'community_share' => 'required|numeric|min:0',
            'ca_share' => 'required|numeric|min:0',
        ], [
            'period.unique' => 'An income record for this year already exists for this organisation.'
        ]);

        $validated['organisation_id'] = $organisation->id;

        IncomeRecord::create($validated);

        return redirect()->route('income_records.index', $organisation->slug)
            ->with('success', 'Income record has been added successfully.');
    }

    public function show(Organisation $organisation, IncomeRecord $incomeRecord)
    {
        return view('organisation.historical-data.income-records.show', compact('organisation', 'incomeRecord'));
    }

    public function edit(Organisation $organisation, IncomeRecord $incomeRecord)
    {
        return view('organisation.historical-data.income-records.edit', compact('organisation', 'incomeRecord'));
    }

    public function update(Request $request, Organisation $organisation, IncomeRecord $incomeRecord)
    {
        $validated = $request->validate([
            'period' => [
                'required',
                'integer',
                'min:1900',
                'max:' . date('Y'),
                Rule::unique('income_records')->where(function ($query) use ($organisation) {
                    return $query->where('organisation_id', $organisation->id);
                })->ignore($incomeRecord->id),
            ],
            'rdc_share' => 'required|numeric|min:0',
            'community_share' => 'required|numeric|min:0',
            'ca_share' => 'required|numeric|min:0',
        ], [
            'period.unique' => 'An income record for this year already exists for this organisation.'
        ]);

        $incomeRecord->update($validated);

        return redirect()
            ->route('income_records.index', $organisation->slug)
            ->with('success', 'Income record has been updated successfully.');
    }

    public function destroy(Organisation $organisation, IncomeRecord $incomeRecord)
    {
        $incomeRecord->delete();

        return redirect()
            ->route('income_records.index', $organisation->slug)
            ->with('success', 'Income record has been deleted successfully.');
    }
} 