<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Organisation\Species;
use Illuminate\Http\Request;

class SpeciesController extends Controller
{
    /**
     * Get all species
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $species = Species::orderBy('name')->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'species' => $species
            ]
        ], 200);
    }

    /**
     * Get a specific species
     *
     * @param Species $species
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Species $species)
    {
        $species->load(['speciesGender', 'maturity']);

        return response()->json([
            'status' => 'success',
            'data' => [
                'species' => $species
            ]
        ], 200);
    }

    /**
     * Search species by name
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = $request->input('query', '');

        $species = Species::with(['speciesGender', 'maturity'])
            ->where('name', 'like', "%{$query}%")
            ->orderBy('name')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'species' => $species
            ]
        ], 200);
    }
}
