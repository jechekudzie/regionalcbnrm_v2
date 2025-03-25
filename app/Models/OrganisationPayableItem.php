<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganisationPayableItem extends Model
{
    use HasFactory;

    protected $guarded = [];
    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function payableItem()
    {
        return $this->belongsTo(PayableItem::class);
    }

    //has many transaction payable items
    public function transactionPayableItems()
    {
        return $this->hasMany(TransactionPayable::class);
    }
    public function species()
    {
        return $this->belongsToMany(Species::class, 'organisation_payable_item_species', 'organisation_payable_item_id', 'species_id')
            ->withPivot('price');
    }


}
