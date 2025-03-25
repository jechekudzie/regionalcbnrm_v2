<?php

namespace App\Models\HistoricalData;

use App\Models\Organisation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncomeUseRecord extends Model
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
        'administration_amount',
        'management_activities_amount',
        'social_services_amount',
        'law_enforcement_amount',
        'other_amount',
        'other_description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'period' => 'integer',
        'administration_amount' => 'decimal:2',
        'management_activities_amount' => 'decimal:2',
        'social_services_amount' => 'decimal:2',
        'law_enforcement_amount' => 'decimal:2',
        'other_amount' => 'decimal:2',
    ];

    /**
     * Get the organisation that owns the income use record.
     */
    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    /**
     * Get the total amount of income used.
     *
     * @return float
     */
    public function getTotalAmountAttribute()
    {
        return $this->administration_amount +
               $this->management_activities_amount +
               $this->social_services_amount +
               $this->law_enforcement_amount +
               $this->other_amount;
    }
}
