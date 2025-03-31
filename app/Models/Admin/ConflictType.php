<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class ConflictType extends Model
{
    use HasFactory, HasSlug;

    protected $guarded = [];

    public function incidents()
    {
        // Custom pivot table and column names
        return $this->belongsToMany(Incident::class, 'incident_conflict_type', 'conflict_type_id', 'incident_id');
    }

    public function ConflictOutComes()
    {
        return $this->hasMany(ConflictOutCome::class);
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
