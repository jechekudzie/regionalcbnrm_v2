<?php

namespace App\Http\Controllers;

use App\Models\CountingMethod;
use Illuminate\Http\Request;

class CountingMethodController extends Controller
{
    //
    public function index()
    {
        $countingMethods = CountingMethod::all();
        return view('admin.counting_methods.index', compact('countingMethods'));
    }

    public function create()
    {
        return view('admin.counting_methods.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'nullable',
        ]);

        CountingMethod::create($request->all());

        return redirect()->route('admin.counting-methods.index')->with('success', 'Counting Method added successfully');
    }

    public function edit(CountingMethod $countingMethod)
    {
        return view('admin.counting_methods.edit', compact('countingMethod'));
    }

    public function update(Request $request, CountingMethod $countingMethod)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'nullable',
        ]);

        $countingMethod->update($request->all());

        return redirect()->route('admin.counting-methods.index')->with('success', 'Counting Method updated successfully');
    }

    public function destroy(CountingMethod $countingMethod)
    {
        $countingMethod->delete();

        return redirect()->route('admin.counting-methods.index')->with('success', 'Counting Method deleted successfully');
    }
}
