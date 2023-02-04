<?php

namespace Database\Factories\Locations;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Locations\{Region, City};

class CityFactory extends Factory
{
    /**
     * The name of factory's corresponding model.
     *
     * @var string
     */
    protected $model = City::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'region_id' => Region::inRandomOrder()->first(),
            'name' => $this->faker->city(),
        ];
    }
}
