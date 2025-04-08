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
use App\Http\Controllers\Organisation\ProblemAnimalControlController;


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
    Route::get('/{organisation}/historical-dashboard', [OrganisationDashboardController::class, 'historicalDashboard'])->name('organisation.dashboard.historical')->middleware('auth');

    Route::get('/rural-district-councils', [OrganisationDashboardController::class, 'ruralDistrictCouncils'])->name('organisation.dashboard.rural-district-councils')->middleware('auth');

    // Historical Data Routes
    Route::prefix('/{organisation}')->group(function () {
        // Hunting Records routes
        Route::resource('hunting-records', HuntingRecordController::class)->names([
            'index' => 'hunting_records.index',
            'create' => 'hunting_records.create',
            'store' => 'hunting_records.store',
            'show' => 'hunting_records.show',
            'edit' => 'hunting_records.edit',
            'update' => 'hunting_records.update',
            'destroy' => 'hunting_records.destroy',
        ])->parameters([
            'hunting-records' => 'record'
        ]);
        
        // Crop Conflict Records routes
        Route::resource('crop-conflict-records', CropConflictRecordController::class)->names([
            'index' => 'crop_conflict_records.index',
            'create' => 'crop_conflict_records.create',
            'store' => 'crop_conflict_records.store',
            'show' => 'crop_conflict_records.show',
            'edit' => 'crop_conflict_records.edit',
            'update' => 'crop_conflict_records.update',
            'destroy' => 'crop_conflict_records.destroy',
        ])->parameters([
            'crop-conflict-records' => 'cropConflict'
        ]);
        
        // Livestock Conflict Records routes
        Route::resource('livestock-conflict-records', LiveStockConflictRecordController::class)->names([
            'index' => 'livestock_conflict_records.index',
            'create' => 'livestock_conflict_records.create',
            'store' => 'livestock_conflict_records.store',
            'show' => 'livestock_conflict_records.show',
            'edit' => 'livestock_conflict_records.edit',
            'update' => 'livestock_conflict_records.update',
            'destroy' => 'livestock_conflict_records.destroy',
        ])->parameters([
            'livestock-conflict-records' => 'liveStockConflict'
        ]);
        
        // Human Conflict Records routes
        Route::resource('human-conflict-records', HumanConflictRecordController::class)->names([
            'index' => 'human_conflict_records.index',
            'create' => 'human_conflict_records.create',
            'store' => 'human_conflict_records.store',
            'show' => 'human_conflict_records.show',
            'edit' => 'human_conflict_records.edit',
            'update' => 'human_conflict_records.update',
            'destroy' => 'human_conflict_records.destroy',
        ])->parameters([
            'human-conflict-records' => 'humanConflict'
        ]);
        
        // Animal Control Records routes
        Route::resource('animal-control-records', AnimalControlRecordController::class)->names([
            'index' => 'animal_control_records.index',
            'create' => 'animal_control_records.create',
            'store' => 'animal_control_records.store',
            'show' => 'animal_control_records.show',
            'edit' => 'animal_control_records.edit',
            'update' => 'animal_control_records.update',
            'destroy' => 'animal_control_records.destroy',
        ])->parameters([
            'animal-control-records' => 'animalControl'
        ]);
        
        // Poaching Records routes
        Route::resource('poaching-records', PoachingRecordController::class)->names([
            'index' => 'poaching_records.index',
            'create' => 'poaching_records.create',
            'store' => 'poaching_records.store',
            'show' => 'poaching_records.show',
            'edit' => 'poaching_records.edit',
            'update' => 'poaching_records.update',
            'destroy' => 'poaching_records.destroy',
        ])->parameters([
            'poaching-records' => 'poachingRecord'
        ]);
        
        // Poachers Records routes
        Route::resource('poachers-records', PoachersRecordController::class)->names([
            'index' => 'poachers_records.index',
            'create' => 'poachers_records.create',
            'store' => 'poachers_records.store', 
            'show' => 'poachers_records.show',
            'edit' => 'poachers_records.edit',
            'update' => 'poachers_records.update',
            'destroy' => 'poachers_records.destroy',
        ])->parameters([
            'poachers-records' => 'poachersRecord'
        ]);
        
        // Income Records routes
        Route::resource('income-records', IncomeRecordController::class)->names([
            'index' => 'income_records.index',
            'create' => 'income_records.create',
            'store' => 'income_records.store',
            'show' => 'income_records.show',
            'edit' => 'income_records.edit',
            'update' => 'income_records.update',
            'destroy' => 'income_records.destroy',
        ])->parameters([
            'income-records' => 'incomeRecord'
        ]);
        
        // Income Use Records routes 
        Route::resource('income-use-records', IncomeUseRecordController::class)->names([
            'index' => 'income_use_records.index',
            'create' => 'income_use_records.create',
            'store' => 'income_use_records.store',
            'show' => 'income_use_records.show',
            'edit' => 'income_use_records.edit',
            'update' => 'income_use_records.update',
            'destroy' => 'income_use_records.destroy',
        ])->parameters([
            'income-use-records' => 'incomeUseRecord'
        ]);
        
        // Source of Income Records routes
        Route::resource('source-of-income-records', SourceOfIncomeRecordController::class)->names([
            'index' => 'source_of_income_records.index',
            'create' => 'source_of_income_records.create',
            'store' => 'source_of_income_records.store',
            'show' => 'source_of_income_records.show',
            'edit' => 'source_of_income_records.edit',
            'update' => 'source_of_income_records.update',
            'destroy' => 'source_of_income_records.destroy',
        ])->parameters([
            'source-of-income-records' => 'sourceOfIncomeRecord'
        ]);
        
        // Income Beneficiary Records routes
        Route::resource('income-beneficiary-records', IncomeBeneficiaryRecordController::class)->names([
            'index' => 'income_beneficiary_records.index',
            'create' => 'income_beneficiary_records.create',
            'store' => 'income_beneficiary_records.store',
            'show' => 'income_beneficiary_records.show',
            'edit' => 'income_beneficiary_records.edit',
            'update' => 'income_beneficiary_records.update',
            'destroy' => 'income_beneficiary_records.destroy',
        ])->parameters([
            'income-beneficiary-records' => 'incomeBeneficiaryRecord'
        ]);
        
        // Human Resource Records routes
        Route::resource('human-resource-records', HumanResourceRecordController::class)->names([
            'index' => 'human_resource_records.index',
            'create' => 'human_resource_records.create',
            'store' => 'human_resource_records.store',
            'show' => 'human_resource_records.show',
            'edit' => 'human_resource_records.edit',
            'update' => 'human_resource_records.update',
            'destroy' => 'human_resource_records.destroy',
        ])->parameters([
            'human-resource-records' => 'humanResourceRecord'
        ]);
    });
    
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
        
        // Problem Animal Control Routes
        Route::get('problem-animal-controls', [ProblemAnimalControlController::class, 'index'])->name('problem-animal-controls.index');
        Route::get('problem-animal-controls/create', [ProblemAnimalControlController::class, 'create'])->name('problem-animal-controls.create');
        Route::post('problem-animal-controls', [ProblemAnimalControlController::class, 'store'])->name('problem-animal-controls.store');
        Route::get('problem-animal-controls/{problemAnimalControl}', [ProblemAnimalControlController::class, 'show'])->name('problem-animal-controls.show');
        Route::get('problem-animal-controls/{problemAnimalControl}/edit', [ProblemAnimalControlController::class, 'edit'])->name('problem-animal-controls.edit');
        Route::patch('problem-animal-controls/{problemAnimalControl}', [ProblemAnimalControlController::class, 'update'])->name('problem-animal-controls.update');
        Route::delete('problem-animal-controls/{problemAnimalControl}', [ProblemAnimalControlController::class, 'destroy'])->name('problem-animal-controls.destroy');
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

// Add test route for chart
Route::get('/test-chart', function() {
    return view('test-chart');
});

Route::get('{organisation}/test-chart', [App\Http\Controllers\Organisation\OrganisationDashboardController::class, 'testChart'])
    ->name('organisation.dashboard.test-chart');

require __DIR__ . '/auth.php';
