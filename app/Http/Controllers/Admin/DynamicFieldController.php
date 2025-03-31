<?php

namespace App\Http\Controllers;

use App\Models\ConflictOutCome;
use App\Models\DynamicField;
use Illuminate\Http\Request;

class DynamicFieldController extends Controller
{
    //index method passing the data to the view
    public function index(ConflictOutCome $ConflictOutCome)
    {
        $fields = $ConflictOutCome->dynamicFields()->get();
        return view('admin.conflict_outcomes.add_dynamic_fields', compact('fields', 'ConflictOutCome'));
    }

    //store method to store the data
    public function store(Request $request, ConflictOutCome $ConflictOutCome)
    {
        // Retrieve the 'field_name' from the request and modify it
        $field_name = $request->input('field_name');
        $modified_field_name = strtolower(str_replace(' ', '_', $field_name)); // Convert to lowercase and replace spaces with underscores

        // Replace the original 'field_name' in the request with the modified version
        $request->merge(['field_name' => $modified_field_name]);

       // Validate the request
        $request->validate([
            'field_name' => 'required',
            'field_type' => 'required',
            'label' => 'required',
        ]);
        $ConflictOutCome->dynamicFields()->create($request->all());
        return redirect()->back()->with('success', 'Field added successfully.');
    }

    //update method to update the data
    public function update(Request $request, ConflictOutCome $ConflictOutCome, DynamicField $dynamicField)
    {
        // Retrieve the 'field_name' from the request and modify it
        $field_name = $request->input('field_name');
        $modified_field_name = strtolower(str_replace(' ', '_', $field_name)); // Convert to lowercase and replace spaces with underscores

        // Replace the original 'field_name' in the request with the modified version
        $request->merge(['field_name' => $modified_field_name]);

        // Validate the request
        $request->validate([
            'field_name' => 'required',
            'field_type' => 'required',
            'label' => 'required',
        ]);
        $dynamicField->update($request->all());
        return redirect()->back()->with('success', 'Field updated successfully.');
    }
}
