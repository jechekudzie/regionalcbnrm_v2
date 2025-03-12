<?php

namespace Database\Seeders;

use App\Mail\AccountCreatedMail;
use App\Models\Organisation;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

class OrganisationAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $users = [

            [
                'name' => 'Nkululeko Ncube',
                'email' => 'nkululekoncube777@gmail.com',
                'password' => 'password@1',
                'organisation_id' => 21,
            ],
            [
                'name' => 'Ruvimbo Mutumwa',
                'email' => 'ruemutumwa@gmail.com',
                'password' => 'password@1',
                'organisation_id' => 22,
            ],
            [
                'name' => 'Nxolelani Ncube',
                'email' => 'nxolelani77@gmail.com',
                'password' => 'password@1',
                'organisation_id' => 15,
            ],
            [
                'name' => 'Xolani Moyo',
                'email' => 'brilliantxolanim@gmail.com',
                'password' => 'password@1',
                'organisation_id' => 18,
            ],
            [
                'name' => 'Chenjerai Zanamwe',
                'email' => 'czanamwe@gmail.com',
                'password' => 'password@1',
                'organisation_id' => 11,
            ],
            [
                'name' => 'Mercy Maguvu',
                'email' => 'messymaguvu@gmail.com',
                'password' => 'password@1',
                'organisation_id' => 11,
            ],
            [
                'name' => 'Nkosilomusa Horwell Mlilo',
                'email' => 'naturalresourcesbubirdc@gmail.com',
                'password' => 'password@1',
                'organisation_id' => 9,
            ],
            [
                'name' => 'Lizwelethu Tshuma',
                'email' => 'lizwetshuma@gmail.com',
                'password' => 'password@1',
                'organisation_id' => 8,
            ],
            [
                'name' => 'Wisdom Munashe Hove',
                'email' => 'wisdomhove09@gmail.com',
                'password' => 'password@1',
                'organisation_id' => 13,
            ],
            [
                'name' => 'John Sikurukumwe',
                'email' => 'jsikurukumwe@gmail.com',
                'password' => 'password@1',
                'organisation_id' => 20,
            ],
            [
                'name' => 'Clayton madzana Gumbo',
                'email' => 'claymadzana@gmail.com',
                'password' => 'password@1',
                'organisation_id' => 12,
            ],
            [
                'name' => 'Tarcisius M Mahuni',
                'email' => 'mahunitm19@gmail.com',
                'password' => 'password@1',
                'organisation_id' => 19,
            ],
            [
                'name' => 'Obert Shoko',
                'email' => 'obertshoko@gmail.com',
                'password' => 'password@1',
                'organisation_id' => 19,
            ],
            [
                'name' => 'Lameck Muntanga',
                'email' => 'muntangalameck010@gmail.com',
                'password' => 'password@1',
                'organisation_id' => 7,
            ],
            [
                'name' => 'Rodrick Manhondo',
                'email' => 'manhondo76@gmail.com',
                'password' => 'password@1',
                'organisation_id' => 10,
            ],
            [
                'name' => 'MASIMBA GOUSTINO',
                'email' => 'mgoustino@umprdc.co.zw',
                'password' => 'password@1',
                'organisation_id' => 16,
            ],
            [
                'name' => 'Samuel Sibanda',
                'email' => 'ssibanda@bbrdc.co.zw',
                'password' => 'password@1',
                'organisation_id' => 6,
            ],
        ];

        foreach ($users as $userData) {
            // Find the organisation for the current user, skip if not found
            $organisation = Organisation::find($userData['organisation_id']);
            if (!$organisation) {
                continue; // Skip this user if the organisation doesn't exist
            }

            // Create the user
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make($userData['password']),
            ]);

            // Check if the 'super-admin' role already exists for the organisation
            $role = Role::firstOrCreate([
                'name' => 'admin',
                'guard_name' => 'web',
                'organisation_id' => $organisation->id,
            ]);

            // Assign the role to the user
            $user->roles()->attach($role->id, [
                'model_type' => get_class($user),
                'organisation_id' => $organisation->id
            ]);

            // Attach the user to the organisation with the specified role
            $organisation->users()->attach($user->id, ['role_id' => $role->id]);

            $organisation = $organisation->refresh();

            //Mail::to($user->email)->queue(new AccountCreatedMail($user->id, $organisation->id));


        }
    }
}
