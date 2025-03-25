<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $categories = [
            [
                'name' => 'Wildlife Conservation Efforts',
                'description' => 'Focused on preserving wildlife and their habitats through various conservation practices.'
            ],
            [
                'name' => 'Eco-tourism Activities',
                'description' => 'Promotes responsible travel to natural areas that conserves the environment and improves the well-being of local people.'
            ],
            [
                'name' => 'Eco-Funding Mechanisms',
                'description' => 'Financial strategies and sources designed to support environmental projects and conservation initiatives.'
            ]
        ];

        foreach ($categories as $categoryData) {
            Category::create($categoryData);
        }
    }
}
