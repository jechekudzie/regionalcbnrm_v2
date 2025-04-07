<?php

namespace Database\Factories;

use App\Models\Admin\Organisation;
use App\Models\Organisation\HuntingActivity;
use App\Models\Organisation\HuntingConcession;
use Illuminate\Database\Eloquent\Factories\Factory;

class HuntingActivityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = HuntingActivity::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $startDate = $this->faker->dateTimeBetween('-1 year', 'now');
        $endDate = $this->faker->dateTimeBetween($startDate, '+30 days');
        
        return [
            'organisation_id' => Organisation::inRandomOrder()->first()->id,
            'hunting_concession_id' => HuntingConcession::inRandomOrder()->first()->id ?? null,
            'safari_id' => Organisation::inRandomOrder()->first()->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'period' => $startDate->format('Y')
        ];
    }
} 