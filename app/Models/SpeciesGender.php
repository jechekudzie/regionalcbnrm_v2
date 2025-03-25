<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class SpeciesGender extends Model
{
    use HasFactory,HasSlug;

    protected $guarded = [];

    public function populationEstimates()
    {
        return $this->hasMany(PopulationEstimate::class);
    }

    public function species()
    {
        return $this->hasMany(Species::class, 'species_gender_id');
    }

    //has many poaching

    public function getSlugOptions() : SlugOptions
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
