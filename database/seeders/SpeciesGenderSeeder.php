<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SpeciesGenderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $speciesGender = ['Male', 'Female', 'Unsexed'];

        foreach ($speciesGender as $gender) {
            \App\Models\Admin\SpeciesGender::create([
                'name' => $gender,
            ]);
        }

    }
}
