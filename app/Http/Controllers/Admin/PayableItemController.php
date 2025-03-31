<?php

namespace App\Http\Controllers;

use App\Models\PayableItem;
use Illuminate\Http\Request;

class PayableItemController extends Controller
{
    //index
    public function index()
    {
        $payableItems = PayableItem::all();
        return view('administration.payable_items.index', compact('payableItems'));
    }
}
