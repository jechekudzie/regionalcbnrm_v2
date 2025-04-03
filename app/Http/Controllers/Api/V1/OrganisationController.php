<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Admin\Organisation;
use App\Models\Admin\OrganisationType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OrganisationController extends Controller
{
    /**
     * Get user's organisations
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserOrganisations(Request $request)
    {
        $user = $request->user();
        $organisations = $user->organisations()->with('organisationType')->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'organisations' => $organisations
            ]
        ], 200);
    }

    /**
     * Get organisation details
     *
     * @param Request $request
     * @param Organisation $organisation
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOrganisation(Request $request, Organisation $organisation)
    {
        // Check if user has access to this organisation
        $user = $request->user();
        $hasAccess = $user->organisations()->where('organisations.id', $organisation->id)->exists();

        if (!$hasAccess) {
            return response()->json([
                'status' => 'error',
                'message' => 'You do not have access to this organisation'
            ], 403);
        }

        // Load relevant relationships
        $organisation->load([
            'organisationType',
            'parentOrganisation',
            'childOrganisations'
        ]);

        return response()->json([
            'status' => 'success',
            'data' => [
                'organisation' => $organisation
            ]
        ], 200);
    }

    /**
     * Get organisation child organisations
     *
     * @param Request $request
     * @param Organisation $organisation
     * @return \Illuminate\Http\JsonResponse
     */
    public function getChildOrganisations(Request $request, Organisation $organisation)
    {
        // Check if user has access to this organisation
        $user = $request->user();
        $hasAccess = $user->organisations()->where('organisations.id', $organisation->id)->exists();

        if (!$hasAccess) {
            return response()->json([
                'status' => 'error',
                'message' => 'You do not have access to this organisation'
            ], 403);
        }

        $children = $organisation->childOrganisations()->with('organisationType')->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'children' => $children
            ]
        ], 200);
    }

    /**
     * Get organisation roles
     *
     * @param Request $request
     * @param Organisation $organisation
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOrganisationRoles(Request $request, Organisation $organisation)
    {
        // Check if user has access to this organisation
        $user = $request->user();
        $hasAccess = $user->organisations()->where('organisations.id', $organisation->id)->exists();

        if (!$hasAccess) {
            return response()->json([
                'status' => 'error',
                'message' => 'You do not have access to this organisation'
            ], 403);
        }

        $roles = $organisation->organisationRoles;

        return response()->json([
            'status' => 'success',
            'data' => [
                'roles' => $roles
            ]
        ], 200);
    }
}