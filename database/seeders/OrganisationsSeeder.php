<?php

namespace Database\Seeders;

use App\Models\Organisation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Str;

class OrganisationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $organisations = [
            ['name' => 'Regional CBNRM', 'organisation_type_id' => 1, 'organisation_id' => null, 'slug' => 'regional-cbnrm', 'created_at' => Carbon::parse('2024-01-28 08:03:49'), 'updated_at' => Carbon::parse('2024-01-28 08:03:49')],
            ['name' => 'Resource Africa', 'organisation_type_id' => 2, 'organisation_id' => 1, 'slug' => 'resource-africa', 'created_at' => Carbon::parse('2024-01-28 08:04:18'), 'updated_at' => Carbon::parse('2024-01-28 08:04:18')],
            ['name' => 'Jamma International', 'organisation_type_id' => 2, 'organisation_id' => 1, 'slug' => 'jamma-international', 'created_at' => Carbon::parse('2024-01-28 08:04:32'), 'updated_at' => Carbon::parse('2024-01-28 08:04:32')],
            ['name' => 'Leading Digital', 'organisation_type_id' => 3, 'organisation_id' => 1, 'slug' => 'leading-digital', 'created_at' => Carbon::parse('2024-01-28 08:04:40'), 'updated_at' => Carbon::parse('2024-01-28 08:04:40')],
            ['name' => 'Zimbabwe', 'organisation_type_id' => 4, 'organisation_id' => 1, 'slug' => 'zimbabwe', 'created_at' => Carbon::parse('2024-01-28 08:04:51'), 'updated_at' => Carbon::parse('2024-01-28 08:04:51')],
            ['name' => 'Beitbridge', 'organisation_type_id' => 5, 'organisation_id' => 5, 'slug' => 'beitbridge', 'created_at' => Carbon::parse('2024-02-02 01:24:17'), 'updated_at' => Carbon::parse('2024-02-02 01:24:17')],
            ['name' => 'Binga', 'organisation_type_id' => 5, 'organisation_id' => 5, 'slug' => 'binga', 'created_at' => Carbon::parse('2024-02-02 01:24:25'), 'updated_at' => Carbon::parse('2024-02-02 01:24:25')],
            ['name' => 'Bulilima', 'organisation_type_id' => 5, 'organisation_id' => 5, 'slug' => 'bulilima', 'created_at' => Carbon::parse('2024-02-02 01:24:31'), 'updated_at' => Carbon::parse('2024-02-02 01:24:31')],
            ['name' => 'Bubi', 'organisation_type_id' => 5, 'organisation_id' => 5, 'slug' => 'bubi', 'created_at' => Carbon::parse('2024-02-02 01:24:39'), 'updated_at' => Carbon::parse('2024-02-02 01:24:39')],
            ['name' => 'Chipinge', 'organisation_type_id' => 5, 'organisation_id' => 5, 'slug' => 'chipinge', 'created_at' => Carbon::parse('2024-02-02 01:24:43'), 'updated_at' => Carbon::parse('2024-02-02 01:24:43')],
            ['name' => 'Chiredzi', 'organisation_type_id' => 5, 'organisation_id' => 5, 'slug' => 'chiredzi', 'created_at' => Carbon::parse('2024-02-02 01:24:48'), 'updated_at' => Carbon::parse('2024-02-02 01:24:48')],
            ['name' => 'Gokwe North', 'organisation_type_id' => 5, 'organisation_id' => 5, 'slug' => 'gokwe-north', 'created_at' => Carbon::parse('2024-02-02 01:24:54'), 'updated_at' => Carbon::parse('2024-02-02 01:24:54')],
            ['name' => 'Gokwe South', 'organisation_type_id' => 5, 'organisation_id' => 5, 'slug' => 'gokwe-south', 'created_at' => Carbon::parse('2024-02-02 01:25:01'), 'updated_at' => Carbon::parse('2024-02-02 01:25:01')],
            ['name' => 'Hurungwe', 'organisation_type_id' => 5, 'organisation_id' => 5, 'slug' => 'hurungwe', 'created_at' => Carbon::parse('2024-02-02 01:25:10'), 'updated_at' => Carbon::parse('2024-02-02 01:25:10')],
            ['name' => 'Hwange', 'organisation_type_id' => 5, 'organisation_id' => 5, 'slug' => 'hwange', 'created_at' => Carbon::parse('2024-02-02 01:25:15'), 'updated_at' => Carbon::parse('2024-02-02 01:25:15')],
            ['name' => 'Uzumba Maramba Pfungwe (UMP)', 'organisation_type_id' => 5, 'organisation_id' => 5, 'slug' => 'uzumba-maramba-pfungwe-ump', 'created_at' => Carbon::parse('2024-02-02 01:26:09'), 'updated_at' => Carbon::parse('2024-02-02 01:26:09')],
            ['name' => 'Umguza', 'organisation_type_id' => 5, 'organisation_id' => 5, 'slug' => 'umguza', 'created_at' => Carbon::parse('2024-02-02 01:26:15'), 'updated_at' => Carbon::parse('2024-02-02 01:26:15')],
            ['name' => 'Mangwe', 'organisation_type_id' => 5, 'organisation_id' => 5, 'slug' => 'mangwe', 'created_at' => Carbon::parse('2024-02-02 01:26:20'), 'updated_at' => Carbon::parse('2024-02-02 01:26:20')],
            ['name' => 'Mbire', 'organisation_type_id' => 5, 'organisation_id' => 5, 'slug' => 'mbire', 'created_at' => Carbon::parse('2024-02-02 01:26:30'), 'updated_at' => Carbon::parse('2024-02-02 01:26:30')],
            ['name' => 'Nyaminyami', 'organisation_type_id' => 5, 'organisation_id' => 5, 'slug' => 'nyaminyami', 'created_at' => Carbon::parse('2024-02-02 01:26:35'), 'updated_at' => Carbon::parse('2024-02-02 01:26:35')],
            ['name' => 'Tsholotsho', 'organisation_type_id' => 5, 'organisation_id' => 5, 'slug' => 'tsholotsho', 'created_at' => Carbon::parse('2024-02-02 01:26:42'), 'updated_at' => Carbon::parse('2024-02-02 01:26:42')],
            ['name' => 'Campfire Association', 'organisation_type_id' => 12, 'organisation_id' => 5, 'slug' => 'campfire-association', 'created_at' => Carbon::parse('2024-02-02 01:26:42'), 'updated_at' => Carbon::parse('2024-02-02 01:26:42')],
            ['name' => 'Sentinel Safaris', 'organisation_type_id' => 13, 'organisation_id' => 6, 'slug' => 'sentinel-safaris', 'created_at' => '2024-02-07 06:45:04', 'updated_at' => '2024-02-07 06:45:04'],
            ['name' => 'Nengasha Safaris', 'organisation_type_id' => 13, 'organisation_id' => 6, 'slug' => 'nengasha-safaris', 'created_at' => '2024-02-07 06:45:10', 'updated_at' => '2024-02-07 06:45:10'],
            ['name' => 'Threeways Safaris', 'organisation_type_id' => 13, 'organisation_id' => 6, 'slug' => 'threeways-safaris', 'created_at' => '2024-02-07 06:45:17', 'updated_at' => '2024-02-07 06:45:17'],
            ['name' => 'Lowveld Hunters', 'organisation_type_id' => 13, 'organisation_id' => 7, 'slug' => 'lowveld-hunters', 'created_at' => '2024-02-07 06:45:39', 'updated_at' => '2024-02-07 06:45:39'],
            ['name' => 'Lodzi Hunters', 'organisation_type_id' => 13, 'organisation_id' => 7, 'slug' => 'lodzi-hunters', 'created_at' => '2024-02-07 06:45:47', 'updated_at' => '2024-02-07 06:45:47'],
            ['name' => 'Muvhimi safaris', 'organisation_type_id' => 13, 'organisation_id' => 8, 'slug' => 'muvhimi-safaris', 'created_at' => '2024-02-07 06:46:11', 'updated_at' => '2024-02-07 06:46:11'],
            ['name' => 'Nengasha Safaris', 'organisation_type_id' => 13, 'organisation_id' => 9, 'slug' => 'nengasha-safaris-1', 'created_at' => '2024-02-07 06:46:32', 'updated_at' => '2024-02-07 06:46:32'],
            ['name' => 'Zambezi Hunters', 'organisation_type_id' => 13, 'organisation_id' => 10, 'slug' => 'zambezi-hunters', 'created_at' => '2024-02-07 06:46:59', 'updated_at' => '2024-02-07 06:46:59'],
            ['name' => 'Shangani Safaris', 'organisation_type_id' => 13, 'organisation_id' => 11, 'slug' => 'shangani-safaris', 'created_at' => '2024-02-07 06:47:37', 'updated_at' => '2024-02-07 06:47:37'],
            ['name' => 'SSG Safaris', 'organisation_type_id' => 13, 'organisation_id' => 11, 'slug' => 'ssg-safaris', 'created_at' => '2024-02-07 06:47:44', 'updated_at' => '2024-02-07 06:47:44'],
            ['name' => 'Lowveld Hunters', 'organisation_type_id' => 13, 'organisation_id' => 11, 'slug' => 'lowveld-hunters-1', 'created_at' => '2024-02-07 06:47:51', 'updated_at' => '2024-02-07 06:47:51'],
            ['name' => 'Afropride Safaris', 'organisation_type_id' => 13, 'organisation_id' => 12, 'slug' => 'afropride-safaris', 'created_at' => '2024-02-07 06:48:54', 'updated_at' => '2024-02-07 06:48:54'],
            ['name' => 'Afropride Safaris', 'organisation_type_id' => 13, 'organisation_id' => 13, 'slug' => 'afropride-safaris-1', 'created_at' => '2024-02-07 06:49:22', 'updated_at' => '2024-02-07 06:49:22'],
            ['name' => 'Hurungwe Safaris', 'organisation_type_id' => 13, 'organisation_id' => 14, 'slug' => 'hurungwe-safaris', 'created_at' => '2024-02-07 06:50:12', 'updated_at' => '2024-02-07 06:50:12'],
            ['name' => 'Mbalabala Safaris', 'organisation_type_id' => 13, 'organisation_id' => 15, 'slug' => 'mbalabala-safaris', 'created_at' => '2024-02-07 06:50:34', 'updated_at' => '2024-02-07 06:50:34'],
            ['name' => 'Rogue Hunting & Safaris', 'organisation_type_id' => 13, 'organisation_id' => 16, 'slug' => 'rogue-hunting-safaris', 'created_at' => '2024-02-07 06:51:59', 'updated_at' => '2024-02-07 06:51:59'],
            ['name' => 'Nyamazana Safaris', 'organisation_type_id' => 13, 'organisation_id' => 17, 'slug' => 'nyamazana-safaris', 'created_at' => '2024-02-07 06:52:23', 'updated_at' => '2024-02-07 06:52:23'],
            ['name' => 'Western Safaris', 'organisation_type_id' => 13, 'organisation_id' => 18, 'slug' => 'western-safaris', 'created_at' => '2024-02-07 06:52:40', 'updated_at' => '2024-02-07 06:52:40'],
            ['name' => 'Matebele Hunters', 'organisation_type_id' => 13, 'organisation_id' => 18, 'slug' => 'matebele-hunters', 'created_at' => '2024-02-07 06:52:46', 'updated_at' => '2024-02-07 06:52:46'],
            ['name' => 'C.M Safaris', 'organisation_type_id' => 13, 'organisation_id' => 19, 'slug' => 'cm-safaris', 'created_at' => '2024-02-07 06:53:16', 'updated_at' => '2024-02-07 06:53:16'],
            ['name' => 'HHK Safaris', 'organisation_type_id' => 13, 'organisation_id' => 19, 'slug' => 'hhk-safaris', 'created_at' => '2024-02-07 06:54:30', 'updated_at' => '2024-02-07 06:54:30'],
            ['name' => 'National Safaris', 'organisation_type_id' => 13, 'organisation_id' => 20, 'slug' => 'national-safaris', 'created_at' => '2024-02-07 06:55:12', 'updated_at' => '2024-02-07 06:55:12'],
            ['name' => 'Track A Hunt Safaris', 'organisation_type_id' => 13, 'organisation_id' => 20, 'slug' => 'track-a-hunt-safaris', 'created_at' => '2024-02-07 06:55:19', 'updated_at' => '2024-02-07 06:55:19'],
            ['name' => 'Matupula Hunters', 'organisation_type_id' => 13, 'organisation_id' => 21, 'slug' => 'matupula-hunters', 'created_at' => '2024-02-07 06:55:57', 'updated_at' => '2024-02-07 06:55:57'],
            ['name' => 'Lodzi Hunters', 'organisation_type_id' => 13, 'organisation_id' => 21, 'slug' => 'lodzi-hunters-1', 'created_at' => '2024-02-07 06:56:06', 'updated_at' => '2024-02-07 06:56:06'],

        ];

        foreach ($organisations as $organisation) {

            $newOrganisation = Organisation::create($organisation);

            // Create admin role
            $role = $newOrganisation->organisationRoles()->create([
                'name' => 'admin',
                'guard_name' => 'web',
            ]);

            // Check if the organisation name is similar to the ones that should have all permissions
            if (Str::lower($newOrganisation->name) === Str::lower("Resource Africa") ||
                Str::lower($newOrganisation->name) === Str::lower("Jamma International") ||
                Str::lower($newOrganisation->name) === Str::lower("Leading Digital") ||
                Str::lower($newOrganisation->name) === Str::lower("Campfire Association"))
            {

                // Retrieve all permissions
                $permissions = Permission::all();
            } else {
                // Retrieve all permissions and reject the ones related to 'generic'
                $permissions = Permission::all()->reject(function ($permission) {
                    // Check if the permission name contains 'generic'
                    return Str::contains(Str::lower($permission->name), 'generic');
                });
            }

            // Find or create permissions based on the provided names
            $permissionsToAssign = [];
            foreach ($permissions as $permission) {
                // Ensure $permission->name is a string representing the permission name
                $permissionsToAssign[] = Permission::findOrCreate($permission->name);
            }

            // Sync permissions to the role (this will attach the new permissions and detach any that are not in the array)
            $role->syncPermissions($permissionsToAssign);

        }
    }
}
