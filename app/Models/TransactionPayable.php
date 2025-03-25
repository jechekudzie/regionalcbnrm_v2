<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionPayable extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function payableItem()
    {
        return $this->belongsTo(PayableItem::class);
    }

    //organisation payable item
    public function organisationPayableItem()
    {
        return $this->belongsTo(OrganisationPayableItem::class);
    }
    public function species()
    {
        return $this->belongsToMany(Species::class, 'transaction_payable_species', 'transaction_payable_id', 'species_id')
            ->withPivot('price');
    }

}
