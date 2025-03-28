<?php

namespace App\Models;

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
        return $this->belongsTo(OrganisationType::class);
    }

    //has many organisations
    public function organisations()
    {
        return $this->hasMany(Organisation::class);
    }


    public function parentOrganisation()
    {
        return $this->belongsTo(Organisation::class, 'organisation_id');
    }

    public function childOrganisations()
    {
        return $this->hasMany(Organisation::class, 'organisation_id');
    }

    public function firstGroupOfChildOrganisations()
    {
        // Get all child organizations
        $childOrganizations = $this->hasMany(Organisation::class, 'organisation_id')->get();

        // Group the child organizations by organisation_type_id
        $groupedOrganizations = $childOrganizations->groupBy('organisation_type_id');

        // Get the first group
        $firstGroup = $groupedOrganizations->skip(1)->first();

        return $firstGroup;
    }

    public function anyGroupOfChildOrganisations()
    {
        // Get all child organizations
        $childOrganizations = $this->hasMany(Organisation::class, 'organisation_id')->get();

        // Group the child organizations by organisation_type_id
        $groupedOrganizations = $childOrganizations->groupBy('organisation_type_id');

        // Get the second group using skip(1) or any other index
        $anyGroup = $groupedOrganizations->skip(1)->first();

        return $anyGroup;
    }


    public function getAllChildren()
    {
        $children = [];

        foreach ($this->childOrganisations as $child) {
            $children[] = $child;
            $children = array_merge($children, $child->getAllChildren());
        }

        return $children;
    }


    //belongs to many users
    public function users()
    {
        return $this->belongsToMany(User::class, 'organisation_users')
            ->withPivot('role_id');
    }

    //has many roles
    public function organisationRoles()
    {
        return $this->hasMany(Role::class, 'organisation_id');
    }

    public function availablePermissions()
    {
        return $this->belongsToMany(Permission::class, 'organisation_permissions');
    }

    //has many population estimates
    public function populationEstimates()
    {
        return $this->hasMany(PopulationEstimate::class, 'conducted_by');
    }

    public function quotaRequests()
    {
        return $this->hasMany(QuotaRequest::class, 'organisation_id');// for RDCs
    }

    public function wardQuotaDistributions()
    {
        return $this->hasMany(WardQuotaDistribution::class, 'ward_id');
    }

    //hunting clients
    public function hunters()
    {
        return $this->belongsToMany(Hunter::class, 'organisation_hunter');//safari hunters
    }

    public function huntingActivitiesAsSafariOperator() // When the organisation is a Safari Operator
    {
        return $this->hasMany(HuntingActivity::class);
    }

    public function huntingConcessions() // When the organisation is an RDC
    {
        return $this->hasMany(HuntingConcession::class);
    }


    public function huntingLicenses() // Licenses held or issued by the organisation
    {
        return $this->hasMany(HuntingLicense::class);
    }

    //has many incidents
    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }

    //has many problem animal controls
    public function problemAnimalControls()
    {
        return $this->hasMany(ProblemAnimalControl::class);
    }

    //has many poaching incidents
    public function poachingIncidents()
    {
        return $this->hasMany(PoachingIncident::class);
    }

    public function organisationPayableItems()
    {
        return $this->hasMany(OrganisationPayableItem::class);
    }


    // Add this method to define the relationship with Transactions
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function revenueSharings()
    {
        return $this->hasMany(RevenueSharing::class);
    }


    //hunting records
    public function huntingRecords()
    {
        return $this->hasMany(HistoricalData\HuntingRecord::class);
    }

    public function conflictRecords()
    {
        return $this->hasMany(HistoricalData\ConflictRecord::class);
    }

    //for historical data
    public function cropConflictRecords()
    {
        return $this->hasMany(HistoricalData\CropConflictRecord::class);
    }

    public function liveStockConflictRecords()
    {
        return $this->hasMany(HistoricalData\LiveStockConflictRecord::class);
    }

    public function humanConflictRecords()
    {
        return $this->hasMany(HistoricalData\HumanConflictRecord::class);
    }

    public function animalControlRecords()
    {
        return $this->hasMany(HistoricalData\AnimalControlRecord::class);
    }

    public function poachingRecords()
    {
        return $this->hasMany(HistoricalData\PoachingRecord::class);
    }

    public function poachersRecords()
    {
        return $this->hasMany(HistoricalData\PoachersRecord::class);
    }

    public function controlCases()
    {
        return $this->hasMany(ControlCase::class);
    }

    //has many income records
    public function incomeRecords()
    {
        return $this->hasMany(IncomeRecord::class);
    }


    //has many projects
    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function getSafariOperators()
    {
        return $this->childOrganisations()
            ->whereHas('organisationType', function ($query) {
                $query->whereRaw('lower(name) = ?', ['safari operators']);
            })->get();
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
