<?php

namespace Database\Seeders;

use App\Models\Admin\HuntingOutcome;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HuntingOutComeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $outcomes = [
            ['name' => 'Killed'],
            ['name' => 'Injured'],
            ['name' => 'Injured and Escaped'],
            ['name' => 'Missed'],
            // Add more outcomes as necessary
        ];

        foreach ($outcomes as $outcome) {
            HuntingOutcome::create($outcome);
        }
    }
}
