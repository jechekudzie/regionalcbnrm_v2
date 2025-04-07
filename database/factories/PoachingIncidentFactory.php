<?php

namespace Database\Factories;

use App\Models\Admin\Organisation;
use App\Models\Organisation\PoachingIncident;
use Illuminate\Database\Eloquent\Factories\Factory;

class PoachingIncidentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PoachingIncident::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $incidentDate = $this->faker->dateTimeBetween('-1 year', 'now');
        // Zimbabwe coordinates approximately
        $latitude = $this->faker->latitude(-22.0, -15.0);
        $longitude = $this->faker->longitude(25.0, 33.0);
        
        $docketStatuses = ['Open', 'Closed', 'Under Investigation', 'Pending'];
        
        return [
            'organisation_id' => Organisation::inRandomOrder()->first()->id,
            'title' => 'Poaching Incident: ' . $this->faker->words(3, true),
            'location' => $this->faker->city() . ', ' . $this->faker->country(),
            'longitude' => $longitude,
            'latitude' => $latitude,
            'docket_number' => 'DOC-' . $this->faker->unique()->numberBetween(1000, 9999),
            'docket_status' => $this->faker->randomElement($docketStatuses),
            'period' => $incidentDate->format('Y'),
            'date' => $incidentDate,
            'time' => $incidentDate->format('H:i:s'),
        ];
    }
} 