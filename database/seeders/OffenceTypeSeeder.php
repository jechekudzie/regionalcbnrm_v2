<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OffenceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //first-time offenders or habitual offenders
        $offenceTypes = [
            'First-time offender',
            'Habitual offender',
        ];
        foreach ($offenceTypes as $offenceType) {
            \App\Models\Admin\OffenceType::create([
                'name' => $offenceType,
            ]);
        }
    }
}
