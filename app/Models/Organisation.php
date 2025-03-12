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
