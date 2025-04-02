<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Organisation\QuotaAllocationController;
use App\Http\Controllers\Api\ConflictOutcomeController;
use App\Models\Admin\City;

//organisation types
Route::get('/admin/organisation-types', [ApiController::class, 'fetchTemplate'])->name('admin.organisation-types.index');
//organisations
Route::get('/admin/organisations', [ApiController::class, 'fetchOrganisationInstances'])->name('admin.organisations.index');
//organisation
Route::get('/admin/organisations/{organisation}/edit', [ApiController::class, 'fetchOrganisation'])->name('admin.organisations.edit');

// Add this new route for getting children
Route::get('/admin/organisations/get-children/{organisation}', [ApiController::class, 'getOrganisationChildren'])
    ->name('admin.organisations.get-children');

Route::get('/admin/organisations/by-type/{typeId}', [ApiController::class, 'getOrganisationsByType'])
    ->name('admin.organisations.by-type');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

// Quota allocation routes

Route::get('/organisations/{organisation}/quota-allocations', [QuotaAllocationController::class, 'getQuotaAllocation']);

// Conflict Outcome Dynamic Fields
Route::get('/conflict-outcomes/{conflictOutcome}/dynamic-fields', [ConflictOutcomeController::class, 'getDynamicFields']);

// Get cities by province
Route::get('/provinces/{province}/cities', function ($province) {
    return City::where('province_id', $province)->get();
});
