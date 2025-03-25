<?php

namespace Database\Seeders;

use App\Models\IdentificationType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IdentificationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        $identificationTypes = [
            ['name' => 'National Identification Card'],
            ['name' => 'Passport'],

        ];

        foreach ($identificationTypes as $type) {
            IdentificationType::create($type);
        }
    }
}
