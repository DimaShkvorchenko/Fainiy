<?php

namespace Database\Factories\Locations;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Locations\Country;

class CountryFactory extends Factory
{
    /**
     * The name of factory's corresponding model.
     *
     * @var string
     */
    protected $model = Country::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        do {
            $iso_code = $this->faker->unique()->countryCode();
        } while (!empty(Country::withTrashed()->where('iso_code', $iso_code)->first()->id));

        return [
            'iso_code' => $iso_code,
            'name' => $this->faker->country(),
        ];
    }
}
