<?php

namespace Database\Seeders;

use App\Models\Admin\CropType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CropTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cropTypes = [
            [
                'name' => 'Maize',
                'description' => 'Corn/Maize crops'
            ],
            [
                'name' => 'Rice',
                'description' => 'Rice crops'
            ],
            [
                'name' => 'Wheat',
                'description' => 'Wheat crops'
            ],
            [
                'name' => 'Sorghum',
                'description' => 'Sorghum crops'
            ],
            [
                'name' => 'Millet',
                'description' => 'Millet crops'
            ],
            [
                'name' => 'Beans',
                'description' => 'Bean crops including common beans, cowpeas, etc.'
            ],
            [
                'name' => 'Groundnuts',
                'description' => 'Peanut/Groundnut crops'
            ],
            [
                'name' => 'Cassava',
                'description' => 'Cassava/Manioc crops'
            ],
            [
                'name' => 'Sweet Potato',
                'description' => 'Sweet potato crops'
            ],
            [
                'name' => 'Cotton',
                'description' => 'Cotton crops'
            ],
            [
                'name' => 'Tobacco',
                'description' => 'Tobacco crops'
            ],
            [
                'name' => 'Vegetables',
                'description' => 'Various vegetable crops'
            ],
            [
                'name' => 'Fruits',
                'description' => 'Various fruit crops'
            ],
            [
                'name' => 'Other',
                'description' => 'Other crop types not listed'
            ],
        ];

        foreach ($cropTypes as $cropType) {
            CropType::create($cropType);
        }
    }
}
