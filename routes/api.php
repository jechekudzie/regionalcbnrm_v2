<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Organisation\QuotaAllocationController;
use App\Http\Controllers\Api\ConflictOutcomeController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\OrganisationController;
use App\Http\Controllers\Api\V1\WildlifeConflictController;
use App\Http\Controllers\Api\V1\ProblemAnimalControlController;
use App\Http\Controllers\Api\V1\PoachingController;
use App\Http\Controllers\Api\V1\HuntingController;
use App\Http\Controllers\Api\V1\SpeciesController;
use App\Models\Admin\City;

// Legacy API routes
Route::get('/admin/organisation-types', [ApiController::class, 'fetchTemplate'])->name('admin.organisation-types.index');
Route::get('/admin/organisations', [ApiController::class, 'fetchOrganisationInstances'])->name('admin.organisations.index');
Route::get('/admin/organisations/{organisation}/edit', [ApiController::class, 'fetchOrganisation'])->name('admin.organisations.edit');
Route::get('/admin/organisations/get-children/{organisation}', [ApiController::class, 'getOrganisationChildren'])
    ->name('admin.organisations.get-children');
Route::get('/admin/organisations/by-type/{typeId}', [ApiController::class, 'getOrganisationsByType'])
    ->name('admin.organisations.by-type');
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');
Route::get('/organisations/{organisation}/quota-allocations', [QuotaAllocationController::class, 'getQuotaAllocation']);
Route::get('/conflict-outcomes/{conflictOutcome}/dynamic-fields', [ConflictOutcomeController::class, 'getDynamicFields']);
Route::get('/provinces/{province}/cities', function ($province) {
    return City::where('province_id', $province)->get();
});

// API v1 Routes
Route::prefix('v1')->group(function () {
    // Public routes
    Route::post('/login', [AuthController::class, 'login']);

    // Protected routes
    Route::middleware('auth:api')->group(function () {
        // Auth routes
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);

        // Organisation routes
        Route::get('/organisations', [OrganisationController::class, 'getUserOrganisations']);
        Route::get('/organisations/{organisation}', [OrganisationController::class, 'getOrganisation']);
        Route::get('/organisations/{organisation}/children', [OrganisationController::class, 'getChildOrganisations']);
        Route::get('/organisations/{organisation}/roles', [OrganisationController::class, 'getOrganisationRoles']);

        // Species routes
        Route::get('/species', [SpeciesController::class, 'index']);
        Route::get('/species/search', [SpeciesController::class, 'search']);
        Route::get('/species/{species}', [SpeciesController::class, 'show']);

        // Wildlife Conflict routes
        Route::get('/organisations/{id}/wildlife-conflicts', [WildlifeConflictController::class, 'getIncidents']);
        Route::get('/wildlife-conflicts/{incident}', [WildlifeConflictController::class, 'getIncident']);
        Route::post('/wildlife-conflicts', [WildlifeConflictController::class, 'createIncident']);
        Route::put('/wildlife-conflicts/{incident}', [WildlifeConflictController::class, 'updateIncident']);
        Route::get('/conflict-types', [WildlifeConflictController::class, 'getConflictTypes']);
        Route::get('/{id}/conflict-outcomes', [WildlifeConflictController::class, 'getConflictOutcomes']);
        Route::post('/wildlife-conflicts/{incident}/outcomes', [WildlifeConflictController::class, 'addOutcome']);

        // Problem Animal Control routes
        Route::get('/organisations/{id}/problem-animal-controls', [ProblemAnimalControlController::class, 'getRecords']);
        Route::get('/problem-animal-controls/{record}', [ProblemAnimalControlController::class, 'getRecord']);
        Route::post('/problem-animal-controls', [ProblemAnimalControlController::class, 'createRecord']);
        Route::put('/problem-animal-controls/{record}', [ProblemAnimalControlController::class, 'updateRecord']);
        Route::get('/control-measures', [ProblemAnimalControlController::class, 'getControlMeasures']);

        // Poaching routes
        Route::get('/organisations/{id}/poaching-incidents', [PoachingController::class, 'getIncidents']);
        Route::get('/poaching-incidents/{incident}', [PoachingController::class, 'getIncident']);
        Route::post('/poaching-incidents', [PoachingController::class, 'createIncident']);
        Route::put('/poaching-incidents/{incident}', [PoachingController::class, 'updateIncident']);
        Route::get('/poaching-methods', [PoachingController::class, 'getPoachingMethods']);
        Route::get('/poaching-reasons', [PoachingController::class, 'getPoachingReasons']);
        Route::post('/poaching-incidents/{incident}/poachers', [PoachingController::class, 'addPoacher']);

        // Hunting routes
        Route::get('/organisations/{organisation}/hunting-activities', [HuntingController::class, 'getActivities']);
        Route::get('/hunting-activities/{activity}', [HuntingController::class, 'getActivity']);
        Route::post('/hunting-activities', [HuntingController::class, 'createActivity']);
        Route::get('/organisations/{organisation}/hunting-concessions', [HuntingController::class, 'getConcessions']);
        Route::get('/organisations/{organisation}/quota-allocations', [HuntingController::class, 'getQuotaAllocations']);
    });
});
