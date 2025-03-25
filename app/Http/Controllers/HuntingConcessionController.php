<?php

namespace App\Http\Controllers;

use App\Models\HuntingConcession;
use App\Models\Organisation;
use Illuminate\Http\Request;

class HuntingConcessionController extends Controller
{
    //index pass hunting concessions to view
    public function index(Organisation $organisation)
    {
        //dd($organisation->getSafariOperators());
        $huntingConcessions = HuntingConcession::all();
        return view('organisation.hunting_concessions.index', compact('huntingConcessions', 'organisation'));
    }

    //store method attach wards to hunting concession (organisation_id as foreign key) attact to huting_conecssion_wards table
    public function store(Request $request, Organisation $organisation)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'hectarage' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'safari_id' => 'required',
            'wards' => 'required|array',
            'wards.*' => 'exists:organisations,id'
        ]);


        $huntingConcession = $organisation->huntingConcessions()->create([
            'name' => $validated['name'],
            'hectarage' => $validated['hectarage'],
            'description' => $validated['description'],
            'safari_id' => $validated['safari_id'],
        ]);

        if($huntingConcession && $validated['wards']){
            $huntingConcession->wards()->attach($validated['wards']);
        }


        return redirect()->route('organisation.hunting-concessions.index', $organisation->slug);

    }

}
