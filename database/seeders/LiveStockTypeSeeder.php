<?php

namespace Database\Seeders;

use App\Models\LiveStockType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LiveStockTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $liveStockTypes = [
            [
                'name' => 'Cattle',
                'description' => 'Cows, bulls, and calves'
            ],
            [
                'name' => 'Goats',
                'description' => 'Domestic goats'
            ],
            [
                'name' => 'Sheep',
                'description' => 'Domestic sheep'
            ],
            [
                'name' => 'Chickens',
                'description' => 'Domestic chickens and roosters'
            ],
            [
                'name' => 'Pigs',
                'description' => 'Domestic pigs'
            ],
            [
                'name' => 'Donkeys',
                'description' => 'Domestic donkeys'
            ],
            [
                'name' => 'Horses',
                'description' => 'Domestic horses'
            ],
            [
                'name' => 'Camels',
                'description' => 'Domestic camels'
            ],
            [
                'name' => 'Ducks',
                'description' => 'Domestic ducks'
            ],
            [
                'name' => 'Turkeys',
                'description' => 'Domestic turkeys'
            ],
            [
                'name' => 'Guinea Fowl',
                'description' => 'Domestic guinea fowl'
            ],
            [
                'name' => 'Rabbits',
                'description' => 'Domestic rabbits'
            ],
            [
                'name' => 'Other',
                'description' => 'Other livestock types not listed'
            ],
        ];

        foreach ($liveStockTypes as $liveStockType) {
            LiveStockType::create($liveStockType);
        }
    }
}
