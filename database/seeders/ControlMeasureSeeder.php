<?php

namespace Database\Seeders;

use App\Models\Admin\ControlMeasure;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ControlMeasureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // Conflict types with IDs
        $conflictTypes = [
            ['id' => 1, 'name' => 'Human - Wildlife'],
            ['id' => 2, 'name' => 'Wildlife - Human'],
        ];

    // Measures for Human - Wildlife conflicts with their corresponding conflict type ID
        $humanWildlifeMeasures = [
            ['name' => 'Retaliatory Killing', 'type' => 'number', 'conflict_type_id' => 1],
            ['name' => 'Relocated', 'type' => 'number', 'conflict_type_id' => 1],
            ['name' => 'Spraying', 'type' => 'drones', 'conflict_type_id' => 1],
            ['name' => 'Nets', 'type' => 'physical', 'conflict_type_id' => 1],
            ['name' => 'Chilli bombs', 'type' => 'physical', 'conflict_type_id' => 1],
            ['name' => 'Fireworks', 'type' => 'auditory', 'conflict_type_id' => 1],
            ['name' => 'Gunshots', 'type' => 'auditory', 'conflict_type_id' => 1],
            ['name' => 'Drums', 'type' => 'auditory', 'conflict_type_id' => 1],
            ['name' => 'Abrupt darting', 'type' => 'physical', 'conflict_type_id' => 1],
            ['name' => 'Flash lights', 'type' => 'visual', 'conflict_type_id' => 1],
            ['name' => 'Traps', 'type' => 'physical', 'conflict_type_id' => 1],
        ];

// Additional measures for Wildlife - Human conflicts with their corresponding conflict type ID
        $wildlifeHumanMeasures = [
            ['name' => 'Electric fencing', 'type' => 'physical', 'conflict_type_id' => 2],
            ['name' => 'Community patrols', 'type' => 'community', 'conflict_type_id' => 2],
            ['name' => 'Early warning systems', 'type' => 'technology', 'conflict_type_id' => 2],
            ['name' => 'Habitat enrichment', 'type' => 'habitat', 'conflict_type_id' => 2],
            ['name' => 'Watering holes', 'type' => 'habitat', 'conflict_type_id' => 2],
            ['name' => 'Guard animals', 'type' => 'biological', 'conflict_type_id' => 2],
            ['name' => 'Beacons or reflectors', 'type' => 'visual', 'conflict_type_id' => 2],
            ['name' => 'Noise-making devices', 'type' => 'auditory', 'conflict_type_id' => 2],
            ['name' => 'Odor repellents', 'type' => 'olfactory', 'conflict_type_id' => 2],
            ['name' => 'Land-use planning', 'type' => 'policy', 'conflict_type_id' => 2],
            ['name' => 'Community education', 'type' => 'community', 'conflict_type_id' => 2],
            ['name' => 'Compensation schemes', 'type' => 'policy', 'conflict_type_id' => 2],
        ];

// Combine the measures for easier handling or separate seeding
        $combinedMeasures = array_merge($humanWildlifeMeasures, $wildlifeHumanMeasures);

        foreach ($combinedMeasures as $measure) {
            ControlMeasure::create([
                'name' => $measure['name'],
                'type' => $measure['type'],
                'conflict_type_id' => $measure['conflict_type_id'],
            ]);
        }

    }
}
