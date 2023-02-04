<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Locations\Branch;

class CashboxFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'number' => $this->faker->randomNumber(6, true),
            'name' => $this->faker->word(),
            'branch_id' => Branch::inRandomOrder()->first(),
            'settings' => json_encode([
                'show_on_exchange_page' => $this->faker->randomElement([1, 0])
            ]),
        ];
    }
}
