<?php

namespace App\Models\Organisation;

use App\Models\Admin\Organisation;
use App\Models\Organisation\Species;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HuntingActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'organisation_id',
        'hunting_concession_id',
        'safari_id',
        'start_date',
        'end_date',
        'period'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date'
    ];

    public function organisation(): BelongsTo
    {
        return $this->belongsTo(Organisation::class);
    }

    public function huntingConcession(): BelongsTo
    {
        return $this->belongsTo(HuntingConcession::class);
    }

    public function safariOperator(): BelongsTo
    {
        return $this->belongsTo(Organisation::class, 'safari_id');
    }

    public function species(): BelongsToMany
    {
        return $this->belongsToMany(Species::class, 'hunting_activity_species')
            ->withPivot('off_take')
            ->withTimestamps();
    }

    public function professionalHunterLicenses(): HasMany
    {
        return $this->hasMany(HuntingActivityProfessionalHunterLicense::class);
    }

    public function quotaAllocationBalances(): HasMany
    {
        return $this->hasMany(QuotaAllocationBalance::class);
    }
}
