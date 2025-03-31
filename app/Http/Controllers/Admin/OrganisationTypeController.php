<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\OrganisationType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrganisationTypeController extends Controller
{
    //index
    public function index()
    {
        return view('admin.organisation_types.index');
    }

    //store
    public function store()
    {
        try {
            //validate the request data
            $validatedData = request()->validate([
                'name' => 'required',
                'description' => 'nullable',
            ]);
            //create a new organisation type
            $organisationType = OrganisationType::create($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Organisation type created successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->validator->getMessageBag()->toArray()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => [$e->getMessage()]
            ], 500);
        }
        //redirect to the organisation type index page
        //return redirect()->route('admin.organisation-types.index');

    }

    public function organisationTypeOrganisation(OrganisationType $organisationType)
    {

        //validate the request data
        $validatedData = request()->validate([
            'name' => 'required',
            'description' => 'nullable',
        ]);

        $newOrganisationType = OrganisationType::create($validatedData);

        // Associate the new OrganisationType as a child of the existing one
        $organisationType->children()->attach($newOrganisationType->id, [
            'notes' => $newOrganisationType->description,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        //redirect to the organisation type index page
        return redirect()->route('admin.organisation-types.index');
    }



    // manage organisation types
    public function manage()
    {
        $organisationTypes = OrganisationType::with(['parents', 'children'])->get();
        return view('admin.organisation_types.manage', compact('organisationTypes'));
    }

    public function createOrgType($parent = null)
    {
        $parent = $parent ? OrganisationType::findOrFail($parent) : null;
        return view('admin.organisation_types.create', compact('parent'));
    }

    public function storeOrgType(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'parent_id' => 'nullable|exists:organisation_types,id'
            ]);

            DB::beginTransaction();

            // Create the organization type
            $organisationType = OrganisationType::create([
                'name' => $validatedData['name'],
                'slug' => Str::slug($validatedData['name'])
            ]);

            // If parent_id is provided, create the relationship using the children relationship
            if (!empty($validatedData['parent_id'])) {
                $parentType = OrganisationType::findOrFail($validatedData['parent_id']);
                $parentType->children()->attach($organisationType->id, [
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::commit();

            return redirect()->route('admin.organisation-types.manage')
                ->with('success', 'Organization type created successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error in storeOrgType:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating organization type: ' . $e->getMessage());
        }
    }

    public function edit(OrganisationType $organisationType)
    {
        return view('admin.organisation_types.edit', compact('organisationType'));
    }

    public function update(Request $request, OrganisationType $organisationType)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255'
            ]);

            DB::beginTransaction();

            // Update the organization type
            $organisationType->update([
                'name' => $validatedData['name'],
                'slug' => Str::slug($validatedData['name'])
            ]);

            DB::commit();

            return redirect()->route('admin.organisation-types.manage')
                ->with('success', 'Organization type updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating organization type: ' . $e->getMessage());
        }
    }

}
