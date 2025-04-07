<?php

namespace Database\Factories;

use App\Models\Admin\IdentificationType;
use App\Models\Admin\OffenceType;
use App\Models\Admin\PoacherType;
use App\Models\Admin\PoachingReason;
use App\Models\Organisation\Poacher;
use App\Models\Organisation\PoachingIncident;
use Illuminate\Database\Eloquent\Factories\Factory;

class PoacherFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Poacher::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $statuses = ['Arrested', 'In Custody', 'Released', 'Escaped', 'Pending Trial'];
        
        return [
            'poaching_incident_id' => PoachingIncident::inRandomOrder()->first()->id ?? null,
            'name' => $this->faker->name(),
            'gender' => $this->faker->randomElement(['Male', 'Female']),
            'age' => $this->faker->numberBetween(18, 60),
            'nationality' => $this->faker->country(),
            'id_number' => $this->faker->optional(0.7)->numerify('########'),
            'id_type_id' => IdentificationType::inRandomOrder()->first()->id ?? null,
            'address' => $this->faker->optional(0.6)->address(),
            'poacher_type_id' => PoacherType::inRandomOrder()->first()->id ?? null,
            'offence_type_id' => OffenceType::inRandomOrder()->first()->id ?? null,
            'reason_id' => PoachingReason::inRandomOrder()->first()->id ?? null,
            'status' => $this->faker->randomElement($statuses),
        ];
    }
} 