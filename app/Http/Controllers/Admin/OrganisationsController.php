<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Organisation;
use App\Models\Admin\OrganisationType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrganisationsController extends Controller
{

    public function index()
    {
        return view('admin.organisations.index');
    }

    //store organisation of organisation
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'parent_id' => 'nullable',
                'organisation_type_id' => 'required|exists:organisation_types,id',
            ]);

            // If parent_id contains a dash (from the tree structure), extract the actual ID
            if (is_string($validatedData['parent_id']) && str_contains($validatedData['parent_id'], '-')) {
                $parts = explode('-', $validatedData['parent_id']);
                $parent_id = isset($parts[2]) ? $parts[2] : null;
            } else {
                $parent_id = $validatedData['parent_id'];
            }

            // Create the organisation
            $organisation = Organisation::create([
                'name' => $validatedData['name'],
                'organisation_type_id' => $validatedData['organisation_type_id'],
                'organisation_id' => $parent_id,
                'slug' => \Illuminate\Support\Str::slug($validatedData['name'])
            ]);

            // Create admin role for the new organisation
            $organisation->organisationRoles()->create([
                'name' => 'admin',
                'guard_name' => 'web',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Organisation created successfully'
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
    }

    public function update(Request $request, Organisation $organisation)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
            ]);

            // Add detailed logging before update
            Log::info('Attempting to update organisation', [
                'id' => $organisation->id,
                'old_name' => $organisation->name,
                'new_name' => $validatedData['name'],
                'request_data' => $request->all(),
                'validated_data' => $validatedData
            ]);

            // Try the update and store result
            $updated = $organisation->update([
                'name' => $validatedData['name'],
                'slug' => \Illuminate\Support\Str::slug($validatedData['name'])
            ]);

            // Log the result
            Log::info('Update result', [
                'success' => $updated,
                'new_organisation_data' => $organisation->fresh()->toArray()
            ]);

            if (!$updated) {
                throw new \Exception('Failed to update organisation. No error provided.');
            }

            return redirect()->route('admin.organisations.manage')
                ->with('success', 'Updated ' . $organisation->organisationType->name . ' successfully');
            
        } catch (\Exception $e) {
            Log::error('Error updating organisation', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'organisation_id' => $organisation->id,
                'request_data' => $request->all()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating ' . $organisation->organisationType->name . ': ' . $e->getMessage());
        }
    }

    public function destroy(Organisation $organisation)
    {
        $organisation->delete();
        return redirect()->route('admin.organisations.index')->with('success', 'Organisation deleted successfully');
    }

    public function manageOrganisations()
    {
        //all organisations sort by organisation type
        $organisations = Organisation::all()->sortBy('organisation_type_id');

        //dd($organisations);
        return view('admin.organisations.manage', compact('organisations'));

    }

    public function hierarchyTest()
    {
        // Get all Rural District Councils
        $rdcs = Organisation::whereHas('organisationType', function($query) {
                $query->where('name', 'Rural District Council');
            })
            ->with('organisationType')
            ->orderBy('name')
            ->get()
            ->map(function($org) {
                return [
                    'id' => $org->id,
                    'text' => $org->name,
                    'type' => $org->organisationType->name,
                    'type_id' => $org->organisation_type_id,
                    'has_children' => $org->organisationType->children()->exists()
                ];
            });

        return view('admin.organisations.hierarchy-test', [
            'initialData' => $rdcs
        ]);
    }


    public function hierarchyByType()
    {
        // Get all organization types
        $orgTypes = OrganisationType::orderBy('name')
            ->get()
            ->map(function($type) {
                return [
                    'id' => $type->id,
                    'text' => $type->name
                ];
            });

        return view('admin.organisations.hierarchy-by-type', [
            'orgTypes' => $orgTypes
        ]);
    }

    public function createRoot($type)
    {
        $orgType = OrganisationType::findOrFail($type);
        
        return view('admin.organisations.create-child', [
            'parent' => null,
            'type' => $orgType,
            'isSameLevel' => true,
            'parentName' => 'Root Level'
        ]);
    }

    public function createChild($parent, $type)
    {
        $parentOrg = Organisation::findOrFail($parent);
        $orgType = OrganisationType::findOrFail($type);
        
        // If creating at same level, use the parent's parent
        if ($orgType->id == $parentOrg->organisation_type_id) {
            return view('admin.organisations.create-child', [
                'parent' => $parentOrg->parentOrganisation,
                'type' => $orgType,
                'isSameLevel' => true,
                'parentName' => $parentOrg->parentOrganisation ? $parentOrg->parentOrganisation->name : 'Root Level'
            ]);
        }
        
        // If creating child level, use the current org as parent
        return view('admin.organisations.create-child', [
            'parent' => $parentOrg,
            'type' => $orgType,
            'isSameLevel' => false,
            'parentName' => $parentOrg->name
        ]);
    }

    public function storeChild(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'parent_id' => 'nullable',
                'organisation_type_id' => 'required|exists:organisation_types,id',
            ]);

            // Create the organisation
            $organisation = Organisation::create([
                'name' => $validatedData['name'],
                'organisation_type_id' => $validatedData['organisation_type_id'],
                'organisation_id' => $validatedData['parent_id'],
                'slug' => \Illuminate\Support\Str::slug($validatedData['name'])
            ]);

            // Create admin role for the new organisation
            $organisation->organisationRoles()->create([
                'name' => 'admin',
                'guard_name' => 'web',
            ]);

            return redirect()->route('admin.organisations.manage')
                ->with('success', 'Organisation created successfully');
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating organisation: ' . $e->getMessage());
        }
    }

    public function edit(Organisation $organisation)
    {
        return view('admin.organisations.edit', compact('organisation'));
    }

}
