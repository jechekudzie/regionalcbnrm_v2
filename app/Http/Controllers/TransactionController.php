<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin\Organisation;

class TransactionController extends Controller
{
    public function index(Organisation $organisation)
    {
        return view('organisation.transactions.index', compact('organisation'));
    }

    public function create(Organisation $organisation)
    {
        return view('organisation.transactions.create', compact('organisation'));
    }

    public function store(Request $request, Organisation $organisation)
    {
        // Placeholder
        return redirect()->route('organisation.transactions.index', $organisation->slug);
    }

    public function edit(Organisation $organisation, $transaction)
    {
        return view('organisation.transactions.edit', compact('organisation', 'transaction'));
    }

    public function update(Request $request, Organisation $organisation, $transaction)
    {
        // Placeholder
        return redirect()->route('organisation.transactions.index', $organisation->slug);
    }

    public function destroy(Organisation $organisation, $transaction)
    {
        // Placeholder
        return redirect()->route('organisation.transactions.index', $organisation->slug);
    }
} 