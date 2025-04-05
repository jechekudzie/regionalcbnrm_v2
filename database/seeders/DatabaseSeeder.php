<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
       

        $this->call([
            ModuleSeeder::class,
            OrganisationTypesSeeder::class,
            OrganisationTypeRelationshipSeeder::class,
            OrganisationsSeeder::class,
           
            SpeciesSeeder::class,
            SpeciesGenderSeeder::class,
            MaturitySeeder::class,
            CountingMethodSeeder::class,
            CountriesSeeder::class,
            ConflictOutComeSeeder::class,
            ConflictTypeSeeder::class,
            ControlMeasureSeeder::class,
            OffenceTypeSeeder::class,
            PoacherTypeSeeder::class,
            PoachingMethodSeeder::class,
            PoachingReasonSeeder::class,
            HuntingOutComeSeeder::class,
            IdentificationTypeSeeder::class,
            GenderSeeder::class,
            ProvinceSeeder::class,
            CitySeeder::class,
            CategorySeeder::class,
            PayableItemSeeder::class,
            UsersTableSeeder::class,
            CropTypeSeeder::class,
            LiveStockTypeSeeder::class,
           
        ]);
    
    }
}
