<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class HuntingConcession extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'name',
        'organisation_id',
        'safari_id',
        'hectarage',
        'description',
        'latitude',
        'longitude',
        'slug',
    ];

    public function organisation() // The RDC managing this concession
    {
        return $this->belongsTo(Organisation::class);
    }

    public function safari()
    {
        return $this->belongsTo(Safari::class);
    }

    public function safariOperator()
    {
        return $this->belongsTo(Organisation::class,'safari_id');
    }

    public function wards() // Wards covered by the concession
    {
        return $this->belongsToMany(Organisation::class, 'hunting_concession_ward', 'hunting_concession_id', 'ward_id');
    }

    // Total quotas for a given species and year
    public function calculateTotalQuotasForSpeciesByYear($speciesId, $year)
    {
        $totalQuotas = ['hunting_quota' => 0, 'pac_quota' => 0, 'rational_quota' => 0];

        foreach ($this->wards as $ward) {
            // Adjust the query to also filter by the year of the quota request
            $distributions = $ward->wardQuotaDistributions()->whereHas('quotaRequest', function ($query) use ($speciesId, $year) {
                $query->where('species_id', $speciesId)->where('year', $year);
            })->get();

            foreach ($distributions as $distribution) {
                $totalQuotas['hunting_quota'] += $distribution->hunting_quota;
                $totalQuotas['pac_quota'] += $distribution->pac_quota;
                $totalQuotas['rational_quota'] += $distribution->rational_quota;
            }
        }

        return $totalQuotas;
    }


    // Total quotas for a given species
    public function calculateTotalQuotasForSpecies($speciesId)
    {
        $totalQuotas = ['hunting_quota' => 0, 'pac_quota' => 0, 'rational_quota' => 0];

        foreach ($this->wards as $ward) {
            $distributions = $ward->wardQuotaDistributions()->whereHas('quotaRequest', function ($query) use ($speciesId) {
                $query->where('species_id', $speciesId);
            })->get();

            foreach ($distributions as $distribution) {
                $totalQuotas['hunting_quota'] += $distribution->hunting_quota;
                $totalQuotas['pac_quota'] += $distribution->pac_quota;
                $totalQuotas['rational_quota'] += $distribution->rational_quota;
            }
        }

        return $totalQuotas;
    }

    public function huntingActivities() // Activities taking place in this concession
    {
        return $this->hasMany(HuntingActivity::class);
    }

    //hunting Detail
    public function huntingDetails()
    {
        return $this->hasMany(HuntingDetail::class);
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }


}
