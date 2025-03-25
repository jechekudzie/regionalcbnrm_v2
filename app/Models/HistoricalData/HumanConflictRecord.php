<?php

namespace App\Models\HistoricalData;

use App\Models\Gender;
use App\Models\Organisation;
use App\Models\Species;
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
        return $this->belongsTo(Organisation::class);
    }

    /**
     * Get the species associated with the human conflict record.
     */
    public function species()
    {
        return $this->belongsTo(Species::class);
    }

    /**
     * Get the gender associated with the human conflict record.
     */
    public function gender()
    {
        return $this->belongsTo(Gender::class);
    }
}
