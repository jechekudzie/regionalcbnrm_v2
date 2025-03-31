<?php

namespace App\Models\Organisation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class DynamicField extends Model
{
    use HasFactory,HasSlug;

    protected $guarded = [];

    //ConflictOutCome
    public function conflictOutCome()
    {
        return $this->belongsTo(\App\Models\Admin\ConflictOutCome::class);
    }

    //dynamicFieldOptions
    public function options()
    {
        return $this->hasMany(\App\Models\Organisation\DynamicFieldOption::class);
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
