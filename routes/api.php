<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
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
