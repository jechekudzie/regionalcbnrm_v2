<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Admin\Organisation;
use App\Models\DynamicFieldOption;
use App\Models\ConflictOutcome;

class DynamicField extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'organisation_id',
        'conflict_outcome_id',
        'field_name',
        'field_type',
        'label',
        'slug',
    ];

    /**
     * Get the organisation that owns the dynamic field.
     */
    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    /**
     * Get the conflict outcome that owns the dynamic field.
     */
    public function conflictOutcome()
    {
        return $this->belongsTo(ConflictOutcome::class, 'conflict_outcome_id', 'id');
    }

    /**
     * Get the options for this dynamic field.
     */
    public function options()
    {
        return $this->hasMany(DynamicFieldOption::class);
    }
} 