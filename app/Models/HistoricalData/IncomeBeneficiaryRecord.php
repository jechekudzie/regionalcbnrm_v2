<?php

namespace App\Models\HistoricalData;

use App\Models\Organisation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomeBeneficiaryRecord extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'organisation_id',
        'period',
        'households',
        'males',
        'females',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'period' => 'integer',
        'households' => 'integer',
        'males' => 'integer',
        'females' => 'integer',
    ];

    /**
     * Get the organisation that owns the income beneficiary record.
     */
    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    /**
     * Get the total number of beneficiaries (males + females).
     *
     * @return int
     */
    public function getTotalBeneficiariesAttribute()
    {
        return $this->males + $this->females;
    }
}
