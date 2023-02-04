<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\{Cashbox, User, Currency, Wallet};

class WalletFactory extends Factory
{
    /**
     * The name of factory's corresponding model.
     *
     * @var string
     */
    protected $model = Wallet::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'client_id' => User::where('account_type', User::CLIENT_TYPE)->inRandomOrder()->first(),
            'currency_id' => Currency::inRandomOrder()->first(),
            'amount' => $this->faker->randomFloat(2, 100, 100000),
            'cashbox_id' => Cashbox::inRandomOrder()->first(),
        ];
    }
}
