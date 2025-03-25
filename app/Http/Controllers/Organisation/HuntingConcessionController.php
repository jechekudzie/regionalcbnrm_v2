<?php

namespace App\Http\Controllers\Organisation;

use App\Http\Controllers\Controller;
use App\Models\HuntingConcession;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HuntingConcessionController extends Controller
{
    public function index()
    {
        $concessions = HuntingConcession::where('organisation_id', auth()->user()->organisation_id)
            ->paginate(12);
        return view('organisation.hunting-concessions.index', compact('concessions'));
    }

    public function create()
    {
        return view('organisation.hunting-concessions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'safari_id' => 'nullable|exists:safaris,id',
            'hectarage' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'latitude' => 'nullable|string|max:255',
            'longitude' => 'nullable|string|max:255',
        ]);

        $validated['organisation_id'] = auth()->user()->organisation_id;
        $validated['slug'] = Str::slug($validated['name']);

        HuntingConcession::create($validated);

        return redirect()
            ->route('organisation.hunting-concessions.index')
            ->with('success', 'Hunting concession created successfully.');
    }

    public function show(HuntingConcession $huntingConcession)
    {
        return view('organisation.hunting-concessions.show', compact('huntingConcession'));
    }

    public function edit(HuntingConcession $huntingConcession)
    {
        return view('organisation.hunting-concessions.edit', compact('huntingConcession'));
    }

    public function update(Request $request, HuntingConcession $huntingConcession)
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
            ->route('organisation.hunting-concessions.index')
            ->with('success', 'Hunting concession updated successfully.');
    }

    public function destroy(HuntingConcession $huntingConcession)
    {
        $huntingConcession->delete();

        return redirect()
            ->route('organisation.hunting-concessions.index')
            ->with('success', 'Hunting concession deleted successfully.');
    }
} 