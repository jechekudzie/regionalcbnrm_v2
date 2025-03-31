<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hunter;
use App\Models\HuntingDetailOutCome;
use App\Models\Organisation;
use App\Models\Admin\OrganisationType;
use App\Models\QuotaRequest;
use App\Models\Species;
use App\Models\User;
use App\Models\WardQuotaDistribution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class ApiController extends Controller
{
    /*
     |--------------------------------------------------------------------------
     | Organisation Management
     |--------------------------------------------------------------------------
     */
    private $generatedNumbers = [];

    public function fetchTemplate()
    {
        $organisations = OrganisationType::whereDoesntHave('parents')->get();
        $data = $this->formatTreeData($organisations);
        return response()->json($data);
    }

    private function formatTreeData($organisations)
    {
        $data = [];

        foreach ($organisations as $organisation) {

            $data[] = [
                'id' => $organisation->id,
                'text' => $organisation->name,
                'slug' => $organisation->slug,
                'children' => $this->formatTreeData($organisation->children),
            ];

        }
        return $data;
    }

    function generateUniqueNumber($min, $max)
    {
        $num = rand($min, $max);
        while (in_array($num, $this->generatedNumbers)) {
            $num = rand($min, $max);
        }
        $this->generatedNumbers[] = $num;
        return $num;
    }

    public function fetchOrganisationInstances()
    {
        $organisations = OrganisationType::whereDoesntHave('parents')->get();
        $data = [];

        foreach ($organisations as $organisation) {
            //random number
            $rand = $this->generateUniqueNumber(1, 1000000);

            $data[] = [
                'id' => $rand . '-ot-' . $organisation->id,
                'text' => $organisation->name,
                'type' => 'organisationType',
                'type_id' => $organisation->id,
                'slug' => $organisation->slug,
                'parentId' => null, // Set parent ID to null for top-level Organisation Types
                'parentName' => null,
                'children' => $this->formatOrganisationTreeData($organisation->organisations()->get()),
            ];
        }

        return response()->json($data);
    }

    private function formatOrganisationTreeData($entities, $parentOrganisationId = null, $parentOrganisationName = null)
    {
        $data = [];

        foreach ($entities as $entity) {
            //random number
            $rand = $this->generateUniqueNumber(1, 1000000);

            if ($entity instanceof Organisation) {

                $data[] = [
                    'id' => $rand . '-o-' . $entity->id,
                    'text' => $entity->name,
                    'type' => 'organisation',
                    'type_id' => $entity->organisation_type_id,
                    'slug' => $entity->slug,
                    'parentId' => $parentId = $entity->parentOrganisation ? $rand . '-o-' . $entity->parentOrganisation->id : null, // Set parent ID to organisation parent
                    'parentName' => $entity->parentOrganisation->name ?? null, //fetch using parentOrganisation method in Organisation model
                    // Process children OrganisationTypes, passing current Organisation as parent
                    'children' => $this->formatOrganisationTreeData($entity->organisationType->children, $rand . '-o-' . $entity->id, $entity->name),
                ];
            } else {

                $parts = explode('-', $parentOrganisationId);
                $organisation_id = $parts[2] ?? null;

                $data[] = [
                    'id' => $rand . '-ot-' . $entity->id,
                    'text' => $entity->name,
                    'type' => 'organisationType',
                    'type_id' => $entity->id,
                    'parentId' => $parentOrganisationId, // Inherit parent ID from the Organisation above
                    'parentName' => $parentOrganisationName, // Inherit parent name from the Organisation above
                    // Process child Organisations, maintaining current OrganisationType as parent
                    'children' => $this->formatOrganisationTreeData($entity->organisations()->where('organisation_id', $organisation_id)->get(), $rand . '-ot-' . $entity->id, $entity->name),
                ];
            }
        }
        return $data;
    }

    //fetchOrganisation via API
    public function fetchOrganisation(Organisation $organisation)
    {
        //return json response
        return response()->json($organisation);
    }

    //fetchOrganisationRoles via API
    public function fetchOrganisationRoles(Organisation $organisation)
    {
        return response()->json($organisation->roles);
    }

    public function getOrganisationChildren( $organisation)
    {
        $organisation = Organisation::find($organisation);
        // Get the first child organization type
        $childOrgType = $organisation->organisationType->children()->first();
        
        if ($childOrgType) {
            // Get organizations of this type that belong to the parent organization
            $children = $organisation->organisations()
                ->where('organisation_type_id', $childOrgType->id)
                ->orderBy('name')
                ->get()
                ->map(function($org) {
                    return [
                        'id' => $org->id,
                        'text' => $org->name,
                        'type' => 'organisation',
                        'type_id' => $org->organisation_type_id,
                        'parentId' => $org->organisation_id,
                        'parentName' => $org->parentOrganisation->name ?? null,
                        'children' => []
                    ];
                });

            return response()->json([
                'children' => $children,
                'parentType' => $organisation->organisationType->name,
                'nextType' => $childOrgType->name
            ]);
        }

        return response()->json([
            'children' => [],
            'parentType' => $organisation->organisationType->name,
            'nextType' => null
        ]);
    }

    public function getOrganisationsByType($typeId)
    {
        $organisations = Organisation::where('organisation_type_id', $typeId)
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

        return response()->json([
            'organisations' => $organisations
        ]);
    }

}
