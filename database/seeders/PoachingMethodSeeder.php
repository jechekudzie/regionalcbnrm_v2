<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PoachingMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $methods = [
            'Snares',
            'Axes',
            'Fire arms',
            'Poisons',
            'Bow and arrows',
            'Dogs',
            'Gin traps',
            'Pit trap',
            'Spears'
        ];
        foreach ($methods as $method) {
            \App\Models\Admin\PoachingMethod::create([
                'name' => $method,
            ]);
        }

    }
}
