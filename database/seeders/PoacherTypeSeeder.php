<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PoacherTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $poacherTypes = [
            'Poacher',
            'Hunter',
            'Herder',
            'Farmer',
            'Tourist',
            'Other',
        ];
        foreach ($poacherTypes as $poacherType) {
            \App\Models\PoacherType::create([
                'name' => $poacherType,
            ]);
        }
    }
}
