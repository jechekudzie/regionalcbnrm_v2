<?php

namespace Database\Seeders;

use App\Models\CountingMethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CountingMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $methods = [
            ['name' => 'Stool (Dung) Counts', 'description' => 'Estimating population size by counting feces. Used where direct observation is difficult.'],
            ['name' => 'Aerial Surveys', 'description' => 'Counting animals from aircraft over large areas. Suitable for large, visible species.'],
            ['name' => 'Ground Surveys', 'description' => 'Direct observation and counting on foot or by vehicle. Used in smaller areas.'],
            ['name' => 'Track Counts', 'description' => 'Identifying and counting animal tracks to indicate species presence and movement.'],
            ['name' => 'MOMS (Management Oriented Monitoring System)', 'description' => 'Community-based monitoring involving local members in data collection for management decisions.'],
            ['name' => 'Satellite Imagery', 'description' => 'Utilizing satellite images for counting and monitoring wildlife populations in remote areas.'],
            ['name' => 'Drone Surveys', 'description' => 'Employing drones with cameras for wildlife surveys in hard-to-reach areas.'],
            ['name' => 'Call Counts', 'description' => 'Counting animal calls, often used for bird population surveys.'],
            ['name' => 'Camera Traps', 'description' => 'Automated cameras capture images/videos of passing animals. Effective for elusive species.'],
            ['name' => 'Line Transect Sampling', 'description' => 'Counting animals seen or heard while walking along fixed lines. Covers representative area samples.'],
            ['name' => 'Point Transect Sampling', 'description' => 'Observers count animals from fixed points. Similar to line transects but stationary.'],
            ['name' => 'Capture-Recapture Method', 'description' => 'Capturing animals, marking them, and later recapturing to estimate total population size.'],
            ['name' => 'Infrared Thermal Imaging', 'description' => 'Using thermal imaging to detect wildlife, especially effective for nocturnal species or dense vegetation.'],
        ];

        foreach ($methods as $method) {
            CountingMethod::create($method);
        }
    }
}
