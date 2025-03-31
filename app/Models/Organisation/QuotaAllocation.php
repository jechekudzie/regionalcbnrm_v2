<?php

namespace App\Models\Organisation;

use App\Models\Admin\Organisation;
use App\Models\Organisation\Species;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuotaAllocation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organisation_id',
        'species_id',
        'hunting_quota',
        'rational_killing_quota',
        'start_date',
        'end_date',
        'period',
        'notes'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function organisation()
    {
        return $this->belongsTo(\App\Models\Admin\Organisation::class);
    }

    public function species()
    {
        return $this->belongsTo(\App\Models\Organisation\Species::class);
    }
}
