<?php

namespace Database\Factories;

use App\Models\Admin\Organisation;
use App\Models\Organisation\ProblemAnimalControl;
use App\Models\Organisation\WildlifeConflictIncident;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProblemAnimalControlFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProblemAnimalControl::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $controlDate = $this->faker->dateTimeBetween('-1 year', 'now');
        // Zimbabwe coordinates approximately
        $latitude = $this->faker->latitude(-22.0, -15.0);
        $longitude = $this->faker->longitude(25.0, 33.0);
        
        return [
            'organisation_id' => Organisation::inRandomOrder()->first()->id,
            'wildlife_conflict_incident_id' => WildlifeConflictIncident::inRandomOrder()->first()->id ?? null,
            'control_date' => $controlDate,
            'control_time' => $controlDate->format('H:i:s'),
            'period' => $controlDate->format('Y'),
            'location' => $this->faker->city() . ', ' . $this->faker->country(),
            'description' => $this->faker->paragraph(),
            'latitude' => $latitude,
            'longitude' => $longitude,
            'estimated_number' => $this->faker->numberBetween(1, 10),
        ];
    }
} 