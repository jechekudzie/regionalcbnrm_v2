<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use App\Models\Organisation\Species;

class SpeciesGender extends Model
{
    use HasFactory,HasSlug;

    protected $guarded = [];

        

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
