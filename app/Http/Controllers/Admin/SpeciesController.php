<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organisation\Species;
use App\Models\Admin\Organisation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SpeciesController extends Controller
{
    public function index(Organisation $organisation)
    {
       
        $species = Species::paginate(12);
        return view('species.index', compact('species', 'organisation'));
    }

    public function create(Organisation $organisation)
    {
        return view('species.create', compact('organisation'));
    }

    public function store(Request $request, Organisation $organisation)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'scientific' => 'nullable|string|max:255',
            'male_name' => 'nullable|string|max:255',
            'female_name' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_special' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('public/images');
            $validated['avatar'] = str_replace('public/', '', $avatarPath);
        }

        Species::create($validated);

        return redirect()
            ->route('species.index', $organisation->slug)
            ->with('success', 'Species created successfully.');
    }

    public function show(Organisation $organisation, Species $species)
    {
        return view('species.show', compact('species', 'organisation'));
    }

    public function edit(Organisation $organisation, Species $species)
    {
        return view('species.edit', compact('species', 'organisation'));
    }

    public function update(Request $request, Organisation $organisation, Species $species)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'scientific' => 'nullable|string|max:255',
            'male_name' => 'nullable|string|max:255',
            'female_name' => 'nullable|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_special' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('public/images');
            $validated['avatar'] = str_replace('public/', '', $avatarPath);
        }

        $species->update($validated);

        return redirect()
            ->route('species.index', $organisation->slug)
            ->with('success', 'Species updated successfully.');
    }

    public function destroy(Organisation $organisation, Species $species)
    {
        $species->delete();

        return redirect()
            ->route('species.index', $organisation->slug)
            ->with('success', 'Species deleted successfully.');
    }
}
