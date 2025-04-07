<?php

namespace Database\Factories;

use App\Models\Admin\Organisation;
use App\Models\Organisation\HuntingConcession;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organisation\HuntingConcession>
 */
class HuntingConcessionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = HuntingConcession::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $latitude = $this->faker->latitude(-22.0, -15.0);  // Zimbabwe coordinates
        $longitude = $this->faker->longitude(25.0, 33.0);  // Zimbabwe coordinates
        
        return [
            'organisation_id' => Organisation::inRandomOrder()->first()->id,
            'name' => $this->faker->words(3, true) . ' Concession',
            'description' => $this->faker->paragraph(),
            'area' => $this->faker->numberBetween(1000, 50000),
            'area_unit' => 'hectares',
            'country' => 'Zimbabwe',
            'province' => $this->faker->randomElement(['Harare', 'Bulawayo', 'Manicaland', 'Mashonaland Central', 'Mashonaland East']),
            'district' => $this->faker->city(),
            'latitude' => $latitude,
            'longitude' => $longitude,
            'start_date' => $this->faker->dateTimeBetween('-5 years', '-1 year'),
            'end_date' => $this->faker->dateTimeBetween('+1 year', '+5 years'),
        ];
    }
} 