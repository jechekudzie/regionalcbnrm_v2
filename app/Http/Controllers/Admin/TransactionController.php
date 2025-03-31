<?php

namespace App\Http\Controllers;

use App\Models\Organisation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    //index get all transaction for Organisation
    public function index(Organisation $organisation)
    {
        $transactions = $organisation->transactions;
        return view('organisation.transactions.index', compact('transactions'));
    }

    //store new transaction for Organisation
    public function store(Request $request, Organisation $organisation)
    {
        // Validate incoming request
        $transaction = $request->validate([
            'transaction_type' => 'required|string',
            'customer_or_donor' => 'nullable|string',
            'payment_method' => 'nullable|string',
            'status' => 'nullable|string',
            'reference_number' => 'nullable|string|unique:transactions',
            'amount' => 'nullable|numeric',
            'transaction_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        // Create a new transaction record
        $transaction = $organisation->transactions()->create($transaction);

        //create reference number str pad 4
        $transaction->reference_number = 'TRXN' . str_pad($transaction->id, 4, '0', STR_PAD_LEFT);
        $transaction->save();

        return redirect()->route('organisation.transaction-payables.index', [$organisation->slug, $transaction->id])->with('success', 'Transaction created successfully');

    }


}
