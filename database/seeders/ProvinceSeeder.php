<?php

namespace Database\Seeders;

use App\Models\Province;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $provinces = [
            ["name" => "Bulawayo"],
            ["name" => "Harare"],
            ["name" => "Manicaland"],
            ["name" => "Mashonaland Central"],
            ["name" => "Mashonaland East"],
            ["name" => "Mashonaland West"],
            ["name" => "Masvingo"],
            ["name" => "Matebeleland North"],
            ["name" => "Matebeleland South"],
            ["name" => "Midlands"],
        ];

        foreach ($provinces as $province) {
            Province::create($province);
        }
    }
}
