<?php

namespace App\Http\Controllers;

use App\Models\Organisation;
use App\Models\OrganisationType;
use Illuminate\Http\Request;

class TestController extends Controller
{
    //

    public function index()
    {
        // Retrieve all top-level organisation types (those without parents)
        $organisationTypes = OrganisationType::whereDoesntHave('parents')->get();

        // Generate the hierarchical JSON structure for organisation types
        // For top-level organisation types, parentId and parentName are null
        $data = $this->generateOrganisationJson($organisationTypes, null, null);

        // Return the generated data as a JSON response
        return response()->json($data);
    }

    private function generateOrganisationJson($organisationTypes, $parentId, $parentName)
    {
        $jsonArray = []; // Initialize the array to hold the JSON structure

        // Iterate over each organisation type
        foreach ($organisationTypes as $organisationType) {
            // Prepare the array for the current organisation type with its id and name
            // The type itself does not have a parentId or parentName because it is the top level in this context
            $orgTypeArray = [
                'typeId' => $organisationType->id,
                'typeName' => $organisationType->name,
                'organisations' => [] // Initialize an array to hold organisations of this type
            ];

            // Iterate over each organisation under the current type
            foreach ($organisationType->organisations as $organisation) {
                // Prepare the array for the organisation with its details, including a reference back to the type
                $organisationArray = [
                    'id' => $organisation->id,
                    'name' => $organisation->name,
                    'organisationTypeId' => $organisationType->id,
                    'organisationTypeName' => $organisationType->name,
                    'parentId' => $organisation->organisation_id, // The organisation's parent, if any
                    'parentName' => $organisation->name, // The organisation's parent, if any
                    'children' => $this->getChildOrganisations($organisation->id,$organisation->name) // Populate with child organisations, if any
                ];

                // Add the prepared organisation array to the 'organisations' array of the current organisation type
                $orgTypeArray['organisations'][] = $organisationArray;
            }

            // Add the prepared organisation type array, including its organisations and their children, to the main JSON array
            $jsonArray[] = $orgTypeArray;
        }

        // Return the constructed hierarchical JSON array
        return $jsonArray;
    }

    private function getChildOrganisations($parentId,$parentName)
    {
        $childOrganisations = Organisation::where('organisation_id', $parentId)->get();
        $childrenArray = [];

        foreach ($childOrganisations as $child) {
            // Prepare the array for each child organisation, including its direct children by recursive call
            $childArray = [
                'id' => $child->id,
                'name' => $child->name,
                'parentId' => $parentId, // The organisation's parent, if any
                'parentName' => $parentName,// This child's parent is the organisation passed into this function
                'children' => $this->getChildOrganisations($child->id,$child->name) // Recursive call to get further descendants
            ];

            // Add the prepared child organisation array to the 'children' array
            $childrenArray[] = $childArray;
        }

        return $childrenArray;
    }



}
