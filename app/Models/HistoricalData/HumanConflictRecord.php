<?php

namespace App\Models\HistoricalData;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HumanConflictRecord extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'organisation_id',
        'species_id',
        'gender_id',
        'deaths',
        'injured',
        'period',
    ];

    /**
     * Get the organisation that owns the human conflict record.
     */
    public function organisation()
    {
        return $this->belongsTo(\App\Models\Admin\Organisation::class);
    }

    /**
     * Get the species associated with the human conflict record.
     */
    public function species()
    {
        return $this->belongsTo(\App\Models\Organisation\Species::class);
    }

    /**
     * Get the gender associated with the human conflict record.
     */
    public function gender()
    {
        return $this->belongsTo(\App\Models\Admin\Gender::class);
    }
}
