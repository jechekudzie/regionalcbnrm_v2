<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class PoachingIncident extends Model
{
    use HasFactory,HasSlug;

    protected $guarded = [];

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function species()
    {
        return $this->belongsToMany(Species::class, 'poaching_incident_species', 'poaching_incident_id', 'species_id')
            ->withPivot('male_count', 'female_count');
    }

    public function poachingMethods()
    {
        return $this->belongsToMany(PoachingMethod::class, 'incident_poaching_methods');
    }

    public function poachers()
    {
        return $this->hasMany(Poacher::class);
    }

    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }


    public function getRouteKeyName()
    {
        return 'slug';
    }
}
