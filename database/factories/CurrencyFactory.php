<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Currency;

class CurrencyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        do {
            $iso_code = $this->faker->unique()->currencyCode();
        } while (!empty(Currency::withTrashed()->where('iso_code', $iso_code)->first()->id));

        return [
            'iso_code' => $iso_code,
            'name' => $this->faker->word(),
        ];
    }
}
