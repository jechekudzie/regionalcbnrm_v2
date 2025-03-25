<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Organisation;
use App\Models\OrganisationPayableItem;
use App\Models\PayableItem;
use Illuminate\Http\Request;

class OrganisationPayableItemController extends Controller
{

    //paymentCategories
    public function payableItemCategories(Organisation $organisation)
    {
        $categories = Category::all();
        return view('organisation.payable_items_categories.index', compact('categories','organisation'));
    }
    //index
    public function index(Organisation $organisation,Category $category)
    {
        $payableItems = $category->payableItems;

        return view('organisation.payable_items.index', compact('payableItems','organisation','category'));
    }

    public function store(Request $request, Organisation $organisation,Category $category)
    {
        $validated = $request->validate([
            'payable_item_id' => 'required|exists:payable_items,id',
            'species_id' => 'nullable|exists:species,id',
            'price' => 'nullable|numeric',
        ]);

        // Create an OrganisationPayableItem (possibly with null price if species_id is provided)
        $organisationPayableItem = $organisation->organisationPayableItems()->create([
            'payable_item_id' => $validated['payable_item_id'],
            'price' => $validated['species_id'] ? null : $validated['price'],  // Null price if species is specified
        ]);

        // If species_id is provided, attach the OrganisationPayableItem to the species with the price
        if (!empty($validated['species_id'])) {
            $organisationPayableItem->species()->attach($validated['species_id'], ['price' => $validated['price']]);
        }

        // Redirect or return response
        return back()->with('success', 'Organisation payable item created successfully.');
    }

}
