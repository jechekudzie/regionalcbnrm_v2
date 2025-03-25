<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class DynamicFieldOption extends Model
{
    use HasFactory;

    protected $guarded = [];

    //dynamicField
    public function dynamicField()
    {
        return $this->belongsTo(DynamicField::class, 'dynamic_field_id');
    }

}
