<?php

namespace App\Models\Organisation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HuntingActivityProfessionalHunterLicense extends Model
{
    use HasFactory;

    protected $fillable = [
        'hunting_activity_id',
        'license_number',
        'hunter_name'
    ];

    public function huntingActivity(): BelongsTo
    {
        return $this->belongsTo(HuntingActivity::class);
    }
} 