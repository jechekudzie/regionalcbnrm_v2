<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Admin\Organisation;
use App\Models\Organisation\Species;
use App\Models\Organisation\Transaction;
use App\Models\Organisation\TransactionPayable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionPayableController extends Controller
{
    //index get all transaction payables
    public function index(Organisation $organisation, Transaction $transaction)
    {

        $transactionPayables = $transaction->transactionPayables;
        $organisationPayableItems = $organisation->organisationPayableItems;
        $species  = Species::all();

        return view('organisation.transaction_payables.index', compact('organisation','transaction',
            'transactionPayables','organisationPayableItems','species'));
    }

    public function store(Request $request, Organisation $organisation, Transaction $transaction)
    {
        $validated = $request->validate([
            'organisation_payable_item_id' => 'required|exists:organisation_payable_items,id',
            'species_id' => 'nullable|exists:species,id',
            'price' => 'required|numeric',
            'amount' => 'required|numeric|min:0', // Ensure amount is not negative
            'payment_method' => 'required|string',
            'notes' => 'nullable|string',
        ]);



        // Calculate the balance
        $balance = $validated['price'] - $validated['amount'];


        // Optionally, check if the amount paid covers the price
       /* if ($validated['amount'] < $validated['price']) {
            // Redirecting back with a custom validation error
            return redirect()->back()->withErrors(['amount' => 'The amount paid must cover the price of the item.']);

        }*/

        // Begin a transaction to ensure data integrity
        DB::beginTransaction();
        try {
            // Create a new transaction payable
            $transactionPayable = $transaction->transactionPayables()->create([
                'organisation_payable_item_id' => $validated['organisation_payable_item_id'],
                'species_id' => $validated['species_id'],
                'price' => $validated['price'],
                'amount' => $validated['amount'],
                'balance' => $balance,
                'payment_method' => $validated['payment_method'],
                'transaction_date' => now(),
                'notes' => $validated['notes'],
            ]);

            // Commit the transaction
            DB::commit();

            // Redirect or return a response, e.g., back to the form with a success message
            return redirect()->back()->with('success', 'Transaction payable successfully recorded with a balance of ' . $balance);
        } catch (\Exception $e) {
            // Rollback the transaction in case of an error
            DB::rollback();

            // Redirect or return a response, e.g., back to the form with an error message
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }


}
