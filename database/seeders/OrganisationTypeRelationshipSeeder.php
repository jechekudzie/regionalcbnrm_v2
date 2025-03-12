<?php

namespace Database\Seeders;

use App\Models\OrganisationType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrganisationTypeRelationshipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $relationships = [
            ['organisation_type_id' => 1, 'child_id' => 2, 'notes' => null, 'created_at' => Carbon::parse('2024-01-28 08:01:22'), 'updated_at' => Carbon::parse('2024-01-28 08:01:22')],
            ['organisation_type_id' => 1, 'child_id' => 3, 'notes' => null, 'created_at' => Carbon::parse('2024-01-28 08:01:32'), 'updated_at' => Carbon::parse('2024-01-28 08:01:32')],
            ['organisation_type_id' => 1, 'child_id' => 4, 'notes' => null, 'created_at' => Carbon::parse('2024-01-28 08:01:40'), 'updated_at' => Carbon::parse('2024-01-28 08:01:40')],
            ['organisation_type_id' => 4, 'child_id' => 5, 'notes' => null, 'created_at' => Carbon::parse('2024-01-28 08:01:47'), 'updated_at' => Carbon::parse('2024-01-28 08:01:47')],
            ['organisation_type_id' => 4, 'child_id' => 6, 'notes' => null, 'created_at' => Carbon::parse('2024-01-28 08:01:59'), 'updated_at' => Carbon::parse('2024-01-28 08:01:59')],
            ['organisation_type_id' => 4, 'child_id' => 7, 'notes' => null, 'created_at' => Carbon::parse('2024-01-28 08:02:21'), 'updated_at' => Carbon::parse('2024-01-28 08:02:21')],
            ['organisation_type_id' => 5, 'child_id' => 8, 'notes' => null, 'created_at' => Carbon::parse('2024-01-28 08:02:29'), 'updated_at' => Carbon::parse('2024-01-28 08:02:29')],
            ['organisation_type_id' => 8, 'child_id' => 10, 'notes' => null, 'created_at' => Carbon::parse('2024-01-28 08:02:54'), 'updated_at' => Carbon::parse('2024-01-28 08:02:54')],
            ['organisation_type_id' => 8, 'child_id' => 9, 'notes' => null, 'created_at' => Carbon::parse('2024-01-28 08:02:42'), 'updated_at' => Carbon::parse('2024-01-28 08:02:42')],
            ['organisation_type_id' => 6, 'child_id' => 11, 'notes' => null, 'created_at' => Carbon::parse('2024-01-28 08:03:10'), 'updated_at' => Carbon::parse('2024-01-28 08:03:10')],
            ['organisation_type_id' => 4, 'child_id' => 12, 'notes' => null, 'created_at' => Carbon::parse('2024-01-28 08:03:10'), 'updated_at' => Carbon::parse('2024-01-28 08:03:10')],
            ['organisation_type_id' => 5, 'child_id' => 13, 'notes' => null, 'created_at' => Carbon::parse('2024-01-28 08:03:10'), 'updated_at' => Carbon::parse('2024-01-28 08:03:10')],
        ];

        DB::table('organisation_type_organisation_type')->insert($relationships);


    }
}
