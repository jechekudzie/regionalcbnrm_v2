<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConflictRecordController;
use App\Http\Controllers\ControlCaseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistoricalData\CropConflictRecordController;
use App\Http\Controllers\HistoricalData\HuntingRecordController;
use App\Http\Controllers\Admin\OrganisationsController;
use App\Http\Controllers\Admin\OrganisationRolesController;
use App\Http\Controllers\Admin\OrganisationTypeController;
use App\Http\Controllers\Admin\OrganisationUsersController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\HistoricalData\LiveStockConflictRecordController;
use App\Http\Controllers\HistoricalData\HumanConflictRecordController;
use App\Http\Controllers\HistoricalData\AnimalControlRecordController;
use App\Http\Controllers\HistoricalData\PoachingRecordController;
use App\Http\Controllers\HistoricalData\PoachersRecordController;
use App\Http\Controllers\HistoricalData\IncomeRecordController;
use App\Http\Controllers\HistoricalData\IncomeUseRecordController;
use App\Http\Controllers\HistoricalData\SourceOfIncomeRecordController;
use App\Http\Controllers\HistoricalData\IncomeBeneficiaryRecordController;
use App\Http\Controllers\HistoricalData\HumanResourceRecordController;
use App\Http\Controllers\Admin\SpeciesController;
use App\Http\Controllers\Organisation\HuntingConcessionController;
use App\Http\Controllers\Organisation\OrganisationDashboardController;
use App\Http\Controllers\Admin\OrganisationChildrenController;
use App\Http\Controllers\Admin\OrganisationPayableItemController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransactionPayableController;  
use App\Http\Controllers\Organisation\QuotaAllocationController;
use App\Models\Admin\Organisation;
use App\Http\Controllers\Organisation\HuntingActivityController;
use App\Http\Controllers\Organisation\WildlifeConflictIncidentController;
use App\Http\Controllers\Organisation\DynamicFieldController;
use App\Http\Controllers\Organisation\WildlifeConflictOutcomeController;


Route::get('/', function () {

    return view('auth.login');
});

Route::prefix('admin')->group(function () {
    // Admin dashboard routes
    Route::get('/', function () {
        return view('admin.index');
    })->name('admin.index');

    // Organisation Types Routes
    Route::get('organisation-types', [OrganisationTypeController::class, 'index'])->name('admin.organisation-types.index');
    Route::post('organisation-types/store', [OrganisationTypeController::class, 'store'])->name('admin.organisation-types.store');
    Route::post('organisation-types/{organisationType}', [OrganisationTypeController::class, 'organisationTypeOrganisation'])->name('admin.organisation-types.organisation-type');
    Route::get('/admin/organisation-types/manage', [OrganisationTypeController::class, 'manage'])->name('admin.organisation-types.manage');

    // Organization Types Routes - these are the only ones we should have
    Route::get('/admin/organisation-types/create/{parent?}', [OrganisationTypeController::class, 'createOrgType'])
        ->name('admin.organisation-types.create') ->where(['parent' => '[0-9]+']);
    // store organisation type
    Route::post('/admin/organisation-types', [OrganisationTypeController::class, 'storeOrgType'])->name('admin.organisation-types.store');
    // edit organisation type
    Route::get('/admin/organisation-types/{organisationType}/edit', [OrganisationTypeController::class, 'edit'])->name('admin.organisation-types.edit');
    Route::put('/admin/organisation-types/{organisationType}', [OrganisationTypeController::class, 'update'])->name('admin.organisation-types.update');
    Route::delete('/admin/organisation-types/{organisationType}', [OrganisationTypeController::class, 'destroy'])
        ->name('admin.organisation-types.destroy') ->where(['organisationType' => '[a-z0-9-]+']);

    // Organisations Routes
    Route::get('organisations', [OrganisationsController::class, 'index'])->name('admin.organisations.index');
    Route::post('organisations/store', [OrganisationsController::class, 'store'])->name('admin.organisations.store');
    Route::patch('organisations/{organisation}/update', [OrganisationsController::class, 'update'])->name('admin.organisations.update');
    Route::delete('organisations/{organisation}', [OrganisationsController::class, 'destroy'])->name('admin.organisations.destroy');
    Route::get('organisations/manage', [OrganisationsController::class, 'manageOrganisations'])->name('admin.organisations.manage');

    // routes for editing organisations
    Route::get('/admin/organisations/{organisation}/edit', [OrganisationsController::class, 'edit'])
        ->name('admin.organisations.edit');

    // Route for creating root-level organizations (no parent)
    Route::get('/admin/organisations/create/{type}', [OrganisationsController::class, 'createRoot'])
        ->name('admin.organisations.create-root')
        ->where(['type' => '[0-9]+']);

    // Route for creating organizations with a parent
    Route::get('/admin/organisations/{parent}/create/{type}', [OrganisationsController::class, 'createChild'])
        ->name('admin.organisations.create-child')
        ->where(['parent' => '[0-9]+', 'type' => '[0-9]+']);

    // Route for storing child organizations
    Route::post('/admin/organisations/store-child', [OrganisationsController::class, 'storeChild'])
        ->name('admin.organisations.store-child');


    //dynamic dropdowns
    Route::get('/organisations/hierarchy-test', [OrganisationsController::class, 'hierarchyTest'])
        ->name('admin.organisations.hierarchy-test');
    Route::get('/organisations/hierarchy-by-type', [OrganisationsController::class, 'hierarchyByType'])
        ->name('admin.organisations.hierarchy-by-type');


    // Organisation Roles Routes
    Route::get('organisation-roles/{organisation}', [OrganisationRolesController::class, 'index'])->name('admin.organisation-roles.index');
    Route::post('organisation-roles/{organisation}/store', [OrganisationRolesController::class, 'store'])->name('admin.organisation-roles.store');
    Route::get('organisation-roles/{role}/edit', [OrganisationRolesController::class, 'edit'])->name('admin.organisation-roles.edit');
    Route::patch('organisation-roles/{role}/update', [OrganisationRolesController::class, 'update'])->name('admin.organisation-roles.update');
    Route::delete('organisation-roles/{role}', [OrganisationRolesController::class, 'destroy'])->name('admin.organisation-roles.destroy');

    // Organisation Users Routes
    Route::get('organisation-users/{organisation}', [OrganisationUsersController::class, 'index'])->name('admin.organisation-users.index');
    Route::post('organisation-users/{organisation}/store', [OrganisationUsersController::class, 'store'])->name('admin.organisation-users.store');
    Route::patch('organisation-users/{user}/update', [OrganisationUsersController::class, 'update'])->name('admin.organisation-users.update');
    Route::delete('organisation-users/{user}/{organisation}', [OrganisationUsersController::class, 'destroy'])->name('admin.organisation-users.destroy');

    // Permissions Routes
    Route::prefix('permissions')->name('admin.')->group(function () {
        Route::group(['prefix' => 'permissions', 'as' => 'permissions.'], function () {
            Route::get('/', [PermissionController::class, 'index'])->name('index');
            Route::post('/store', [PermissionController::class, 'store'])->name('store');
            Route::get('/{permission}/edit', [PermissionController::class, 'edit'])->name('edit');
            Route::patch('/{permission}/update', [PermissionController::class, 'update'])->name('update');
            Route::delete('/{permission}', [PermissionController::class, 'destroy'])->name('destroy');
            Route::get('/{organisation}/{role}/assignPermission', [PermissionController::class, 'assignPermission'])->name('assign');
            Route::post('/{organisation}/{role}/assignPermissionToRole', [PermissionController::class, 'assignPermissionToRole'])->name('assign-permission-to-role');
        });
    });

    // Quota Allocation Routes
    Route::get('/{organisation}/quota-allocations', [QuotaAllocationController::class, 'index'])->name('admin.quota-allocations.index');
    Route::get('/{organisation}/quota-allocations/create', [QuotaAllocationController::class, 'create'])->name('admin.quota-allocations.create');
    Route::post('/{organisation}/quota-allocations', [QuotaAllocationController::class, 'store'])->name('admin.quota-allocations.store');
    Route::get('/{organisation}/quota-allocations/{quotaAllocation}', [QuotaAllocationController::class, 'show'])->name('admin.quota-allocations.show');
    Route::get('/{organisation}/quota-allocations/{quotaAllocation}/edit', [QuotaAllocationController::class, 'edit'])->name('admin.quota-allocations.edit');
    Route::patch('/{organisation}/quota-allocations/{quotaAllocation}', [QuotaAllocationController::class, 'update'])->name('admin.quota-allocations.update');
    Route::delete('/{organisation}/quota-allocations/{quotaAllocation}', [QuotaAllocationController::class, 'destroy'])->name('admin.quota-allocations.destroy');
});

Route::middleware('auth')->group(function () {

    //organisation dashboard
    Route::get('/organisation/dashboard/check/{organisation}', [OrganisationDashboardController::class, 'checkDashboardAccess'])->name('organisation.check-dashboard-access')->middleware('auth');
    Route::get('/organisation/dashboard', [OrganisationDashboardController::class, 'dashboard'])->name('organisation.dashboard')->middleware('auth');
    Route::get('/{organisation}/index', [OrganisationDashboardController::class, 'index'])->name('organisation.dashboard.index')->middleware('auth');


    Route::prefix('historical-data/{organisation:slug}')->group(function () {
        //main_dashboard
        Route::get('/main-dashboard', [DashboardController::class, 'index'])->name('main-dashboard');
        Route::get('/main-dashboard-2', [DashboardController::class, 'index2'])->name('main-dashboard-2');

        Route::get('/report-dashboard', [DashboardController::class, 'huntingDashboard'])->name('report-dashboard');
        Route::get('/hunting-dashboard/district', [DashboardController::class, 'huntingDashboardByDistrict'])->name('hunting-dashboard-district');
        Route::get('/hunting_dashboard/species', [DashboardController::class, 'huntingDashboardBySpecies'])->name('hunting-dashboard-species');

        //conflict dashboard
        Route::get('/conflict-dashboard', [DashboardController::class, 'conflictDashboard'])->name('conflict-dashboard');
        Route::get('/conflict-dashboard/district', [DashboardController::class, 'conflictDashboardByDistrict'])->name('conflict-dashboard-district');
        Route::get('/conflict-dashboard/species', [DashboardController::class, 'conflictDashboardBySpecies'])->name('conflict-dashboard-species');

        //control cases dashboard
        Route::get('/control-case_dashboard', [DashboardController::class, 'controlDashboard'])->name('control-cases-dashboard');
        Route::get('/control-cases/district', [DashboardController::class, 'controlDashboardByDistrict'])->name('control-dashboard-district');
        Route::get('/control-cases/species', [DashboardController::class, 'controlDashboardBySpecies'])->name('control-dashboard-species');

        //income records dashboard
        Route::get('/income-records-dashboard', [DashboardController::class, 'incomeRecordsDashboard'])->name('income-records-dashboard');
        Route::get('/income-records-dashboard/district', [DashboardController::class, 'incomeRecordsDashboardByDistrict'])->name('income-records-dashboard-district');
        Route::get('/income-records-dashboard/species', [DashboardController::class, 'incomeRecordsDashboardBySpecies'])->name('income-records-dashboard-species');

        //income dashboard bar chart
        Route::get('/income-records-dashboard/bar-chart', [DashboardController::class, 'incomeRecordDashboardBarChart'])->name('income-records-dashboard-bar-chart');

        //forms sections for reports
        Route::get('hunting-records', [\App\Http\Controllers\HistoricalData\HuntingRecordController::class, 'index'])->name('hunting_records.index');
        Route::get('hunting-records/create', [\App\Http\Controllers\HistoricalData\HuntingRecordController::class, 'create'])->name('hunting_records.create');
        Route::post('hunting-records', [\App\Http\Controllers\HistoricalData\HuntingRecordController::class, 'store'])->name('hunting_records.store');
        Route::get('hunting-records/{record}', [\App\Http\Controllers\HistoricalData\HuntingRecordController::class, 'show'])->name('hunting_records.show');
        Route::get('hunting-records/{record}/edit', [\App\Http\Controllers\HistoricalData\HuntingRecordController::class, 'edit'])->name('hunting_records.edit');
        Route::patch('hunting-records/{record}', [\App\Http\Controllers\HistoricalData\HuntingRecordController::class, 'update'])->name('hunting_records.update');
        Route::delete('hunting-records/{record}', [\App\Http\Controllers\HistoricalData\HuntingRecordController::class, 'destroy'])->name('hunting_records.destroy');

        //crop conflict records
        Route::get('/crop-conflict-records', [\App\Http\Controllers\HistoricalData\CropConflictRecordController::class, 'index'])->name('crop_conflict_records.index');
        Route::get('/crop-conflict-records/create', [\App\Http\Controllers\HistoricalData\CropConflictRecordController::class, 'create'])->name('crop_conflict_records.create');
        Route::post('/crop-conflict-records', [\App\Http\Controllers\HistoricalData\CropConflictRecordController::class, 'store'])->name('crop_conflict_records.store');
        Route::get('/crop-conflict-records/{cropConflict}', [\App\Http\Controllers\HistoricalData\CropConflictRecordController::class, 'show'])->name('crop_conflict_records.show');
        Route::get('/crop-conflict-records/{cropConflict}/edit', [\App\Http\Controllers\HistoricalData\CropConflictRecordController::class, 'edit'])->name('crop_conflict_records.edit');
        Route::patch('/crop-conflict-records/{cropConflict}', [\App\Http\Controllers\HistoricalData\CropConflictRecordController::class, 'update'])->name('crop_conflict_records.update');
        Route::delete('/crop-conflict-records/{cropConflict}', [\App\Http\Controllers\HistoricalData\CropConflictRecordController::class, 'destroy'])->name('crop_conflict_records.destroy');

        //livestock conflict records
        Route::get('/livestock-conflict-records', [\App\Http\Controllers\HistoricalData\LiveStockConflictRecordController::class, 'index'])->name('livestock_conflict_records.index');
        Route::get('/livestock-conflict-records/create', [\App\Http\Controllers\HistoricalData\LiveStockConflictRecordController::class, 'create'])->name('livestock_conflict_records.create');
        Route::post('/livestock-conflict-records', [\App\Http\Controllers\HistoricalData\LiveStockConflictRecordController::class, 'store'])->name('livestock_conflict_records.store');
        Route::get('/livestock-conflict-records/{liveStockConflict}', [\App\Http\Controllers\HistoricalData\LiveStockConflictRecordController::class, 'show'])->name('livestock_conflict_records.show');
        Route::get('/livestock-conflict-records/{liveStockConflict}/edit', [\App\Http\Controllers\HistoricalData\LiveStockConflictRecordController::class, 'edit'])->name('livestock_conflict_records.edit');
        Route::patch('/livestock-conflict-records/{liveStockConflict}', [\App\Http\Controllers\HistoricalData\LiveStockConflictRecordController::class, 'update'])->name('livestock_conflict_records.update');
        Route::delete('/livestock-conflict-records/{liveStockConflict}', [\App\Http\Controllers\HistoricalData\LiveStockConflictRecordController::class, 'destroy'])->name('livestock_conflict_records.destroy');

        //human conflict records
        Route::get('/human-conflict-records', [\App\Http\Controllers\HistoricalData\HumanConflictRecordController::class, 'index'])->name('human_conflict_records.index');
        Route::get('/human-conflict-records/create', [\App\Http\Controllers\HistoricalData\HumanConflictRecordController::class, 'create'])->name('human_conflict_records.create');
        Route::post('/human-conflict-records', [\App\Http\Controllers\HistoricalData\HumanConflictRecordController::class, 'store'])->name('human_conflict_records.store');
        Route::get('/human-conflict-records/{humanConflict}', [\App\Http\Controllers\HistoricalData\HumanConflictRecordController::class, 'show'])->name('human_conflict_records.show');
        Route::get('/human-conflict-records/{humanConflict}/edit', [\App\Http\Controllers\HistoricalData\HumanConflictRecordController::class, 'edit'])->name('human_conflict_records.edit');
        Route::patch('/human-conflict-records/{humanConflict}', [\App\Http\Controllers\HistoricalData\HumanConflictRecordController::class, 'update'])->name('human_conflict_records.update');
        Route::delete('/human-conflict-records/{humanConflict}', [\App\Http\Controllers\HistoricalData\HumanConflictRecordController::class, 'destroy'])->name('human_conflict_records.destroy');

        //animal control records
        Route::get('/animal-control-records', [\App\Http\Controllers\HistoricalData\AnimalControlRecordController::class, 'index'])->name('animal_control_records.index');
        Route::get('/animal-control-records/create', [\App\Http\Controllers\HistoricalData\AnimalControlRecordController::class, 'create'])->name('animal_control_records.create');
        Route::post('/animal-control-records', [\App\Http\Controllers\HistoricalData\AnimalControlRecordController::class, 'store'])->name('animal_control_records.store');
        Route::get('/animal-control-records/{animalControl}', [\App\Http\Controllers\HistoricalData\AnimalControlRecordController::class, 'show'])->name('animal_control_records.show');
        Route::get('/animal-control-records/{animalControl}/edit', [\App\Http\Controllers\HistoricalData\AnimalControlRecordController::class, 'edit'])->name('animal_control_records.edit');
        Route::patch('/animal-control-records/{animalControl}', [\App\Http\Controllers\HistoricalData\AnimalControlRecordController::class, 'update'])->name('animal_control_records.update');
        Route::delete('/animal-control-records/{animalControl}', [\App\Http\Controllers\HistoricalData\AnimalControlRecordController::class, 'destroy'])->name('animal_control_records.destroy');

        //poaching records
        Route::get('/poaching-records', [\App\Http\Controllers\HistoricalData\PoachingRecordController::class, 'index'])->name('poaching_records.index');
        Route::get('/poaching-records/create', [\App\Http\Controllers\HistoricalData\PoachingRecordController::class, 'create'])->name('poaching_records.create');
        Route::post('/poaching-records', [\App\Http\Controllers\HistoricalData\PoachingRecordController::class, 'store'])->name('poaching_records.store');
        Route::get('/poaching-records/{poachingRecord}', [\App\Http\Controllers\HistoricalData\PoachingRecordController::class, 'show'])->name('poaching_records.show');
        Route::get('/poaching-records/{poachingRecord}/edit', [\App\Http\Controllers\HistoricalData\PoachingRecordController::class, 'edit'])->name('poaching_records.edit');
        Route::patch('/poaching-records/{poachingRecord}', [\App\Http\Controllers\HistoricalData\PoachingRecordController::class, 'update'])->name('poaching_records.update');
        Route::delete('/poaching-records/{poachingRecord}', [\App\Http\Controllers\HistoricalData\PoachingRecordController::class, 'destroy'])->name('poaching_records.destroy');

        //poachers records
        Route::get('/poachers-records', [\App\Http\Controllers\HistoricalData\PoachersRecordController::class, 'index'])->name('poachers_records.index');
        Route::get('/poachers-records/create', [\App\Http\Controllers\HistoricalData\PoachersRecordController::class, 'create'])->name('poachers_records.create');
        Route::post('/poachers-records', [\App\Http\Controllers\HistoricalData\PoachersRecordController::class, 'store'])->name('poachers_records.store');
        Route::get('/poachers-records/{poachersRecord}', [\App\Http\Controllers\HistoricalData\PoachersRecordController::class, 'show'])->name('poachers_records.show');
        Route::get('/poachers-records/{poachersRecord}/edit', [\App\Http\Controllers\HistoricalData\PoachersRecordController::class, 'edit'])->name('poachers_records.edit');
        Route::patch('/poachers-records/{poachersRecord}', [\App\Http\Controllers\HistoricalData\PoachersRecordController::class, 'update'])->name('poachers_records.update');
        Route::delete('/poachers-records/{poachersRecord}', [\App\Http\Controllers\HistoricalData\PoachersRecordController::class, 'destroy'])->name('poachers_records.destroy');

        //income records
        Route::resource('income-records', \App\Http\Controllers\HistoricalData\IncomeRecordController::class)->names([
            'index' => 'income_records.index',
            'create' => 'income_records.create',
            'store' => 'income_records.store',
            'show' => 'income_records.show',
            'edit' => 'income_records.edit',
            'update' => 'income_records.update',
            'destroy' => 'income_records.destroy',
        ]);

        //income use records
        Route::resource('income-use-records', \App\Http\Controllers\HistoricalData\IncomeUseRecordController::class)->names([
            'index' => 'income_use_records.index',
            'create' => 'income_use_records.create',
            'store' => 'income_use_records.store',
            'show' => 'income_use_records.show',
            'edit' => 'income_use_records.edit',
            'update' => 'income_use_records.update',
            'destroy' => 'income_use_records.destroy',
        ]);

        //source of income records
        Route::resource('source-of-income-records', \App\Http\Controllers\HistoricalData\SourceOfIncomeRecordController::class)->names([
            'index' => 'source_of_income_records.index',
            'create' => 'source_of_income_records.create',
            'store' => 'source_of_income_records.store',
            'show' => 'source_of_income_records.show',
            'edit' => 'source_of_income_records.edit',
            'update' => 'source_of_income_records.update',
            'destroy' => 'source_of_income_records.destroy',
        ]);

        //income beneficiary records
        Route::resource('income-beneficiary-records', \App\Http\Controllers\HistoricalData\IncomeBeneficiaryRecordController::class)->names([
            'index' => 'income_beneficiary_records.index',
            'create' => 'income_beneficiary_records.create',
            'store' => 'income_beneficiary_records.store',
            'show' => 'income_beneficiary_records.show',
            'edit' => 'income_beneficiary_records.edit',
            'update' => 'income_beneficiary_records.update',
            'destroy' => 'income_beneficiary_records.destroy',
        ]);

        //human resource records
        Route::resource('human-resource-records', \App\Http\Controllers\HistoricalData\HumanResourceRecordController::class)->names([
            'index' => 'human-resource-records.index',
            'create' => 'human-resource-records.create',
            'store' => 'human-resource-records.store',
            'show' => 'human-resource-records.show',
            'edit' => 'human-resource-records.edit',
            'update' => 'human-resource-records.update',
            'destroy' => 'human-resource-records.destroy',
        ]);

    });


    //organisation dashboard
    Route::get('/organisation/dashboard/check/{organisation}', [OrganisationDashboardController::class, 'checkDashboardAccess'])->name('organisation.check-dashboard-access')->middleware('auth');
    Route::get('/organisation/dashboard', [OrganisationDashboardController::class, 'dashboard'])->name('organisation.dashboard')->middleware('auth');
    Route::get('/{organisation}/index', [OrganisationDashboardController::class, 'index'])->name('organisation.dashboard.index')->middleware('auth');

    Route::get('/rural-district-councils', [OrganisationDashboardController::class, 'ruralDistrictCouncils'])->name('organisation.dashboard.rural-district-councils')->middleware('auth');

    // Species Routes
    Route::get('/{organisation}/species', [SpeciesController::class, 'index'])->name('species.index');
    Route::get('/{organisation}/species/create', [SpeciesController::class, 'create'])->name('species.create');
    Route::post('/{organisation}/species', [SpeciesController::class, 'store'])->name('species.store');
    Route::get('/{organisation}/species/{species}', [SpeciesController::class, 'show'])->name('species.show');
    Route::get('/{organisation}/species/{species}/edit', [SpeciesController::class, 'edit'])->name('species.edit');
    Route::patch('/{organisation}/species/{species}', [SpeciesController::class, 'update'])->name('species.update');
    Route::delete('/{organisation}/species/{species}', [SpeciesController::class, 'destroy'])->name('species.destroy');


    //hunting concession routes
    Route::get('/{organisation}/hunting-concessions', [HuntingConcessionController::class, 'index'])->name('organisation.hunting-concessions.index');
    Route::get('/{organisation}/hunting-concessions/create', [HuntingConcessionController::class, 'create'])->name('organisation.hunting-concessions.create');
    Route::post('/{organisation}/hunting-concessions/store', [HuntingConcessionController::class, 'store'])->name('organisation.hunting-concessions.store');
    Route::get('/{organisation}/hunting-concessions/{huntingConcession}', [HuntingConcessionController::class, 'show'])->name('organisation.hunting-concessions.show');
    Route::get('/{organisation}/hunting-concessions/{huntingConcession}/edit', [HuntingConcessionController::class, 'edit'])->name('organisation.hunting-concessions.edit');
    Route::patch('/{organisation}/hunting-concessions/{huntingConcession}/update', [HuntingConcessionController::class, 'update'])->name('organisation.hunting-concessions.update');
    Route::delete('/{organisation}/hunting-concessions/{huntingConcession}', [HuntingConcessionController::class, 'destroy'])->name('organisation.hunting-concessions.destroy');

    // Hunting Activities Routes
    Route::get('/{organisation}/hunting-activities', [HuntingActivityController::class, 'index'])->name('organisation.hunting-activities.index');
    Route::get('/{organisation}/hunting-activities/create', [HuntingActivityController::class, 'create'])->name('organisation.hunting-activities.create');
    Route::post('/{organisation}/hunting-activities', [HuntingActivityController::class, 'store'])->name('organisation.hunting-activities.store');
    Route::get('/{organisation}/hunting-activities/{huntingActivity}', [HuntingActivityController::class, 'show'])->name('organisation.hunting-activities.show');
    Route::get('/{organisation}/hunting-activities/{huntingActivity}/edit', [HuntingActivityController::class, 'edit'])->name('organisation.hunting-activities.edit');
    Route::patch('/{organisation}/hunting-activities/{huntingActivity}', [HuntingActivityController::class, 'update'])->name('organisation.hunting-activities.update');
    Route::delete('/{organisation}/hunting-activities/{huntingActivity}', [HuntingActivityController::class, 'destroy'])->name('organisation.hunting-activities.destroy');

    //Quota Allocation Routes
    Route::get('/{organisation}/quota-allocations', [QuotaAllocationController::class, 'index'])->name('organisation.quota-allocations.index');
    Route::get('/{organisation}/quota-allocations/create', [QuotaAllocationController::class, 'create'])->name('organisation.quota-allocations.create');
    Route::post('/{organisation}/quota-allocations', [QuotaAllocationController::class, 'store'])->name('organisation.quota-allocations.store');
    Route::get('/{organisation}/quota-allocations/{quotaAllocation}', [QuotaAllocationController::class, 'show'])->name('organisation.quota-allocations.show');
    Route::get('/{organisation}/quota-allocations/{quotaAllocation}/edit', [QuotaAllocationController::class, 'edit'])->name('organisation.quota-allocations.edit');
    Route::patch('/{organisation}/quota-allocations/{quotaAllocation}', [QuotaAllocationController::class, 'update'])->name('organisation.quota-allocations.update');
    Route::delete('/{organisation}/quota-allocations/{quotaAllocation}', [QuotaAllocationController::class, 'destroy'])->name('organisation.quota-allocations.destroy');

    // Wildlife Conflict Incidents Routes
    Route::get('/{organisation}/wildlife-conflicts', [WildlifeConflictIncidentController::class, 'index'])->name('organisation.wildlife-conflicts.index');
    Route::get('/{organisation}/wildlife-conflicts/create', [WildlifeConflictIncidentController::class, 'create'])->name('organisation.wildlife-conflicts.create');
    Route::post('/{organisation}/wildlife-conflicts', [WildlifeConflictIncidentController::class, 'store'])->name('organisation.wildlife-conflicts.store');
    Route::get('/{organisation}/wildlife-conflicts/{wildlifeConflictIncident}', [WildlifeConflictIncidentController::class, 'show'])->name('organisation.wildlife-conflicts.show');
    Route::get('/{organisation}/wildlife-conflicts/{wildlifeConflictIncident}/edit', [WildlifeConflictIncidentController::class, 'edit'])->name('organisation.wildlife-conflicts.edit');
    Route::patch('/{organisation}/wildlife-conflicts/{wildlifeConflictIncident}', [WildlifeConflictIncidentController::class, 'update'])->name('organisation.wildlife-conflicts.update');
    Route::delete('/{organisation}/wildlife-conflicts/{wildlifeConflictIncident}', [WildlifeConflictIncidentController::class, 'destroy'])->name('organisation.wildlife-conflicts.destroy');
    
    // Poaching Incidents Routes
    Route::get('/{organisation}/poaching-incidents', [\App\Http\Controllers\Organisation\PoachingIncidentController::class, 'index'])->name('organisation.poaching-incidents.index');
    Route::get('/{organisation}/poaching-incidents/create', [\App\Http\Controllers\Organisation\PoachingIncidentController::class, 'create'])->name('organisation.poaching-incidents.create');
    Route::post('/{organisation}/poaching-incidents', [\App\Http\Controllers\Organisation\PoachingIncidentController::class, 'store'])->name('organisation.poaching-incidents.store');
    Route::get('/{organisation}/poaching-incidents/{poachingIncident}', [\App\Http\Controllers\Organisation\PoachingIncidentController::class, 'show'])->name('organisation.poaching-incidents.show');
    Route::get('/{organisation}/poaching-incidents/{poachingIncident}/edit', [\App\Http\Controllers\Organisation\PoachingIncidentController::class, 'edit'])->name('organisation.poaching-incidents.edit');
    Route::patch('/{organisation}/poaching-incidents/{poachingIncident}', [\App\Http\Controllers\Organisation\PoachingIncidentController::class, 'update'])->name('organisation.poaching-incidents.update');
    Route::delete('/{organisation}/poaching-incidents/{poachingIncident}', [\App\Http\Controllers\Organisation\PoachingIncidentController::class, 'destroy'])->name('organisation.poaching-incidents.destroy');
    
    //organisation create
    Route::get('/{organisation}/organisations/{organisationType}/{parentOrganisation}/index', [OrganisationChildrenController::class, 'index'])->name('organisation.organisations.index');
    Route::post('/{organisation}/organisations/{organisationType}/{parentOrganisation}/store', [OrganisationChildrenController::class, 'store'])->name('organisation.organisations.store');
    Route::get('/{organisation}/organisations/{organisationType}/edit', [OrganisationChildrenController::class, 'edit'])->name('organisation.organisations.edit');
    Route::patch('/{organisation}/organisations/{organisationToUpdate}/update', [OrganisationChildrenController::class, 'update'])->name('organisation.organisations.update');
    Route::delete('/{organisation}/organisations/{organisationToDelete}', [OrganisationChildrenController::class, 'destroy'])->name('organisation.organisations.destroy');

    /*
    |--------------------------------------------------------------------------
    | Organisation Dashboard Payments
    |--------------------------------------------------------------------------
    */

    //organisation categories
    Route::get('/{organisation}/payable-categories', [OrganisationPayableItemController::class, 'payableItemCategories'])->name('organisation.payable-categories.index');
    //organisation payable items
    Route::get('/{organisation}/{category}/payable-items', [OrganisationPayableItemController::class, 'index'])->name('organisation.payable-items.index');
    Route::get('/{organisation}/{category}/payable-items/create', [OrganisationPayableItemController::class, 'create'])->name('organisation.payable-items.create');
    Route::post('/{organisation}/{category}/payable-items/store', [OrganisationPayableItemController::class, 'store'])->name('organisation.payable-items.store');
    Route::get('/{organisation}/{category}/payable-items/{payableItem}/edit', [OrganisationPayableItemController::class, 'edit'])->name('organisation.payable-items.edit');
    Route::patch('/{organisation}/{category}/payable-items/{payableItem}/update', [OrganisationPayableItemController::class, 'update'])->name('organisation.payable-items.update');
    Route::delete('/{organisation}/{category}/payable-items/{payableItem}', [OrganisationPayableItemController::class, 'destroy'])->name('organisation.payable-items.destroy');

    //organisation transactions
    Route::get('/{organisation}/transactions', [\App\Http\Controllers\TransactionController::class, 'index'])->name('organisation.transactions.index');
    Route::get('/{organisation}/transactions/create', [\App\Http\Controllers\TransactionController::class, 'create'])->name('organisation.transactions.create');
    Route::post('/{organisation}/transactions/store', [\App\Http\Controllers\TransactionController::class, 'store'])->name('organisation.transactions.store');
    Route::get('/{organisation}/transactions/{transaction}/edit', [\App\Http\Controllers\TransactionController::class, 'edit'])->name('organisation.transactions.edit');
    Route::patch('/{organisation}/transactions/{transaction}/update', [\App\Http\Controllers\TransactionController::class, 'update'])->name('organisation.transactions.update');
    Route::delete('/{organisation}/transactions/{transaction}', [\App\Http\Controllers\TransactionController::class, 'destroy'])->name('organisation.transactions.destroy');

    //transaction payables
    Route::get('/{organisation}/transactions/{transaction}/payables', [\App\Http\Controllers\TransactionPayableController::class, 'index'])->name('organisation.transaction-payables.index');
    Route::post('/{organisation}/transactions/{transaction}/payables/store', [\App\Http\Controllers\TransactionPayableController::class, 'store'])->name('organisation.transaction-payables.store');
    Route::get('/{organisation}/transactions/{transaction}/payables/{transactionPayable}/edit', [\App\Http\Controllers\TransactionPayableController::class, 'edit'])->name('organisation.transaction-payables.edit');
    Route::patch('/{organisation}/transactions/{transaction}/payables/{transactionPayable}/update', [\App\Http\Controllers\TransactionPayableController::class, 'update'])->name('organisation.transaction-payables.update');
    Route::delete('/{organisation}/transactions/{transaction}/payables/{transactionPayable}', [\App\Http\Controllers\TransactionPayableController::class, 'destroy'])->name('organisation.transaction-payables.destroy');

    // Organisation Wildlife Conflicts Routes
    Route::prefix('organisation/{organisation:slug}')->name('organisation.')->group(function () {
        // Dynamic Fields Management
        Route::get('dynamic-fields', [DynamicFieldController::class, 'index'])->name('dynamic-fields.index');
        Route::get('dynamic-fields/create', [DynamicFieldController::class, 'create'])->name('dynamic-fields.create');
        Route::post('dynamic-fields', [DynamicFieldController::class, 'store'])->name('dynamic-fields.store');
        Route::get('dynamic-fields/{dynamicField}', [DynamicFieldController::class, 'show'])->name('dynamic-fields.show');
        Route::get('dynamic-fields/{dynamicField}/edit', [DynamicFieldController::class, 'edit'])->name('dynamic-fields.edit');
        Route::put('dynamic-fields/{dynamicField}', [DynamicFieldController::class, 'update'])->name('dynamic-fields.update');
        Route::delete('dynamic-fields/{dynamicField}', [DynamicFieldController::class, 'destroy'])->name('dynamic-fields.destroy');
        
        // Wildlife Conflict Outcomes
        Route::get('wildlife-conflicts/{wildlifeConflictIncident}/outcomes/create', [\App\Http\Controllers\Organisation\WildlifeConflictOutcomeController::class, 'create'])->name('wildlife-conflicts.outcomes.create');
        Route::post('wildlife-conflicts/{wildlifeConflictIncident}/outcomes', [\App\Http\Controllers\Organisation\WildlifeConflictOutcomeController::class, 'store'])->name('wildlife-conflicts.outcomes.store');
        Route::get('wildlife-conflicts/{wildlifeConflictIncident}/outcomes/{outcome}', [\App\Http\Controllers\Organisation\WildlifeConflictOutcomeController::class, 'show'])->name('wildlife-conflicts.outcomes.show');
        Route::get('wildlife-conflicts/{wildlifeConflictIncident}/outcomes/{outcome}/edit', [\App\Http\Controllers\Organisation\WildlifeConflictOutcomeController::class, 'edit'])->name('wildlife-conflicts.outcomes.edit');
        Route::patch('wildlife-conflicts/{wildlifeConflictIncident}/outcomes/{outcome}', [\App\Http\Controllers\Organisation\WildlifeConflictOutcomeController::class, 'update'])->name('wildlife-conflicts.outcomes.update');
        Route::delete('wildlife-conflicts/{wildlifeConflictIncident}/outcomes/{outcome}', [\App\Http\Controllers\Organisation\WildlifeConflictOutcomeController::class, 'destroy'])->name('wildlife-conflicts.outcomes.destroy');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Temporary debug route
    Route::get('/debug/dynamic-fields/{outcomeId}', function($outcomeId) {
        $controller = new \App\Http\Controllers\Api\ConflictOutcomeController();
        return $controller->getDynamicFields($outcomeId);
    });
});


require __DIR__ . '/auth.php';
