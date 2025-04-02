<?php

namespace App\Http\Controllers\Admin ;

use App\Http\Controllers\Controller;

use App\Models\Admin\Maturity;
use Illuminate\Http\Request;

class MaturityController extends Controller
{
    //
    public function index()
    {
        $speciesMaturity = Maturity::all();
        return view('admin.species_maturity.index', compact('speciesMaturity'));
    }

    public function create()
    {
        return view('admin.species_maturity.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'nullable',
        ]);

        Maturity::create($request->all());

        return redirect()->route('admin.maturity.index')->with('success', 'Maturity added successfully');
    }

    public function edit(Maturity $maturity)
    {
        return view('admin.species_maturity.edit', compact('maturity'));
    }

    public function update(Request $request, Maturity $maturity)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'nullable',
        ]);

        $maturity->update($request->all());

        return redirect()->route('admin.maturity.index')->with('success', 'Maturity updated successfully');
    }

    public function destroy(Maturity $maturity)
    {
        $maturity->delete();

        return redirect()->route('admin.maturity.index')->with('success', 'Maturity deleted successfully');
    }
}
