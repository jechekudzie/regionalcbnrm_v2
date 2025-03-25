<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\Fluent\Concerns\Has;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Province extends Model
{

    use HasSlug;
    protected $guarded = [];

    //poachers
    public function poachers()
    {
        return $this->hasMany(Poacher::class);
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
