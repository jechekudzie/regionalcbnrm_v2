<?php

namespace Database\Seeders;

use App\Models\Admin\Gender;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GenderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //add genders male and female

        $genders = [
            ['name' => 'Male'],
            ['name' => 'Female'],
        ];

        foreach ($genders as $gender){

            Gender::create($gender);

        }


    }
}
