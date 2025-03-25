<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConflictRecordController;
use App\Http\Controllers\ControlCaseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistoricalData\CropConflictRecordController;
use App\Http\Controllers\HistoricalData\HuntingRecordController;
use App\Http\Controllers\OrganisationsController;
use App\Http\Controllers\OrganisationRolesController;
use App\Http\Controllers\OrganisationTypeController;
use App\Http\Controllers\OrganisationUsersController;
use App\Http\Controllers\PermissionController;
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
use App\Http\Controllers\SpeciesController;
use App\Http\Controllers\Organisation\HuntingConcessionController;

use App\Models\Organisation;


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
    Route::get('/admin/organisation-types/manage', [OrganisationTypeController::class, 'manage'])
        ->name('admin.organisation-types.manage');

    // Organization Types Routes - these are the only ones we should have
    Route::get('/admin/organisation-types/create/{parent?}', [OrganisationTypeController::class, 'createOrgType'])
        ->name('admin.organisation-types.create')
        ->where(['parent' => '[0-9]+']);

    // store organisation type
    Route::post('/admin/organisation-types', [OrganisationTypeController::class, 'storeOrgType'])
        ->name('admin.organisation-types.store');

    // edit organisation type
    Route::get('/admin/organisation-types/{organisationType}/edit', [OrganisationTypeController::class, 'edit'])
        ->name('admin.organisation-types.edit');

    Route::put('/admin/organisation-types/{organisationType}', [OrganisationTypeController::class, 'update'])
        ->name('admin.organisation-types.update');

    Route::delete('/admin/organisation-types/{organisationType}', [OrganisationTypeController::class, 'destroy'])
        ->name('admin.organisation-types.destroy')
        ->where(['organisationType' => '[a-z0-9-]+']);

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
});

Route::middleware('auth')->group(function () {

    //organisation dashboard
    Route::get('/organisation/dashboard/check/{organisation}', [\App\Http\Controllers\OrganisationDashboardController::class, 'checkDashboardAccess'])->name('organisation.check-dashboard-access')->middleware('auth');
    Route::get('/organisation/dashboard', [\App\Http\Controllers\OrganisationDashboardController::class, 'dashboard'])->name('organisation.dashboard')->middleware('auth');
    Route::get('/{organisation}/index', [\App\Http\Controllers\OrganisationDashboardController::class, 'index'])->name('organisation.dashboard.index')->middleware('auth');


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
        Route::get('hunting-records', [HuntingRecordController::class, 'index'])->name('hunting_records.index');
        Route::get('hunting-records/create', [HuntingRecordController::class, 'create'])->name('hunting_records.create');
        Route::post('hunting-records', [HuntingRecordController::class, 'store'])->name('hunting_records.store');
        Route::get('hunting-records/{record}', [HuntingRecordController::class, 'show'])->name('hunting_records.show');
        Route::get('hunting-records/{record}/edit', [HuntingRecordController::class, 'edit'])->name('hunting_records.edit');
        Route::patch('hunting-records/{record}', [HuntingRecordController::class, 'update'])->name('hunting_records.update');
        Route::delete('hunting-records/{record}', [HuntingRecordController::class, 'destroy'])->name('hunting_records.destroy');

        //crop conflict records
        Route::get('/crop-conflict-records', [CropConflictRecordController::class, 'index'])->name('crop_conflict_records.index');
        Route::get('/crop-conflict-records/create', [CropConflictRecordController::class, 'create'])->name('crop_conflict_records.create');
        Route::post('/crop-conflict-records', [CropConflictRecordController::class, 'store'])->name('crop_conflict_records.store');
        Route::get('/crop-conflict-records/{cropConflict}', [CropConflictRecordController::class, 'show'])->name('crop_conflict_records.show');
        Route::get('/crop-conflict-records/{cropConflict}/edit', [CropConflictRecordController::class, 'edit'])->name('crop_conflict_records.edit');
        Route::patch('/crop-conflict-records/{cropConflict}', [CropConflictRecordController::class, 'update'])->name('crop_conflict_records.update');
        Route::delete('/crop-conflict-records/{cropConflict}', [CropConflictRecordController::class, 'destroy'])->name('crop_conflict_records.destroy');

        //livestock conflict records
        Route::get('/livestock-conflict-records', [LiveStockConflictRecordController::class, 'index'])->name('livestock_conflict_records.index');
        Route::get('/livestock-conflict-records/create', [LiveStockConflictRecordController::class, 'create'])->name('livestock_conflict_records.create');
        Route::post('/livestock-conflict-records', [LiveStockConflictRecordController::class, 'store'])->name('livestock_conflict_records.store');
        Route::get('/livestock-conflict-records/{liveStockConflict}', [LiveStockConflictRecordController::class, 'show'])->name('livestock_conflict_records.show');
        Route::get('/livestock-conflict-records/{liveStockConflict}/edit', [LiveStockConflictRecordController::class, 'edit'])->name('livestock_conflict_records.edit');
        Route::patch('/livestock-conflict-records/{liveStockConflict}', [LiveStockConflictRecordController::class, 'update'])->name('livestock_conflict_records.update');
        Route::delete('/livestock-conflict-records/{liveStockConflict}', [LiveStockConflictRecordController::class, 'destroy'])->name('livestock_conflict_records.destroy');

        //human conflict records
        Route::get('/human-conflict-records', [HumanConflictRecordController::class, 'index'])->name('human_conflict_records.index');
        Route::get('/human-conflict-records/create', [HumanConflictRecordController::class, 'create'])->name('human_conflict_records.create');
        Route::post('/human-conflict-records', [HumanConflictRecordController::class, 'store'])->name('human_conflict_records.store');
        Route::get('/human-conflict-records/{humanConflict}', [HumanConflictRecordController::class, 'show'])->name('human_conflict_records.show');
        Route::get('/human-conflict-records/{humanConflict}/edit', [HumanConflictRecordController::class, 'edit'])->name('human_conflict_records.edit');
        Route::patch('/human-conflict-records/{humanConflict}', [HumanConflictRecordController::class, 'update'])->name('human_conflict_records.update');
        Route::delete('/human-conflict-records/{humanConflict}', [HumanConflictRecordController::class, 'destroy'])->name('human_conflict_records.destroy');

        //animal control records
        Route::get('/animal-control-records', [AnimalControlRecordController::class, 'index'])->name('animal_control_records.index');
        Route::get('/animal-control-records/create', [AnimalControlRecordController::class, 'create'])->name('animal_control_records.create');
        Route::post('/animal-control-records', [AnimalControlRecordController::class, 'store'])->name('animal_control_records.store');
        Route::get('/animal-control-records/{animalControl}', [AnimalControlRecordController::class, 'show'])->name('animal_control_records.show');
        Route::get('/animal-control-records/{animalControl}/edit', [AnimalControlRecordController::class, 'edit'])->name('animal_control_records.edit');
        Route::patch('/animal-control-records/{animalControl}', [AnimalControlRecordController::class, 'update'])->name('animal_control_records.update');
        Route::delete('/animal-control-records/{animalControl}', [AnimalControlRecordController::class, 'destroy'])->name('animal_control_records.destroy');

        //poaching records
        Route::get('/poaching-records', [PoachingRecordController::class, 'index'])->name('poaching_records.index');
        Route::get('/poaching-records/create', [PoachingRecordController::class, 'create'])->name('poaching_records.create');
        Route::post('/poaching-records', [PoachingRecordController::class, 'store'])->name('poaching_records.store');
        Route::get('/poaching-records/{poachingRecord}', [PoachingRecordController::class, 'show'])->name('poaching_records.show');
        Route::get('/poaching-records/{poachingRecord}/edit', [PoachingRecordController::class, 'edit'])->name('poaching_records.edit');
        Route::patch('/poaching-records/{poachingRecord}', [PoachingRecordController::class, 'update'])->name('poaching_records.update');
        Route::delete('/poaching-records/{poachingRecord}', [PoachingRecordController::class, 'destroy'])->name('poaching_records.destroy');

        //poachers records
        Route::get('/poachers-records', [PoachersRecordController::class, 'index'])->name('poachers_records.index');
        Route::get('/poachers-records/create', [PoachersRecordController::class, 'create'])->name('poachers_records.create');
        Route::post('/poachers-records', [PoachersRecordController::class, 'store'])->name('poachers_records.store');
        Route::get('/poachers-records/{poachersRecord}', [PoachersRecordController::class, 'show'])->name('poachers_records.show');
        Route::get('/poachers-records/{poachersRecord}/edit', [PoachersRecordController::class, 'edit'])->name('poachers_records.edit');
        Route::patch('/poachers-records/{poachersRecord}', [PoachersRecordController::class, 'update'])->name('poachers_records.update');
        Route::delete('/poachers-records/{poachersRecord}', [PoachersRecordController::class, 'destroy'])->name('poachers_records.destroy');

        //income records
        Route::resource('income-records', App\Http\Controllers\HistoricalData\IncomeRecordController::class)->names([
            'index' => 'income_records.index',
            'create' => 'income_records.create',
            'store' => 'income_records.store',
            'show' => 'income_records.show',
            'edit' => 'income_records.edit',
            'update' => 'income_records.update',
            'destroy' => 'income_records.destroy',
        ]);

        //income use records
        Route::resource('income-use-records', App\Http\Controllers\HistoricalData\IncomeUseRecordController::class)->names([
            'index' => 'income_use_records.index',
            'create' => 'income_use_records.create',
            'store' => 'income_use_records.store',
            'show' => 'income_use_records.show',
            'edit' => 'income_use_records.edit',
            'update' => 'income_use_records.update',
            'destroy' => 'income_use_records.destroy',
        ]);

        //source of income records
        Route::resource('source-of-income-records', App\Http\Controllers\HistoricalData\SourceOfIncomeRecordController::class)->names([
            'index' => 'source_of_income_records.index',
            'create' => 'source_of_income_records.create',
            'store' => 'source_of_income_records.store',
            'show' => 'source_of_income_records.show',
            'edit' => 'source_of_income_records.edit',
            'update' => 'source_of_income_records.update',
            'destroy' => 'source_of_income_records.destroy',
        ]);

        //income beneficiary records
        Route::resource('income-beneficiary-records', App\Http\Controllers\HistoricalData\IncomeBeneficiaryRecordController::class)->names([
            'index' => 'income_beneficiary_records.index',
            'create' => 'income_beneficiary_records.create',
            'store' => 'income_beneficiary_records.store',
            'show' => 'income_beneficiary_records.show',
            'edit' => 'income_beneficiary_records.edit',
            'update' => 'income_beneficiary_records.update',
            'destroy' => 'income_beneficiary_records.destroy',
        ]);

        //human resource records
        Route::resource('human-resource-records', App\Http\Controllers\HistoricalData\HumanResourceRecordController::class)->names([
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
    Route::get('/organisation/dashboard/check/{organisation}', [\App\Http\Controllers\OrganisationDashboardController::class, 'checkDashboardAccess'])->name('organisation.check-dashboard-access')->middleware('auth');
    Route::get('/organisation/dashboard', [\App\Http\Controllers\OrganisationDashboardController::class, 'dashboard'])->name('organisation.dashboard')->middleware('auth');
    Route::get('/{organisation}/index', [\App\Http\Controllers\OrganisationDashboardController::class, 'index'])->name('organisation.dashboard.index')->middleware('auth');

    Route::get('/rural-district-councils', [\App\Http\Controllers\OrganisationDashboardController::class, 'ruralDistrictCouncils'])->name('organisation.dashboard.rural-district-councils')->middleware('auth');

    //wildlife species
      // Species Routes
      Route::get('/{organisation}/species', [SpeciesController::class, 'index'])->name('species.index');
      Route::get('/{organisation}/species/create', [SpeciesController::class, 'create'])->name('species.create');
      Route::post('/{organisation}/species', [SpeciesController::class, 'store'])->name('species.store');
      Route::get('/{organisation}/species/{species}', [SpeciesController::class, 'show'])->name('species.show');
      Route::get('/{organisation}/species/{species}/edit', [SpeciesController::class, 'edit'])->name('species.edit');
      Route::patch('/{organisation}/species/{species}', [SpeciesController::class, 'update'])->name('species.update');
      Route::delete('/{organisation}/species/{species}', [SpeciesController::class, 'destroy'])->name('species.destroy');

    //routes for population estimates for organisation with organisation
    Route::get('/{organisation}/population-estimates', [\App\Http\Controllers\PopulationEstimateController::class, 'species'])->name('organisation.population-estimates.species');
    Route::get('/{organisation}/population-estimates/{species}/index', [\App\Http\Controllers\PopulationEstimateController::class, 'index'])->name('organisation.population-estimates.index');
    Route::get('/{organisation}/population-estimates/by-counting-methods', [\App\Http\Controllers\PopulationEstimateController::class, 'byCountingMethods'])->name('organisation.population-estimates.by-counting-methods');
    Route::post('/{organisation}/population-estimates/store', [\App\Http\Controllers\PopulationEstimateController::class, 'store'])->name('organisation.population-estimates.store');
    Route::get('/{organisation}/population-estimates/{populationEstimate}/edit', [\App\Http\Controllers\PopulationEstimateController::class, 'edit'])->name('organisation.population-estimates.edit');
    Route::patch('/{organisation}/population-estimates/{populationEstimate}/update', [\App\Http\Controllers\PopulationEstimateController::class, 'update'])->name('organisation.population-estimates.update');

    //quota settings routes
    Route::get('/{organisation}/quota-settings/species', [\App\Http\Controllers\QuotaSettingController::class, 'species'])->name('organisation.quota-settings.species');
    Route::get('/{organisation}/quota-settings/{species}/index', [\App\Http\Controllers\QuotaSettingController::class, 'index'])->name('organisation.quota-settings.index');
    Route::post('/{organisation}/quota-settings/store', [\App\Http\Controllers\QuotaSettingController::class, 'store'])->name('organisation.quota-settings.store');
    Route::get('/{organisation}/quota-settings/{quotaRequest}/edit', [\App\Http\Controllers\QuotaSettingController::class, 'edit'])->name('organisation.quota-settings.edit');
    Route::patch('/{organisation}/quota-settings/{quotaRequest}/update', [\App\Http\Controllers\QuotaSettingController::class, 'update'])->name('organisation.quota-settings.update');

    //routes for WardQuotaDistribution for a quota request
    Route::get('/{organisation}/quota-settings/{quotaRequest}/ward-quota-distribution', [\App\Http\Controllers\WardQuotaDistributionController::class, 'index'])->name('organisation.ward-quota-distribution.index');
    Route::post('/{organisation}/quota-settings/{quotaRequest}/ward-quota-distribution/store', [\App\Http\Controllers\WardQuotaDistributionController::class, 'store'])->name('organisation.ward-quota-distribution.store');
    Route::get('/{organisation}/quota-settings/{quotaRequest}/ward-quota-distribution/{wardQuotaDistribution}/edit', [\App\Http\Controllers\WardQuotaDistributionController::class, 'edit'])->name('organisation.ward-quota-distribution.edit');
    Route::patch('/{organisation}/quota-settings/{quotaRequest}/ward-quota-distribution/{wardQuotaDistribution}/update', [\App\Http\Controllers\WardQuotaDistributionController::class, 'update'])->name('organisation.ward-quota-distribution.update');
    Route::delete('/{organisation}/quota-settings/{quotaRequest}/ward-quota-distribution/{wardQuotaDistribution}', [\App\Http\Controllers\WardQuotaDistributionController::class, 'destroy'])->name('organisation.ward-quota-distribution.destroy');


    //hunting concession routes
    Route::get('/{organisation}/hunting-concessions', [\App\Http\Controllers\HuntingConcessionController::class, 'index'])->name('organisation.hunting-concessions.index');
    Route::get('/{organisation}/hunting-concessions/create', [\App\Http\Controllers\HuntingConcessionController::class, 'create'])->name('organisation.hunting-concessions.create');
    Route::post('/{organisation}/hunting-concessions/store', [\App\Http\Controllers\HuntingConcessionController::class, 'store'])->name('organisation.hunting-concessions.store');
    Route::get('/{organisation}/hunting-concessions/{huntingConcession}/edit', [\App\Http\Controllers\HuntingConcessionController::class, 'edit'])->name('organisation.hunting-concessions.edit');
    Route::patch('/{organisation}/hunting-concessions/{huntingConcession}/update', [\App\Http\Controllers\HuntingConcessionController::class, 'update'])->name('organisation.hunting-concessions.update');
    Route::delete('/{organisation}/hunting-concessions/{huntingConcession}', [\App\Http\Controllers\HuntingConcessionController::class, 'destroy'])->name('organisation.hunting-concessions.destroy');


    //hunter controller
    Route::get('/{organisation}/hunters', [\App\Http\Controllers\HunterController::class, 'index'])->name('organisation.hunters.index');
    //create, store, edit, update, destroy
    Route::get('/{organisation}/hunters/create', [\App\Http\Controllers\HunterController::class, 'create'])->name('organisation.hunters.create');
    Route::post('/{organisation}/hunters/store', [\App\Http\Controllers\HunterController::class, 'store'])->name('organisation.hunters.store');
    Route::get('/{organisation}/hunters/{hunter}/edit', [\App\Http\Controllers\HunterController::class, 'edit'])->name('organisation.hunters.edit');
    Route::patch('/{organisation}/hunters/{hunter}/update', [\App\Http\Controllers\HunterController::class, 'update'])->name('organisation.hunters.update');
    Route::delete('/{organisation}/hunters/{hunter}', [\App\Http\Controllers\HunterController::class, 'destroy'])->name('organisation.hunters.destroy');

    //safari-license
    Route::get('/{organisation}/safari-licenses', [\App\Http\Controllers\HuntingLicenseController::class, 'index'])->name('organisation.safari-licenses.index');

    //give create, store, edit, update and delete routes
    Route::get('/{organisation}/safari-licenses/create', [\App\Http\Controllers\HuntingLicenseController::class, 'create'])->name('organisation.safari-licenses.create');
    Route::post('/{organisation}/safari-licenses/store', [\App\Http\Controllers\HuntingLicenseController::class, 'store'])->name('organisation.safari-licenses.store');
    Route::get('/{organisation}/safari-licenses/{huntingLicense}/edit', [\App\Http\Controllers\HuntingLicenseController::class, 'edit'])->name('organisation.safari-licenses.edit');
    Route::patch('/{organisation}/safari-licenses/{huntingLicense}/update', [\App\Http\Controllers\HuntingLicenseController::class, 'update'])->name('organisation.safari-licenses.update');
    Route::delete('/{organisation}/safari-licenses/{huntingLicense}', [\App\Http\Controllers\HuntingLicenseController::class, 'destroy'])->name('organisation.safari-licenses.destroy');

    //need routes for organisation.hunting-activity
    Route::get('/{organisation}/hunting-activities', [\App\Http\Controllers\HuntingActivityController::class, 'index'])->name('organisation.hunting-activities.index');
    Route::get('/{organisation}/hunting-activities/create', [\App\Http\Controllers\HuntingActivityController::class, 'create'])->name('organisation.hunting-activities.create');
    Route::post('/{organisation}/hunting-activities/store', [\App\Http\Controllers\HuntingActivityController::class, 'store'])->name('organisation.hunting-activities.store');
    Route::get('/{organisation}/hunting-activities/{huntingActivity}/edit', [\App\Http\Controllers\HuntingActivityController::class, 'edit'])->name('organisation.hunting-activities.edit');
    Route::patch('/{organisation}/hunting-activities/{huntingActivity}/update', [\App\Http\Controllers\HuntingActivityController::class, 'update'])->name('organisation.hunting-activities.update');
    Route::delete('/{organisation}/hunting-activities/{huntingActivity}', [\App\Http\Controllers\HuntingActivityController::class, 'destroy'])->name('organisation.hunting-activities.destroy');
    Route::get('/{organisation}/hunting-activities/{huntingActivity}', [\App\Http\Controllers\HuntingActivityController::class, 'show'])->name('organisation.hunting-activities.show');

    //add hunter client
    Route::get('/{organisation}/hunting-activities/{huntingActivity}/add-hunter-client', [\App\Http\Controllers\HuntingActivityController::class, 'addHunterClient'])->name('organisation.hunting-activities.add-hunter-client');
    Route::post('/{organisation}/hunting-activities/{huntingActivity}/save-hunter-client', [\App\Http\Controllers\HuntingActivityController::class, 'saveHunterClient'])->name('organisation.hunting-activities.save-hunter-client');
    Route::post('/{organisation}/hunting-activities/{huntingActivity}/save-new-hunter-client', [\App\Http\Controllers\HuntingActivityController::class, 'saveNewHunterClient'])->name('organisation.hunting-activities.save-new-hunter-client');

    //hunting vehicles
    Route::get('/{organisation}/hunting-activities/{huntingActivity}/add-hunting-vehicles', [\App\Http\Controllers\HuntingActivityController::class, 'addHuntingVehicles'])->name('organisation.hunting-activities.add-hunting-vehicles');
    Route::post('/{organisation}/hunting-activities/{huntingActivity}/save-hunting-vehicles', [\App\Http\Controllers\HuntingActivityController::class, 'saveHuntingVehicles'])->name('organisation.hunting-activities.save-hunting-vehicles');
    Route::patch('/{organisation}/hunting-activities/{huntingActivityVehicle}/update-hunting-vehicles', [\App\Http\Controllers\HuntingActivityController::class, 'updateHuntingVehicles'])->name('organisation.hunting-activities.update-hunting-vehicles');
    Route::delete('/{organisation}/hunting-activities/{huntingActivityVehicle}/delete-hunting-vehicles', [\App\Http\Controllers\HuntingActivityController::class, 'deleteHuntingVehicles'])->name('organisation.hunting-activities.delete-hunting-vehicles');

    //species details
    Route::get('/{organisation}/hunting-activities/{huntingActivity}/species-details', [\App\Http\Controllers\HuntingDetailsController::class, 'index'])->name('organisation.hunting-activities.species-details');
    Route::post('/{organisation}/hunting-activities/{huntingActivity}/save-species-details', [\App\Http\Controllers\HuntingDetailsController::class, 'store'])->name('organisation.hunting-activities.save-species-details');
    Route::get('/{organisation}/hunting-activities/{huntingDetail}/edit-species-details', [\App\Http\Controllers\HuntingDetailsController::class, 'editSpeciesDetails'])->name('organisation.hunting-activities.edit-species-details');
    Route::patch('/{organisation}/hunting-activities/{huntingDetail}/update-species-details', [\App\Http\Controllers\HuntingDetailsController::class, 'updateSpeciesDetails'])->name('organisation.hunting-activities.update-species-details');
    Route::delete('/{organisation}/hunting-activities/{huntingDetail}/delete-species-details', [\App\Http\Controllers\HuntingDetailsController::class, 'deleteSpeciesDetails'])->name('organisation.hunting-activities.delete-species-details');

    //hunting detail outcomes
    Route::get('/{organisation}/hunting-activities/{huntingDetail}/hunting-outcomes', [\App\Http\Controllers\HuntingDetailOutComeController::class, 'index'])->name('organisation.hunting-detail-outcome.index');
    Route::post('/{organisation}/hunting-activities/{huntingDetail}/hunting-outcomes/store', [\App\Http\Controllers\HuntingDetailOutComeController::class, 'store'])->name('organisation.hunting-detail-outcome.store');
    Route::get('/{organisation}/hunting-activities/hunting-outcomes/{huntingDetailOutCome}/edit', [\App\Http\Controllers\HuntingDetailOutComeController::class, 'edit'])->name('organisation.hunting-detail-outcome.edit');
    Route::patch('/{organisation}/hunting-activities/hunting-outcomes/{huntingDetailOutCome}/update', [\App\Http\Controllers\HuntingDetailOutComeController::class, 'update'])->name('organisation.hunting-detail-outcome.update');
    Route::delete('/{organisation}/hunting-activities/hunting-outcomes/{huntingDetailOutCome}', [\App\Http\Controllers\HuntingDetailOutComeController::class, 'destroy'])->name('organisation.hunting-detail-outcome.destroy');


    //Human Wildlife Conflict Incidents routes
    Route::get('/{organisation}/incidents', [\App\Http\Controllers\IncidentController::class, 'index'])->name('organisation.incidents.index');
    Route::get('/{organisation}/incidents/create', [\App\Http\Controllers\IncidentController::class, 'create'])->name('organisation.incidents.create');
    Route::post('/{organisation}/incidents/store', [\App\Http\Controllers\IncidentController::class, 'store'])->name('organisation.incidents.store');
    Route::get('/{organisation}/incidents/{incident}', [\App\Http\Controllers\IncidentController::class, 'show'])->name('organisation.incidents.show');
    Route::get('/{organisation}/incidents/{incident}/edit', [\App\Http\Controllers\IncidentController::class, 'edit'])->name('organisation.incidents.edit');
    Route::patch('/{organisation}/incidents/{incident}/update', [\App\Http\Controllers\IncidentController::class, 'update'])->name('organisation.incidents.update');
    Route::delete('/{organisation}/incidents/{incident}', [\App\Http\Controllers\IncidentController::class, 'destroy'])->name('organisation.incidents.destroy');


    //incident species routes
    Route::get('/{organisation}/incidents/{incident}/species', [\App\Http\Controllers\IncidentSpeciesController::class, 'index'])->name('organisation.incident-species.index');
    Route::post('/{organisation}/incidents/{incident}/species/store', [\App\Http\Controllers\IncidentSpeciesController::class, 'store'])->name('organisation.incident-species.store');
    Route::get('/{organisation}/incidents/{incident}/species/{incidentSpecies}/edit', [\App\Http\Controllers\IncidentSpeciesController::class, 'edit'])->name('organisation.incident-species.edit');
    Route::patch('/{organisation}/incidents/{incident}/species/{incidentSpecies}/update', [\App\Http\Controllers\IncidentSpeciesController::class, 'update'])->name('organisation.incident-species.update');
    Route::delete('/{organisation}/incidents/{incident}/species/{incidentSpecies}', [\App\Http\Controllers\IncidentSpeciesController::class, 'destroy'])->name('organisation.incident-species.destroy');


    //incident conflict types routes
    Route::get('/{organisation}/incidents/{incident}/conflict-types', [\App\Http\Controllers\IncidentConflictTypeController::class, 'index'])->name('organisation.incident-conflict-types.index');
    Route::post('/{organisation}/incidents/{incident}/conflict-types/store', [\App\Http\Controllers\IncidentConflictTypeController::class, 'store'])->name('organisation.incident-conflict-types.store');
    Route::get('/{organisation}/incidents/{incident}/conflict-types/{incidentConflictType}/edit', [\App\Http\Controllers\IncidentConflictTypeController::class, 'edit'])->name('organisation.incident-conflict-types.edit');
    Route::patch('/{organisation}/incidents/{incident}/conflict-types/{incidentConflictType}/update', [\App\Http\Controllers\IncidentConflictTypeController::class, 'update'])->name('organisation.incident-conflict-types.update');
    Route::delete('/{organisation}/incidents/{incident}/conflict-types/{incidentConflictType}', [\App\Http\Controllers\IncidentConflictTypeController::class, 'destroy'])->name('organisation.incident-conflict-types.destroy');

    //incident outcomes
    Route::get('/{organisation}/incidents/{incident}/outcomes', [\App\Http\Controllers\IncidentOutcomeController::class, 'index'])->name('organisation.incident-outcomes.index');
    Route::post('/{organisation}/incidents/{incident}/outcomes/store', [\App\Http\Controllers\IncidentOutcomeController::class, 'store'])->name('organisation.incident-outcomes.store');
    Route::get('/{organisation}/incidents/{incident}/outcomes/{incidentOutcome}/edit', [\App\Http\Controllers\IncidentOutcomeController::class, 'edit'])->name('organisation.incident-outcomes.edit');
    Route::patch('/{organisation}/incidents/{incident}/outcomes/{incidentOutcome}/update', [\App\Http\Controllers\IncidentOutcomeController::class, 'update'])->name('organisation.incident-outcomes.update');
    Route::delete('/{organisation}/incidents/{incident}/outcomes/{incidentOutcome}', [\App\Http\Controllers\IncidentOutcomeController::class, 'destroy'])->name('organisation.incident-outcomes.destroy');

    //incident conflict outcome dynamic fields
    Route::get('/{organisation}/incidents/{incident}/outcomes/{incidentOutCome}/dynamic-fields', [\App\Http\Controllers\IncidentOutcomeDynamicFieldController::class, 'index'])->name('organisation.incident-outcomes-dynamic-fields.index');
    Route::post('/{organisation}/incidents/{incident}/outcomes/{incidentOutCome}/dynamic-fields/store', [\App\Http\Controllers\IncidentOutcomeDynamicFieldController::class, 'store'])->name('organisation.incident-outcomes-dynamic-fields.store');
    Route::get('/{organisation}/incidents/{incident}/outcomes/{incidentOutCome}/dynamic-fields/{incidentOutcomeDynamicField}/edit', [\App\Http\Controllers\IncidentOutcomeDynamicFieldController::class, 'edit'])->name('organisation.incident-outcomes-dynamic-fields.edit');
    Route::patch('/{organisation}/incidents/{incident}/outcomes/{incidentOutCome}/dynamic-fields/{incidentOutcomeDynamicField}/update', [\App\Http\Controllers\IncidentOutcomeDynamicFieldController::class, 'update'])->name('organisation.incident-outcomes-dynamic-fields.update');
    Route::delete('/{organisation}/incidents/{incident}/outcomes/{incidentOutCome}/dynamic-fields/{incidentOutcomeDynamicField}', [\App\Http\Controllers\IncidentOutcomeDynamicFieldController::class, 'destroy'])->name('organisation.incident-outcomes-dynamic-fields.destroy');

    //Problem Animal Control
    Route::get('/{organisation}/problem-animal-control/index', [\App\Http\Controllers\ProblemAnimalControlController::class, 'index'])->name('organisation.problem-animal-control.index');
    Route::get('/{organisation}/problem-animal-control/{incident}/create', [\App\Http\Controllers\ProblemAnimalControlController::class, 'create'])->name('organisation.problem-animal-control.create');
    Route::post('/{organisation}/problem-animal-control/{incident}/store', [\App\Http\Controllers\ProblemAnimalControlController::class, 'store'])->name('organisation.problem-animal-control.store');
    Route::get('/{organisation}/problem-animal-control/{problemAnimalControl}', [\App\Http\Controllers\ProblemAnimalControlController::class, 'show'])->name('organisation.problem-animal-control.edit');
    Route::get('/{organisation}/problem-animal-control/{problemAnimalControl}/edit', [\App\Http\Controllers\ProblemAnimalControlController::class, 'edit'])->name('organisation.problem-animal-control.edit');
    Route::patch('/{organisation}/problem-animal-control/{problemAnimalControl}/update', [\App\Http\Controllers\ProblemAnimalControlController::class, 'update'])->name('organisation.problem-animal-control.update-species-detail');
    Route::delete('/{organisation}/problem-animal-control/{problemAnimalControl}', [\App\Http\Controllers\ProblemAnimalControlController::class, 'destroy'])->name('organisation.problem-animal-control.destroy');


    //Poaching incidents
    Route::get('/{organisation}/poaching-incidents', [\App\Http\Controllers\PoachingIncidentController::class, 'index'])->name('organisation.poaching-incidents.index');
    Route::get('/{organisation}/poaching-incidents/create', [\App\Http\Controllers\PoachingIncidentController::class, 'create'])->name('organisation.poaching-incidents.create');
    Route::post('/{organisation}/poaching-incidents/store', [\App\Http\Controllers\PoachingIncidentController::class, 'store'])->name('organisation.poaching-incidents.store');
    Route::get('/{organisation}/poaching-incidents/{poachingIncident}/edit', [\App\Http\Controllers\PoachingIncidentController::class, 'edit'])->name('organisation.poaching-incidents.edit');
    Route::patch('/{organisation}/poaching-incidents/{poachingIncident}/update', [\App\Http\Controllers\PoachingIncidentController::class, 'update'])->name('organisation.poaching-incidents.update');
    Route::delete('/{organisation}/poaching-incidents/{poachingIncident}', [\App\Http\Controllers\PoachingIncidentController::class, 'destroy'])->name('organisation.poaching-incidents.destroy');

    //poaching incident species
    Route::get('/{organisation}/poaching-incidents/{poachingIncident}/species', [\App\Http\Controllers\PoachingIncidentSpeciesController::class, 'index'])->name('organisation.poaching-incident-species.index');
    Route::post('/{organisation}/poaching-incidents/{poachingIncident}/species/store', [\App\Http\Controllers\PoachingIncidentSpeciesController::class, 'store'])->name('organisation.poaching-incident-species.store');
    Route::get('/{organisation}/poaching-incidents/{poachingIncident}/species/{poachingIncidentSpecies}/edit', [\App\Http\Controllers\PoachingIncidentSpeciesController::class, 'edit'])->name('organisation.poaching-incident-species.edit');
    Route::patch('/{organisation}/poaching-incidents/{poachingIncident}/species/{poachingIncidentSpecies}/update', [\App\Http\Controllers\PoachingIncidentSpeciesController::class, 'update'])->name('organisation.poaching-incident-species.update');
    Route::delete('/{organisation}/poaching-incidents/{poachingIncident}/species/{poachingIncidentSpecies}', [\App\Http\Controllers\PoachingIncidentSpeciesController::class, 'destroy'])->name('organisation.poaching-incident-species.destroy');

    //manage poacher
    Route::get('/{organisation}/poacher/{poachingIncident}/index', [\App\Http\Controllers\PoacherController::class, 'index'])->name('organisation.poaching-incident-poacher.index');
    Route::post('/{organisation}/poacher/{poachingIncident}/store', [\App\Http\Controllers\PoacherController::class, 'store'])->name('organisation.poaching-incident-poacher.store');
    Route::post('/{organisation}/poacher/{poacher}/edit', [\App\Http\Controllers\PoacherController::class, 'edit'])->name('organisation.poaching-incident-poacher.edit');
    Route::patch('/{organisation}/poacher/{poacher}/update', [\App\Http\Controllers\PoacherController::class, 'update'])->name('organisation.poaching-incident-poacher.update');
    Route::delete('/{organisation}/poacher/{poacher}/destroy', [\App\Http\Controllers\PoacherController::class, 'destroy'])->name('organisation.poaching-incident-poacher.destroy');

    //organisation create
    Route::get('/{organisation}/organisations/{organisationType}/{parentOrganisation}/index', [\App\Http\Controllers\OrganisationChildrenController::class, 'index'])->name('organisation.organisations.index');
    Route::post('/{organisation}/organisations/{organisationType}/{parentOrganisation}/store', [\App\Http\Controllers\OrganisationChildrenController::class, 'store'])->name('organisation.organisations.store');
    Route::get('/{organisation}/organisations/{organisationType}/edit', [\App\Http\Controllers\OrganisationChildrenController::class, 'edit'])->name('organisation.organisations.edit');
    Route::patch('/{organisation}/organisations/{organisationToUpdate}/update', [\App\Http\Controllers\OrganisationChildrenController::class, 'update'])->name('organisation.organisations.update');
    Route::delete('/{organisation}/organisations/{organisationToDelete}', [\App\Http\Controllers\OrganisationChildrenController::class, 'destroy'])->name('organisation.organisations.destroy');

    /*
    |--------------------------------------------------------------------------
    | Organisation Dashboard Payments
    |--------------------------------------------------------------------------
    */

    //organisation categories
    Route::get('/{organisation}/payable-categories', [\App\Http\Controllers\OrganisationPayableItemController::class, 'payableItemCategories'])->name('organisation.payable-categories.index');
    //organisation payable items
    Route::get('/{organisation}/{category}/payable-items', [\App\Http\Controllers\OrganisationPayableItemController::class, 'index'])->name('organisation.payable-items.index');
    Route::get('/{organisation}/{category}/payable-items/create', [\App\Http\Controllers\OrganisationPayableItemController::class, 'create'])->name('organisation.payable-items.create');
    Route::post('/{organisation}/{category}/payable-items/store', [\App\Http\Controllers\OrganisationPayableItemController::class, 'store'])->name('organisation.payable-items.store');
    Route::get('/{organisation}/{category}/payable-items/{payableItem}/edit', [\App\Http\Controllers\OrganisationPayableItemController::class, 'edit'])->name('organisation.payable-items.edit');
    Route::patch('/{organisation}/{category}/payable-items/{payableItem}/update', [\App\Http\Controllers\OrganisationPayableItemController::class, 'update'])->name('organisation.payable-items.update');
    Route::delete('/{organisation}/{category}/payable-items/{payableItem}', [\App\Http\Controllers\OrganisationPayableItemController::class, 'destroy'])->name('organisation.payable-items.destroy');


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

    //organisation projects
    Route::get('/{organisation}/projects', [\App\Http\Controllers\ProjectController::class, 'index'])->name('organisation.projects.index');
    Route::get('/{organisation}/projects/create', [\App\Http\Controllers\ProjectController::class, 'create'])->name('organisation.projects.create');
    Route::post('/{organisation}/projects/store', [\App\Http\Controllers\ProjectController::class, 'store'])->name('organisation.projects.store');
    Route::get('/{organisation}/projects/{project}', [\App\Http\Controllers\ProjectController::class, 'show'])->name('organisation.projects.show');
    Route::get('/{organisation}/projects/{project}/edit', [\App\Http\Controllers\ProjectController::class, 'edit'])->name('organisation.projects.edit');
    Route::patch('/{organisation}/projects/{project}/update', [\App\Http\Controllers\ProjectController::class, 'update'])->name('organisation.projects.update');
    Route::delete('/{organisation}/projects/{project}', [\App\Http\Controllers\ProjectController::class, 'destroy'])->name('organisation.projects.destroy');

    //project timelines
    Route::get('/{organisation}/projects/{project}/timelines', [\App\Http\Controllers\ProjectTimelineController::class, 'index'])->name('organisation.project-timelines.index');
    Route::get('/{organisation}/projects/{project}/timelines/create', [\App\Http\Controllers\ProjectTimelineController::class, 'create'])->name('organisation.project-timelines.create');
    Route::post('/{organisation}/projects/{project}/timelines/store', [\App\Http\Controllers\ProjectTimelineController::class, 'store'])->name('organisation.project-timelines.store');
    Route::get('/{organisation}/projects/{project}/timelines/{projectTimeline}/edit', [\App\Http\Controllers\ProjectTimelineController::class, 'edit'])->name('organisation.project-timelines.edit');
    Route::patch('/{organisation}/projects/{project}/timelines/{projectTimeline}/update', [\App\Http\Controllers\ProjectTimelineController::class, 'update'])->name('organisation.project-timelines.update');
    Route::delete('/{organisation}/projects/{project}/timelines/{projectTimeline}', [\App\Http\Controllers\ProjectTimelineController::class, 'destroy'])->name('organisation.project-timelines.destroy');

    //project budgets
    Route::get('/{organisation}/projects/{project}/budgets', [\App\Http\Controllers\ProjectBudgetController::class, 'index'])->name('organisation.project-budgets.index');
    Route::get('/{organisation}/projects/{project}/budgets/create', [\App\Http\Controllers\ProjectBudgetController::class, 'create'])->name('organisation.project-budgets.create');
    Route::post('/{organisation}/projects/{project}/budgets/store', [\App\Http\Controllers\ProjectBudgetController::class, 'store'])->name('organisation.project-budgets.store');
    Route::get('/{organisation}/projects/{project}/budgets/{projectBudget}/edit', [\App\Http\Controllers\ProjectBudgetController::class, 'edit'])->name('organisation.project-budgets.edit');
    Route::patch('/{organisation}/projects/{project}/budgets/{projectBudget}/update', [\App\Http\Controllers\ProjectBudgetController::class, 'update'])->name('organisation.project-budgets.update');
    Route::delete('/{organisation}/projects/{project}/budgets/{projectBudget}', [\App\Http\Controllers\ProjectBudgetController::class, 'destroy'])->name('organisation.project-budgets.destroy');

    //project stakeholders
    Route::get('/{organisation}/projects/{project}/stakeholders', [\App\Http\Controllers\StakeholderController::class, 'index'])->name('organisation.project-stakeholders.index');
    Route::get('/{organisation}/projects/{project}/stakeholders/create', [\App\Http\Controllers\StakeholderController::class, 'create'])->name('organisation.project-stakeholders.create');
    Route::post('/{organisation}/projects/{project}/stakeholders/store', [\App\Http\Controllers\StakeholderController::class, 'store'])->name('organisation.project-stakeholders.store');
    Route::get('/{organisation}/projects/{project}/stakeholders/{stakeholder}/edit', [\App\Http\Controllers\StakeholderController::class, 'edit'])->name('organisation.project-stakeholders.edit');
    Route::patch('/{organisation}/projects/{project}/stakeholders/{stakeholder}/update', [\App\Http\Controllers\StakeholderController::class, 'update'])->name('organisation.project-stakeholders.update');
    Route::delete('/{organisation}/projects/{project}/stakeholders/{stakeholder}', [\App\Http\Controllers\StakeholderController::class, 'destroy'])->name('organisation.project-stakeholders.destroy');

    //project beneficiaries
    Route::get('/{organisation}/projects/{project}/beneficiaries', [\App\Http\Controllers\BeneficiaryController::class, 'index'])->name('organisation.project-beneficiaries.index');
    Route::get('/{organisation}/projects/{project}/beneficiaries/create', [\App\Http\Controllers\BeneficiaryController::class, 'create'])->name('organisation.project-beneficiaries.create');
    Route::post('/{organisation}/projects/{project}/beneficiaries/store', [\App\Http\Controllers\BeneficiaryController::class, 'store'])->name('organisation.project-beneficiaries.store');
    Route::get('/{organisation}/projects/{project}/beneficiaries/{beneficiary}/edit', [\App\Http\Controllers\BeneficiaryController::class, 'edit'])->name('organisation.project-beneficiaries.edit');
    Route::patch('/{organisation}/projects/{project}/beneficiaries/{beneficiary}/update', [\App\Http\Controllers\BeneficiaryController::class, 'update'])->name('organisation.project-beneficiaries.update');
    Route::delete('/{organisation}/projects/{project}/beneficiaries/{beneficiary}', [\App\Http\Controllers\BeneficiaryController::class, 'destroy'])->name('organisation.project-beneficiaries.destroy');


});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


require __DIR__ . '/auth.php';
