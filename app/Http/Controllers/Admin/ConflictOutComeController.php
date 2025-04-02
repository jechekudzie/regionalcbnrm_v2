<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Admin\ConflictOutCome;
use App\Models\Admin\ConflictType;
use Illuminate\Http\Request;

class ConflictOutComeController extends Controller
{
    //index method passing the data to the view
    public function index()
    {
        $ConflictOutComes = ConflictOutCome::all();
        $conflictTypes = ConflictType::all();
        return view('admin.conflict_outcomes.index', compact('ConflictOutComes','conflictTypes'));
    }

    //store method
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:conflict_out_comes,name',
            'conflict_type_id' => 'required|exists:conflict_types,id',
        ]);

        $ConflictOutCome = ConflictOutCome::create([
            'name' => $validated['name'],
            'conflict_type_id' => $validated['conflict_type_id'],
        ]);

        return redirect()->route('admin.conflict-outcomes.index')->with('success', 'Conflict Outcome created successfully');
    }

    //update method
    public function update(Request $request, ConflictOutCome $ConflictOutCome)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'conflict_type_id' => 'required|exists:conflict_types,id',
        ]);

        $ConflictOutCome->update([
            'name' => $validated['name'],
            'conflict_type_id' => $validated['conflict_type_id'],
        ]);

        return redirect()->route('admin.conflict-outcomes.index')->with('success', 'Conflict Outcome updated successfully');
    }
}
