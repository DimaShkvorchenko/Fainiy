<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\{Cashbox, Currency, Exchange, User};

class ExchangeFactory extends Factory
{
    /**
     * The name of factory's corresponding model.
     *
     * @var string
     */
    protected $model = Exchange::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'access_code' => $this->faker->randomNumber(4, true),
            'from_amount' => $this->faker->randomFloat(2, 100, 100000),
            'to_amount' => $this->faker->randomFloat(2, 100, 100000),
            'commission' => json_encode([
                'exchange_commission' => $this->faker->randomFloat(2, 1, 100)
            ]),
            'user_id' => User::where('account_type', User::STAFF_TYPE)->inRandomOrder()->first(),
            'client_id' => User::where('account_type', User::CLIENT_TYPE)->inRandomOrder()->first(),
            'cashbox_id' => Cashbox::inRandomOrder()->first(),
            'from_currency_id' => Currency::inRandomOrder()->first(),
            'to_currency_id' => Currency::inRandomOrder()->first(),
            'exchange_rate' => $this->faker->randomFloat(2, 10, 100),
            'exchange_type' => $this->faker->randomElement(['buy', 'sell', 'cross']),
            'description' => $this->faker->sentence(),
            'status' => $this->faker->randomElement(['created', 'completed', 'cancelled']),
            'time_of_issue' => $this->faker->dateTimeBetween('-30 days', '+30 days')->format('Y-m-d H:i:s'),
            'spread' => $this->faker->randomFloat(2, 10, 100),
        ];
    }
}
