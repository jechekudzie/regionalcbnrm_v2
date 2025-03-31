<?php

namespace App\Http\Controllers;

use App\Models\ConflictOutCome;
use App\Models\DynamicField;
use App\Models\Organisation;
use Illuminate\Http\Request;

class DynamicFieldOptionController extends Controller
{
    //index
    public function index()
    {
        return view('admin.conflict_outcomes.add_dynamic_fields');
    }

    //store
    public function store(Request $request, ConflictOutCome $ConflictOutCome, DynamicField $dynamicField)
    {
        $request->validate([
            'option_value' => 'required',
            'option_label' => 'required',
        ]);
        $dynamicField->options()->create($request->all());
        return redirect()->back()->with('success', 'Field added successfully.');
    }

}
