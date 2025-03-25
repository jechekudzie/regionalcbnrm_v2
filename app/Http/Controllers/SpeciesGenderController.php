<?php

namespace App\Http\Controllers;

use App\Models\SpeciesGender;
use Illuminate\Http\Request;

class SpeciesGenderController extends Controller
{
    //index
    public function index()
    {

        $species_genders = SpeciesGender::all();
        return view('admin.species_gender.index', compact('species_genders'));
    }


    //store
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        SpeciesGender::create($request->all());

        return redirect()->route('admin.species-gender.index')->with('success', 'Species gender added successfully.');
    }

    public function update(Request $request, SpeciesGender $speciesGender)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        $speciesGender->update($request->all());

        return redirect()->route('admin.species-gender.index')->with('success', 'Species gender updated successfully.');
    }


    public function destroy(Request $request, SpeciesGender $speciesGender){

        $speciesGender->delete();
        return redirect()->route('admin.species-gender.index')->with('success', 'Species gender deleted successfully.');
    }


}
