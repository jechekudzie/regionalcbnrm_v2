<?php

namespace Database\Seeders;

use App\Models\Maturity;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MaturitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $maturities = ['Adult', 'Juvenile'];

        foreach ($maturities as $maturity) {
            Maturity::create([
                'name' => $maturity,
            ]);
        }
    }
}
