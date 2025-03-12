<?php

namespace Database\Seeders;

use App\Models\OrganisationType;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrganisationTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $organisationTypes = [

            ['name' => 'System users', 'slug' => 'system-users', 'description' => 'Users with access to the core system functionalities, typically involved in daily operations and management tasks.', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Funders', 'slug' => 'funders', 'description' => 'Individuals or organizations that provide financial support for projects or operations, playing a critical role in resource allocation.', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Developers', 'slug' => 'developers', 'description' => 'Technical professionals responsible for developing, maintaining, and updating the system software.', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Zimbabwe template', 'slug' => 'zimbabwe-template', 'description' => 'A predefined framework used specifically for configurations and operations within Zimbabwe.', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Rural District Council', 'slug' => 'rural-district-council', 'description' => 'Local government authorities responsible for the administration and governance of rural areas, focusing on development and local services.', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Wildlife Management Authority', 'slug' => 'zimbabwe-parks-wildlife-management-authority', 'description' => 'Governing body overseeing the conservation and management of wildlife resources, ensuring sustainable practices and protection of biodiversity.', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'NGOs', 'slug' => 'ngos', 'description' => 'Non-governmental organizations involved in various aspects of community development, environmental conservation, and social welfare.', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Wards', 'slug' => 'wards', 'description' => 'Producer Communities.', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Campfire Committee', 'slug' => 'campfire-committee', 'description' => 'Committees involved in managing community-based natural resource projects, focusing on benefiting local communities through wildlife and tourism.', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Villages', 'slug' => 'villages', 'description' => 'Producer Communities.', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Zimparks Stations', 'slug' => 'zimparks-stations', 'description' => 'Operational bases for the Zimbabwe Parks and Wildlife Management Authority, tasked with protecting and managing national parks and wildlife.', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Associations', 'slug' => 'associations', 'description' => 'Groups or coalitions formed around common interests or professions, providing a platform for collaboration and advocacy.', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Safari Operators', 'slug' => 'safari-operator', 'description' => 'Companies or individuals that organize and conduct safari tours, responsible for offering wildlife experiences and managing tourist services.', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],

        ];

        foreach ($organisationTypes as $type) {
            OrganisationType::create($type);
        }

    }
}
