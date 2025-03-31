<?php

namespace App\Models\Organisation;

use App\Models\Admin\Organisation;
use App\Models\Admin\ControlCase;
use App\Models\Admin\Incident;
use App\Models\Admin\PACDetail;
use App\Models\Admin\PopulationEstimate;
use App\Models\Admin\PoachingIncident;
use App\Models\Admin\QuotaRequest;
use App\Models\HistoricalData\AnimalControlRecord;
use App\Models\HistoricalData\ConflictRecord;
use App\Models\HistoricalData\CropConflictRecord;
use App\Models\HistoricalData\HumanConflictRecord;
use App\Models\HistoricalData\HuntingRecord;
use App\Models\HistoricalData\LiveStockConflictRecord;
use App\Models\HistoricalData\PoachersRecord;
use App\Models\HistoricalData\PoachingRecord;
use App\Models\Organisation\HuntingDetail;
use App\Models\Organisation\OrganisationPayableItem;
use App\Models\Organisation\TransactionPayable;
use App\Models\Organisation\QuotaAllocation;
use App\Models\Organisation\HuntingActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Species extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = [
        'name',
        'scientific_name',
        'description',
        'category',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

  

    public function getEstimateCount($maturityId, $genderId, $year)
    {
        return $this->populationEstimates()
            ->where('maturity_id', $maturityId)
            ->where('species_gender_id', $genderId)
            ->where('year', $year)
            ->sum('estimate');
    }


    // Species-specific pricing for Payable Items within Organisations
    public function payableItems()
    {
        return $this->belongsToMany(\App\Models\Organisation\OrganisationPayableItem::class, 'organisation_payable_item_species', 'species_id', 'organisation_payable_item_id')
            ->withPivot('price');
    }

    public function transactionPayables()
    {
        return $this->belongsToMany(\App\Models\Organisation\TransactionPayable::class, 'transaction_payable_species', 'species_id', 'transaction_payable_id')
            ->withPivot('price');
    }



    public function huntingRecords()
    {
        return $this->hasMany(\App\Models\HistoricalData\HuntingRecord::class);
    }

    public function conflictRecords()
    {
        return $this->hasMany(\App\Models\HistoricalData\ConflictRecord::class);
    }

    public function cropConflictRecords()
    {
        return $this->hasMany(\App\Models\HistoricalData\CropConflictRecord::class);
    }

    public function liveStockConflictRecords()
    {
        return $this->hasMany(\App\Models\HistoricalData\LiveStockConflictRecord::class);
    }

    public function humanConflictRecords()
    {
        return $this->hasMany(\App\Models\HistoricalData\HumanConflictRecord::class);
    }

    public function animalControlRecords()
    {
        return $this->hasMany(\App\Models\HistoricalData\AnimalControlRecord::class);
    }

    public function poachingRecords()
    {
        return $this->hasMany(\App\Models\HistoricalData\PoachingRecord::class);
    }

    public function poachersRecords()
    {
        return $this->hasMany(\App\Models\HistoricalData\PoachersRecord::class);
    }

    public function controlCases()
    {
        return $this->hasMany(\App\Models\Admin\ControlCase::class);
    }

    public function quotaAllocations()
    {
        return $this->hasMany(QuotaAllocation::class);
    }

    public function huntingActivities()
    {
        return $this->belongsToMany(HuntingActivity::class, 'hunting_activity_species')
            ->withPivot('off_take')
            ->withTimestamps();
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
