<?php

namespace App\Http\Controllers\Organisation;

use App\Http\Controllers\Controller;
use App\Models\Admin\OffenceType;
use App\Models\Admin\Organisation;
use App\Models\Admin\PoacherType;
use App\Models\Admin\PoachingMethod;
use App\Models\Admin\PoachingReason;
use App\Models\Organisation\Poacher;
use App\Models\Organisation\PoachingIncident;
use App\Models\Organisation\Species;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PoachingIncidentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Organisation $organisation)
    {
        $poachingIncidents = PoachingIncident::where('organisation_id', $organisation->id)
            ->orderBy('date', 'desc')
            ->paginate(10);

        return view('organisation.poaching-incidents.index', compact('organisation', 'poachingIncidents'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Organisation $organisation)
    {
        $species = Species::all();
        $poachingMethods = PoachingMethod::all();
        $poacherTypes = PoacherType::all();
        $offenceTypes = OffenceType::all();
        $poachingReasons = PoachingReason::all();

        return view('organisation.poaching-incidents.create', compact(
            'organisation',
            'species',
            'poachingMethods',
            'poacherTypes',
            'offenceTypes',
            'poachingReasons'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Organisation $organisation)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'location' => 'nullable|string',
            'longitude' => 'nullable|numeric',
            'latitude' => 'nullable|numeric',
            'period' => 'required|integer',
            'date' => 'required|date',
            'time' => 'required',
            'docket_number' => 'nullable|string|max:255',
            'docket_status' => 'nullable|string|in:open,under investigation,closed,pending court,convicted',
            'species' => 'required|array',
            'species.*.id' => 'required|exists:species,id',
            'species.*.estimate_number' => 'nullable|integer|min:0',
            'poaching_methods' => 'required|array',
            'poaching_methods.*' => 'exists:poaching_methods,id',
            'poachers' => 'required|array',
            'poachers.*.first_name' => 'required|string|max:255',
            'poachers.*.last_name' => 'required|string|max:255',
            'poachers.*.middle_name' => 'nullable|string|max:255',
            'poachers.*.age' => 'nullable|integer',
            'poachers.*.status' => 'nullable|string|in:suspected,arrested,bailed,sentenced,released',
            'poachers.*.country_id' => 'nullable|exists:countries,id',
            'poachers.*.province_id' => 'nullable|exists:provinces,id',
            'poachers.*.city_id' => 'nullable|exists:cities,id',
            'poachers.*.offence_type_id' => 'nullable|exists:offence_types,id',
            'poachers.*.poacher_type_id' => 'nullable|exists:poacher_types,id',
            'poachers.*.poaching_reason_id' => 'nullable|exists:poaching_reasons,id',
        ]);

        try {
            DB::beginTransaction();

            // Create poaching incident
            $poachingIncident = PoachingIncident::create([
                'organisation_id' => $organisation->id,
                'title' => $request->title,
                'location' => $request->location,
                'longitude' => $request->longitude,
                'latitude' => $request->latitude,
                'docket_number' => $request->docket_number,
                'docket_status' => $request->docket_status,
                'period' => $request->period,
                'date' => $request->date,
                'time' => $request->time,
            ]);

            // Attach species
            foreach ($request->species as $speciesData) {
                $poachingIncident->species()->attach($speciesData['id'], [
                    'estimate_number' => $speciesData['estimate_number'] ?? null,
                ]);
            }

            // Attach poaching methods
            $poachingIncident->methods()->attach($request->poaching_methods);

            // Create poachers
            foreach ($request->poachers as $poacherData) {
                $poachingIncident->poachers()->create([
                    'first_name' => $poacherData['first_name'],
                    'last_name' => $poacherData['last_name'],
                    'middle_name' => $poacherData['middle_name'] ?? null,
                    'age' => $poacherData['age'] ?? null,
                    'status' => $poacherData['status'] ?? null,
                    'country_id' => $poacherData['country_id'] ?? null,
                    'province_id' => $poacherData['province_id'] ?? null,
                    'city_id' => $poacherData['city_id'] ?? null,
                    'offence_type_id' => $poacherData['offence_type_id'] ?? null,
                    'poacher_type_id' => $poacherData['poacher_type_id'] ?? null,
                    'poaching_reason_id' => $poacherData['poaching_reason_id'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('organisation.poaching-incidents.show', [$organisation->slug, $poachingIncident->id])
                ->with('success', 'Poaching incident created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to create poaching incident. ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Organisation $organisation, PoachingIncident $poachingIncident)
    {
        $poachingIncident->load(['species', 'methods', 'poachers']);
        
        return view('organisation.poaching-incidents.show', compact('organisation', 'poachingIncident'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Organisation $organisation, PoachingIncident $poachingIncident)
    {
        $poachingIncident->load(['species', 'methods', 'poachers']);
        
        $species = Species::all();
        $poachingMethods = PoachingMethod::all();
        $poacherTypes = PoacherType::all();
        $offenceTypes = OffenceType::all();
        $poachingReasons = PoachingReason::all();

        return view('organisation.poaching-incidents.edit', compact(
            'organisation',
            'poachingIncident',
            'species',
            'poachingMethods',
            'poacherTypes',
            'offenceTypes',
            'poachingReasons'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Organisation $organisation, PoachingIncident $poachingIncident)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'location' => 'nullable|string',
            'longitude' => 'nullable|numeric',
            'latitude' => 'nullable|numeric',
            'period' => 'required|integer',
            'date' => 'required|date',
            'time' => 'required',
            'docket_number' => 'nullable|string|max:255',
            'docket_status' => 'nullable|string|in:open,under investigation,closed,pending court,convicted',
            'species' => 'required|array',
            'species.*.id' => 'required|exists:species,id',
            'species.*.estimate_number' => 'nullable|integer|min:0',
            'poaching_methods' => 'required|array',
            'poaching_methods.*' => 'exists:poaching_methods,id',
            'poachers' => 'required|array',
            'poachers.*.id' => 'nullable|exists:poachers,id',
            'poachers.*.first_name' => 'required|string|max:255',
            'poachers.*.last_name' => 'required|string|max:255',
            'poachers.*.middle_name' => 'nullable|string|max:255',
            'poachers.*.age' => 'nullable|integer',
            'poachers.*.status' => 'nullable|string|in:suspected,arrested,bailed,sentenced,released',
            'poachers.*.country_id' => 'nullable|exists:countries,id',
            'poachers.*.province_id' => 'nullable|exists:provinces,id',
            'poachers.*.city_id' => 'nullable|exists:cities,id',
            'poachers.*.offence_type_id' => 'nullable|exists:offence_types,id',
            'poachers.*.poacher_type_id' => 'nullable|exists:poacher_types,id',
            'poachers.*.poaching_reason_id' => 'nullable|exists:poaching_reasons,id',
        ]);

        try {
            DB::beginTransaction();

            // Update poaching incident
            $poachingIncident->update([
                'title' => $request->title,
                'location' => $request->location,
                'longitude' => $request->longitude,
                'latitude' => $request->latitude,
                'docket_number' => $request->docket_number,
                'docket_status' => $request->docket_status,
                'period' => $request->period,
                'date' => $request->date,
                'time' => $request->time,
            ]);

            // Update species
            $poachingIncident->species()->detach();
            foreach ($request->species as $speciesData) {
                $poachingIncident->species()->attach($speciesData['id'], [
                    'estimate_number' => $speciesData['estimate_number'] ?? null,
                ]);
            }

            // Update poaching methods
            $poachingIncident->methods()->sync($request->poaching_methods);

            // Process existing and new poachers
            $existingPoacherIds = [];
            foreach ($request->poachers as $poacherData) {
                if (isset($poacherData['id']) && $poacherData['id']) {
                    // Update existing poacher
                    $poacher = Poacher::find($poacherData['id']);
                    if ($poacher && $poacher->poaching_incident_id == $poachingIncident->id) {
                        $poacher->update([
                            'first_name' => $poacherData['first_name'],
                            'last_name' => $poacherData['last_name'],
                            'middle_name' => $poacherData['middle_name'] ?? null,
                            'age' => $poacherData['age'] ?? null,
                            'status' => $poacherData['status'] ?? null,
                            'country_id' => $poacherData['country_id'] ?? null,
                            'province_id' => $poacherData['province_id'] ?? null,
                            'city_id' => $poacherData['city_id'] ?? null,
                            'offence_type_id' => $poacherData['offence_type_id'] ?? null,
                            'poacher_type_id' => $poacherData['poacher_type_id'] ?? null,
                            'poaching_reason_id' => $poacherData['poaching_reason_id'] ?? null,
                        ]);
                        $existingPoacherIds[] = $poacher->id;
                    } else {
                        // If poacher doesn't exist or doesn't belong to this incident, create a new one
                        $poacher = $poachingIncident->poachers()->create([
                            'first_name' => $poacherData['first_name'],
                            'last_name' => $poacherData['last_name'],
                            'middle_name' => $poacherData['middle_name'] ?? null,
                            'age' => $poacherData['age'] ?? null,
                            'status' => $poacherData['status'] ?? null,
                            'country_id' => $poacherData['country_id'] ?? null,
                            'province_id' => $poacherData['province_id'] ?? null,
                            'city_id' => $poacherData['city_id'] ?? null,
                            'offence_type_id' => $poacherData['offence_type_id'] ?? null,
                            'poacher_type_id' => $poacherData['poacher_type_id'] ?? null,
                            'poaching_reason_id' => $poacherData['poaching_reason_id'] ?? null,
                        ]);
                        $existingPoacherIds[] = $poacher->id;
                    }
                } else {
                    // Create new poacher
                    $poacher = $poachingIncident->poachers()->create([
                        'first_name' => $poacherData['first_name'],
                        'last_name' => $poacherData['last_name'],
                        'middle_name' => $poacherData['middle_name'] ?? null,
                        'age' => $poacherData['age'] ?? null,
                        'status' => $poacherData['status'] ?? null,
                        'country_id' => $poacherData['country_id'] ?? null,
                        'province_id' => $poacherData['province_id'] ?? null,
                        'city_id' => $poacherData['city_id'] ?? null,
                        'offence_type_id' => $poacherData['offence_type_id'] ?? null,
                        'poacher_type_id' => $poacherData['poacher_type_id'] ?? null,
                        'poaching_reason_id' => $poacherData['poaching_reason_id'] ?? null,
                    ]);
                    $existingPoacherIds[] = $poacher->id;
                }
            }

            // Delete poachers that were removed
            $poachingIncident->poachers()
                ->whereNotIn('id', $existingPoacherIds)
                ->delete();

            DB::commit();

            return redirect()
                ->route('organisation.poaching-incidents.show', [$organisation->slug, $poachingIncident->id])
                ->with('success', 'Poaching incident updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to update poaching incident. ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organisation $organisation, PoachingIncident $poachingIncident)
    {
        try {
            DB::beginTransaction();
            
            // Delete associated poachers
            $poachingIncident->poachers()->delete();
            
            // Delete the poaching incident
            $poachingIncident->delete();
            
            DB::commit();
            
            return redirect()
                ->route('organisation.poaching-incidents.index', $organisation->slug)
                ->with('success', 'Poaching incident deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete poaching incident. ' . $e->getMessage());
        }
    }
}
