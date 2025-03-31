<?php

namespace App\Models\Organisation;

use App\Models\Admin\Organisation;
use App\Models\Organisation\Species;
use App\Models\Organisation\QuotaAllocation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuotaAllocationBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'organisation_id',
        'species_id',
        'period',
        'quota_allocation_id',
        'allocated_quota',
        'total_off_take',
        'remaining_quota'
    ];

    protected $casts = [
        'allocated_quota' => 'integer',
        'total_off_take' => 'integer',
        'remaining_quota' => 'integer',
    ];

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    public function species(): BelongsTo
    {
        return $this->belongsTo(Species::class);
    }

    public function quotaAllocation(): BelongsTo
    {
        return $this->belongsTo(QuotaAllocation::class);
    }
}
