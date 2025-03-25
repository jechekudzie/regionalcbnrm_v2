<?php

namespace App\Models\HistoricalData;

use App\Models\Organisation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SourceOfIncomeRecord extends Model
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
        'trophy_fee_amount',
        'hides_amount',
        'meat_amount',
        'hunting_concession_fee_amount',
        'photographic_fee_amount',
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
        'trophy_fee_amount' => 'decimal:2',
        'hides_amount' => 'decimal:2',
        'meat_amount' => 'decimal:2',
        'hunting_concession_fee_amount' => 'decimal:2',
        'photographic_fee_amount' => 'decimal:2',
        'other_amount' => 'decimal:2',
    ];

    /**
     * Get the organisation that owns the source of income record.
     */
    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    /**
     * Get the total amount of income from all sources.
     *
     * @return float
     */
    public function getTotalAmountAttribute()
    {
        return $this->trophy_fee_amount +
               $this->hides_amount +
               $this->meat_amount +
               $this->hunting_concession_fee_amount +
               $this->photographic_fee_amount +
               $this->other_amount;
    }
} 