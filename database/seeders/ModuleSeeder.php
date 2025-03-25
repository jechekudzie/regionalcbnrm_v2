<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $modules = [
            'Generic',
            'Wildlife',
            'Population Estimate',
            'Quota Setting',
            'Hunting Concession',
            'Hunting Client',
            'Hunting Activity',
            'Human Wildlife Conflict',
            'Problematic Animal Control',
            'Poaching',
            'Child Organisation',
            'Organisation User',
            'Revenue Sharing',
            'Finance',
        ];

        foreach ($modules as $module) {
            Module::create(['name' => ucfirst($module)]);
        }
    }
}
