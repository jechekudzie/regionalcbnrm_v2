<?php

namespace App\Models\Organisation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DynamicFieldOption extends Model
{
    use HasFactory;

    protected $guarded = [];

    //dynamicField
    public function dynamicField()
    {
        return $this->belongsTo(\App\Models\Organisation\DynamicField::class, 'dynamic_field_id');
    }

}
