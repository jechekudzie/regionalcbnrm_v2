<?php

namespace Database\Seeders;

use App\Models\Organisation\Species;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SpeciesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
     function run(): void
    {
        //
        $species = [
            ['name' => 'Elephant', 'scientific' => 'Elephas maximus, Loxodonta africana', 'male_name' => 'Bull', 'female_name' => 'Cow', 'avatar' => '/images/elephant.jpg', 'is_special' => 1],
            ['name' => 'Lion', 'scientific' => 'Panthera leo', 'male_name' => 'Lion', 'female_name' => 'Lioness', 'avatar' => '/images/lion.jpg', 'is_special' => 1],
            ['name' => 'Leopard', 'scientific' => 'Panthera pardus', 'male_name' => 'Leopard', 'female_name' => 'Leopardess', 'avatar' => '/images/leopard.jpg', 'is_special' => 1],
            ['name' => 'Buffalo', 'scientific' => 'Syncerus caffer, Bubalus bubalis', 'male_name' => 'Bull', 'female_name' => 'Cow', 'avatar' => '/images/buffalo.jpg', 'is_special' => 1],
            ['name' => 'Crocodile', 'scientific' => 'Crocodylus niloticus, Crocodylus porosus', 'male_name' => 'Bull', 'female_name' => 'Cow', 'avatar' => '/images/crocodile.jpg', 'is_special' => 1],
            ['name' => 'Hippo', 'scientific' => 'Hippopotamus amphibius', 'male_name' => 'Bull', 'female_name' => 'Cow', 'avatar' => '/images/hippo.jpg', 'is_special' => 1],
            ['name' => 'Hyena - Spotted', 'scientific' => 'Crocuta crocuta', 'male_name' => 'Male Hyena', 'female_name' => 'Female Hyena', 'avatar' => '/images/hyena - spotted.jpg', 'is_special' => 1],
            ['name' => 'Hyena - Brown', 'scientific' => 'Hyaena brunnea', 'male_name' => 'Male Hyena', 'female_name' => 'Female Hyena', 'avatar' => '/images/hyena - brown.jpg', 'is_special' => 1],
            ['name' => 'Wild Dogs', 'scientific' => 'Lycaon pictus', 'male_name' => 'Male', 'female_name' => 'Female', 'avatar' => '/images/wild dogs.jpg', 'is_special' => 0],
            ['name' => 'Jackal', 'scientific' => 'Canis aureus, Canis mesomelas', 'male_name' => 'Male', 'female_name' => 'Female', 'avatar' => '/images/jackal.jpg', 'is_special' => 0],
            ['name' => 'Snakes', 'scientific' => 'Varies by species', 'male_name' => 'Male', 'female_name' => 'Female', 'avatar' => '/images/snakes.jpeg', 'is_special' => 0],
            ['name' => 'Python', 'scientific' => 'Python regius, Python bivittatus', 'male_name' => 'Male', 'female_name' => 'Female', 'avatar' => '/images/python.jpg', 'is_special' => 0],
            ['name' => 'Wild Pigs', 'scientific' => 'Sus scrofa', 'male_name' => 'Boar', 'female_name' => 'Sow', 'avatar' => '/images/wild pigs.jpg', 'is_special' => 0],
            ['name' => 'Antelopes', 'scientific' => 'Varies by species', 'male_name' => 'Buck', 'female_name' => 'Doe', 'avatar' => '/images/antelopes.jpg', 'is_special' => 0],
            ['name' => 'Quelea Birds', 'scientific' => 'Quelea quelea', 'male_name' => 'Male Quelea', 'female_name' => 'Female Quelea', 'avatar' => '/images/quelea birds.jpeg', 'is_special' => 0],
        ];


        foreach ($species as $specie) {
            Species::create($specie);
        }
    }
}
