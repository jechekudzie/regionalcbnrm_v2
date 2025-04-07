<?php

namespace Database\Factories;

use App\Models\Admin\Organisation;
use App\Models\Organisation\QuotaAllocation;
use App\Models\Organisation\Species;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuotaAllocationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = QuotaAllocation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $startDate = $this->faker->dateTimeBetween('-1 year', 'now');
        $endDate = $this->faker->dateTimeBetween($startDate, '+1 year');
        $huntingQuota = $this->faker->numberBetween(5, 20);
        
        return [
            'organisation_id' => Organisation::inRandomOrder()->first()->id,
            'species_id' => Species::inRandomOrder()->first()->id,
            'hunting_quota' => $huntingQuota,
            'rational_killing_quota' => $this->faker->numberBetween(1, $huntingQuota),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'period' => $startDate->format('Y'),
            'notes' => $this->faker->optional(0.7)->sentence(),
        ];
    }
} 