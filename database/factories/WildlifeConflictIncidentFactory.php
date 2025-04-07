<?php

namespace Database\Factories;

use App\Models\Admin\ConflictType;
use App\Models\Admin\Organisation;
use App\Models\Organisation\WildlifeConflictIncident;
use Illuminate\Database\Eloquent\Factories\Factory;

class WildlifeConflictIncidentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = WildlifeConflictIncident::class;

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
        
        return [
            'organisation_id' => Organisation::inRandomOrder()->first()->id,
            'title' => 'Wildlife Conflict: ' . $this->faker->words(3, true),
            'period' => $incidentDate->format('Y'),
            'incident_date' => $incidentDate,
            'incident_time' => $incidentDate->format('H:i:s'),
            'longitude' => $longitude,
            'latitude' => $latitude,
            'location_description' => $this->faker->optional(0.8)->sentence(),
            'description' => $this->faker->paragraph(),
            'conflict_type_id' => ConflictType::inRandomOrder()->first()->id ?? null,
        ];
    }
} 