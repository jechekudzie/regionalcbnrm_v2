<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\PayableItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PayableItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $payableItems = [
            'Wildlife Conservation Efforts' => [
                'Trophy Fee', 'Hides', 'Daily Rates', 'Meat', 'Ivory', 'Live Sales', 'Traditional Uses', 'Egg Sales'
            ],
            'Eco-tourism Activities' => [
                'Wildlife Safaris', 'Bird Watching', 'Hiking and Trekking', 'Snorkeling and Diving',
                'Kayaking and Canoeing', 'Responsible Camping', 'Volunteer Conservation Projects',
                'Cultural Tours', 'Eco-Lodges and Sustainable Accommodations', 'Agricultural Tours',
                'Educational Workshops and Seminars', 'Biking Tours', 'Photography Expeditions',
                'Eco-friendly Skiing and Snowboarding', 'Zip-lining and Canopy Tours'
            ],
            'Eco-Funding Mechanisms' => [
                'Grants and Donor Funds', 'Corporate Social Responsibility (CSR) Programs',
                'Environmental Fines and Penalties', 'Green Bonds and Environmental Impact Investments'
            ]
        ];

        foreach ($payableItems as $categoryName => $items) {
            $category = Category::where('name', $categoryName)->first();
            if ($category) {
                foreach ($items as $itemName) {
                    PayableItem::create([
                        'category_id' => $category->id,
                        'name' => $itemName,
                        'description' => "{$itemName} description" // Customize this as needed
                    ]);
                }
            }
        }
    }
}
