<?php

namespace App\Http\Controllers\Organisation;

use App\Http\Controllers\Controller;
use App\Models\HuntingConcession;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Organisation;

class HuntingConcessionController extends Controller
{
    public function index(Organisation $organisation)
    {
        $concessions = HuntingConcession::where('organisation_id', $organisation->id)
            ->paginate(12);
        return view('organisation.hunting-concessions.index', compact('concessions', 'organisation'));
    }

    public function create(Organisation $organisation)
    {
        return view('organisation.hunting-concessions.create',compact('organisation'));
    }

    public function store(Request $request, Organisation $organisation)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'safari_id' => 'nullable|exists:safaris,id',
            'hectarage' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'latitude' => 'nullable|string|max:255',
            'longitude' => 'nullable|string|max:255',
        ]);

        $validated['organisation_id'] = $organisation->id;
    

        HuntingConcession::create($validated);

        return redirect()
            ->route('organisation.hunting-concessions.index', $organisation->slug)
            ->with('success', 'Hunting concession created successfully.');
    }

    public function show(Organisation $organisation, HuntingConcession $huntingConcession)
    {
        return view('organisation.hunting-concessions.show', compact('huntingConcession', 'organisation'));
    }

    public function edit(Organisation $organisation, HuntingConcession $huntingConcession)
    {
        return view('organisation.hunting-concessions.edit', compact('huntingConcession', 'organisation'));
    }

    public function update(Request $request, Organisation $organisation, HuntingConcession $huntingConcession)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'safari_id' => 'nullable|exists:safaris,id',
            'hectarage' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'latitude' => 'nullable|string|max:255',
            'longitude' => 'nullable|string|max:255',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $huntingConcession->update($validated);

        return redirect()
            ->route('organisation.hunting-concessions.show', [$organisation->slug, $huntingConcession])
            ->with('success', 'Hunting concession updated successfully.');
    }

    public function destroy(Organisation $organisation, HuntingConcession $huntingConcession)
    {
        $huntingConcession->delete();

        return redirect()
            ->route('organisation.hunting-concessions.index', $organisation->slug)
            ->with('success', 'Hunting concession deleted successfully.');
    }
} 