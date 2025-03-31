<?php

namespace App\Models\Admin;

use App\Models\Admin\Hunter;
use App\Models\Admin\HuntingActivity;
use App\Models\Admin\HuntingLicense;
use App\Models\Admin\Incident;
use App\Models\Admin\OrganisationType;
use App\Models\Admin\PoachingIncident;
use App\Models\Admin\PopulationEstimate;
use App\Models\Admin\ProblemAnimalControl;
use App\Models\Admin\QuotaRequest;
use App\Models\Admin\RevenueSharing;
use App\Models\Admin\WardQuotaDistribution;
use App\Models\HistoricalData\AnimalControlRecord;
use App\Models\HistoricalData\ConflictRecord;
use App\Models\HistoricalData\CropConflictRecord;
use App\Models\HistoricalData\HumanConflictRecord;
use App\Models\HistoricalData\HuntingRecord;
use App\Models\HistoricalData\IncomeRecord;
use App\Models\HistoricalData\LiveStockConflictRecord;
use App\Models\HistoricalData\PoachersRecord;
use App\Models\HistoricalData\PoachingRecord;
use App\Models\Organisation\HuntingConcession;
use App\Models\Organisation\OrganisationPayableItem;
use App\Models\Organisation\QuotaAllocation;
use App\Models\Organisation\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Organisation extends Model
{
    use HasFactory, HasSlug;

    protected $guarded = [];


    public function organisationType()
    {
        return $this->belongsTo(\App\Models\Admin\OrganisationType::class);
    }

    //has many organisations
    public function organisations()
    {
        return $this->hasMany(\App\Models\Admin\Organisation::class);
    }


    public function parentOrganisation()
    {
        return $this->belongsTo(\App\Models\Admin\Organisation::class, 'organisation_id');
    }

    public function childOrganisations()
    {
        return $this->hasMany(\App\Models\Admin\Organisation::class, 'organisation_id');
    }

    public function getSafariOperators()
    {
        return $this->childOrganisations()
            ->whereHas('organisationType', function ($query) {
                $query->whereRaw('lower(name) = ?', ['safari operators']);
            })->get();
    }

    //get the first group of child organisations
    public function firstGroupOfChildOrganisations()
    {
        // Get all child organizations
        $childOrganizations = $this->hasMany(\App\Models\Admin\Organisation::class, 'organisation_id')->get();

        // Group the child organizations by organisation_type_id
        $groupedOrganizations = $childOrganizations->groupBy('organisation_type_id');

        // Get the first group
        $firstGroup = $groupedOrganizations->skip(1)->first();

        return $firstGroup;
    }

    //get any group of child organisations  
    public function anyGroupOfChildOrganisations()
    {
        // Get all child organizations
        $childOrganizations = $this->hasMany(\App\Models\Admin\Organisation::class, 'organisation_id')->get();

        // Group the child organizations by organisation_type_id
        $groupedOrganizations = $childOrganizations->groupBy('organisation_type_id');

        // Get the second group using skip(1) or any other index
        $anyGroup = $groupedOrganizations->skip(1)->first();

        return $anyGroup;
    }

    //get all children
    public function getAllChildren()
    {
        $children = [];

        foreach ($this->childOrganisations as $child) {
            $children[] = $child;
            $children = array_merge($children, $child->getAllChildren());
        }

        return $children;
    }

    //get all hunting concessions
    public function huntingConcessions() // When the organisation is an RDC
    {
        return $this->hasMany(\App\Models\Organisation\HuntingConcession::class);
    }

    public function huntingActivities()
    {
        return $this->hasMany(\App\Models\Organisation\HuntingActivity::class);
    }

    public function organisationPayableItems()
    {
        return $this->hasMany(\App\Models\Organisation\OrganisationPayableItem::class);
    }

    // Add this method to define the relationship with Transactions
    public function transactions()
    {
        return $this->hasMany(\App\Models\Organisation\Transaction::class);
    }

    //hunting records
    public function huntingRecords()
    {
        return $this->hasMany(\App\Models\HistoricalData\HuntingRecord::class);
    }

    public function conflictRecords()
    {
        return $this->hasMany(\App\Models\HistoricalData\ConflictRecord::class);
    }

    //for historical data
    public function cropConflictRecords()
    {
        return $this->hasMany(\App\Models\HistoricalData\CropConflictRecord::class);
    }

    public function liveStockConflictRecords()
    {
        return $this->hasMany(\App\Models\HistoricalData\LiveStockConflictRecord::class);
    }

    public function humanConflictRecords()
    {
        return $this->hasMany(\App\Models\HistoricalData\HumanConflictRecord::class);
    }

    public function quotaAllocations()
    {
        return $this->hasMany(\App\Models\Organisation\QuotaAllocation::class);
    }

    public function animalControlRecords()
    {
        return $this->hasMany(\App\Models\HistoricalData\AnimalControlRecord::class);
    }

    public function poachingRecords()
    {
        return $this->hasMany(\App\Models\HistoricalData\PoachingRecord::class);
    }

    public function poachersRecords()
    {
        return $this->hasMany(\App\Models\HistoricalData\PoachersRecord::class);
    }

    public function controlCases()
    {
        return $this->hasMany(\App\Models\Admin\ControlCase::class);
    }

    //has many income records
    public function incomeRecords()
    {
        return $this->hasMany(\App\Models\HistoricalData\IncomeRecord::class);
    }


    //belongs to many users
    public function users()
    {
        return $this->belongsToMany(\App\Models\User::class, 'organisation_users')
            ->withPivot('role_id');
    }

    //has many roles
    public function organisationRoles()
    {
        return $this->hasMany(\Spatie\Permission\Models\Role::class, 'organisation_id');
    }

    public function availablePermissions()
    {
        return $this->belongsToMany(\Spatie\Permission\Models\Permission::class, 'organisation_permissions');
    }


    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }


    public function getRouteKeyName()
    {
        return 'slug';
    }
}
