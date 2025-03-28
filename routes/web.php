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


    //hunting concession routes
    Route::get('/{organisation}/hunting-concessions', [\App\Http\Controllers\Organisation\HuntingConcessionController::class, 'index'])->name('organisation.hunting-concessions.index');
    Route::get('/{organisation}/hunting-concessions/create', [\App\Http\Controllers\Organisation\HuntingConcessionController::class, 'create'])->name('organisation.hunting-concessions.create');
    Route::post('/{organisation}/hunting-concessions/store', [\App\Http\Controllers\Organisation\HuntingConcessionController::class, 'store'])->name('organisation.hunting-concessions.store');
    Route::get('/{organisation}/hunting-concessions/{huntingConcession}', [\App\Http\Controllers\Organisation\HuntingConcessionController::class, 'show'])->name('organisation.hunting-concessions.show');
    Route::get('/{organisation}/hunting-concessions/{huntingConcession}/edit', [\App\Http\Controllers\Organisation\HuntingConcessionController::class, 'edit'])->name('organisation.hunting-concessions.edit');
    Route::patch('/{organisation}/hunting-concessions/{huntingConcession}/update', [\App\Http\Controllers\Organisation\HuntingConcessionController::class, 'update'])->name('organisation.hunting-concessions.update');
    Route::delete('/{organisation}/hunting-concessions/{huntingConcession}', [\App\Http\Controllers\Organisation\HuntingConcessionController::class, 'destroy'])->name('organisation.hunting-concessions.destroy');



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

   
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


require __DIR__ . '/auth.php';
