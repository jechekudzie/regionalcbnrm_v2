<?php

namespace Database\Seeders;


use App\Models\Admin\ConflictOutCome;
use App\Models\Admin\ConflictType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConflictOutComeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $conflictTypes = ConflictType::whereIn('name', ['Human - Wildlife', 'Wildlife - Human'])->get()->keyBy('name');

        $ConflictOutComes = [
            ['name' => 'Human death', 'type' => 'Human - Wildlife'],
            ['name' => 'Human Injury', 'type' => 'Human - Wildlife'],
            ['name' => 'Livestock killing', 'type' => 'Wildlife - Human'],
            ['name' => 'Livestock injury', 'type' => 'Wildlife - Human'],
            ['name' => 'Crop damage', 'type' => 'Human - Wildlife'],
            ['name' => 'Infrastructure damage', 'type' => 'Human - Wildlife'],
            ['name' => 'Retaliatory killing', 'type' => 'Wildlife - Human'],
            ['name' => 'Threat to human life', 'type' => 'Human - Wildlife'],
        ];

        foreach ($ConflictOutComes as $ConflictOutCome) {
            ConflictOutCome::create([
                'name' => $ConflictOutCome['name'],
                'conflict_type_id' => $conflictTypes[$ConflictOutCome['type']]->id,
            ]);
        }

    }
}
