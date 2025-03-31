<?php

namespace Database\Seeders;

use App\Models\Admin\ConflictType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConflictTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $conflictTypes = [
            ['name' => 'Human - Wildlife'],
            ['name' => 'Wildlife - Human'],
        ];

        foreach ($conflictTypes as $conflictType) {
            ConflictType::create($conflictType);
        }
    }
}
