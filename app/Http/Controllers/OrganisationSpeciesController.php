<?php

namespace App\Http\Controllers;

use App\Models\Organisation;
use App\Models\Species;
use Illuminate\Http\Request;

class OrganisationSpeciesController extends Controller
{
    //
    public function index(Organisation $organisation)
    {
        $species = Species::all();
        return view('organisation.wildlife.index',compact('species','organisation'));
    }
}
