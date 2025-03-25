<?php

namespace Database\Seeders;

use App\Models\Shot;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShotsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $shots = [
            [
                'name' => 'Broadside Shot',
                'description' => 'The animal is standing perpendicular to the hunter, offering a clear shot at the vital organs. This shot can quickly incapacitate the animal, leading to a swift demise.',
            ],
            [
                'name' => 'Quartering-Away Shot',
                'description' => 'The animal is slightly turned away, exposing the vitals from an angle. The hunter aims for a point where the bullet or arrow can penetrate the ribcage and reach the heart or lungs.',
            ],
            [
                'name' => 'Frontal Shot',
                'description' => 'Aiming for the central chest area, where the bullet or arrow can penetrate to reach the heart or major blood vessels. This shot is typically taken at closer ranges and requires precision to be effective.',
            ],
            [
                'name' => 'Neck Shot',
                'description' => 'Aiming for the neck can disrupt the spinal cord or major blood vessels, leading to rapid incapacitation. However, this shot requires precision due to the smaller target area and the risk of merely wounding the animal.',
            ],
            [
                'name' => 'Head Shot',
                'description' => 'This is the most controversial and least recommended shot due to the high risk of wounding the animal without causing immediate death. The target area is very small, and even a slight miscalculation can lead to a non-lethal injury.',
            ],
            // Add more shot types or details as necessary
        ];

        foreach ($shots as $shot) {
            Shot::create($shot);
        }
    }
}
