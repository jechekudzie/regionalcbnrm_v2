<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin\Organisation;

class TransactionPayableController extends Controller
{
    public function index(Organisation $organisation, $transaction)
    {
        return view('organisation.transaction-payables.index', compact('organisation', 'transaction'));
    }

    public function store(Request $request, Organisation $organisation, $transaction)
    {
        // Placeholder
        return redirect()->route('organisation.transaction-payables.index', [$organisation->slug, $transaction]);
    }

    public function edit(Organisation $organisation, $transaction, $transactionPayable)
    {
        return view('organisation.transaction-payables.edit', compact('organisation', 'transaction', 'transactionPayable'));
    }

    public function update(Request $request, Organisation $organisation, $transaction, $transactionPayable)
    {
        // Placeholder
        return redirect()->route('organisation.transaction-payables.index', [$organisation->slug, $transaction]);
    }

    public function destroy(Organisation $organisation, $transaction, $transactionPayable)
    {
        // Placeholder
        return redirect()->route('organisation.transaction-payables.index', [$organisation->slug, $transaction]);
    }
} 