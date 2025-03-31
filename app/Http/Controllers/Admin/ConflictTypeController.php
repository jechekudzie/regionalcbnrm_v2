<?php

namespace App\Http\Controllers;

use App\Models\ConflictType;
use Illuminate\Http\Request;

class ConflictTypeController extends Controller
{
    //index
    public function index()
    {
        $conflictTypes = ConflictType::all();
        return view('admin.conflict_types.index',compact('conflictTypes'));
    }

    //store
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:conflict_types,name',
        ]);
        ConflictType::create($request->all());
        return redirect()->route('admin.conflict-types.index')->with('success','Conflict Type created successfully.');
    }

    //update
    public function update(Request $request, ConflictType $conflictType)
    {
        $request->validate([
            'name' => 'required|unique:conflict_types,name,'.$conflictType->id,
        ]);
        $conflictType->update($request->all());
        return redirect()->route('admin.conflict-types.index')->with('success','Conflict Type updated successfully.');
    }

    //destroy
    public function destroy(ConflictType $conflictType)
    {
        $conflictType->delete();
        return redirect()->route('admin.conflict-types.index')->with('success','Conflict Type deleted successfully.');
    }
}
