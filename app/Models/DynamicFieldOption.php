<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\DynamicField;

class DynamicFieldOption extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'dynamic_field_id',
        'option_value',
        'option_label',
    ];

    /**
     * Get the dynamic field that owns this option.
     */
    public function dynamicField()
    {
        return $this->belongsTo(DynamicField::class);
    }
} 