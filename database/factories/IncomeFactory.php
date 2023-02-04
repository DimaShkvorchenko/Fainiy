<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\{Cashbox, User, Income, Currency};
use App\Services\Commission\IncomeCommission;

class IncomeFactory extends Factory
{
    /**
     * The name of factory's corresponding model.
     *
     * @var string
     */
    protected $model = Income::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $client = User::where('account_type', User::CLIENT_TYPE)->inRandomOrder()->first();
        $amount = $this->faker->randomFloat(2, 100, 100000);
        $commission = (new IncomeCommission())->getOrSetClientIncomeCommission($client->id);
        return [
            'client_id' => $client->id,
            'staff_id' => User::where('account_type', User::STAFF_TYPE)->inRandomOrder()->first(),
            'amount' => $amount,
            'currency_id' => Currency::inRandomOrder()->first(),
            'code' => $this->faker->randomNumber(4, true),
            'access_code' => $this->faker->randomNumber(4, true),
            'cashbox_id' => Cashbox::inRandomOrder()->first(),
            'commission' => $commission,
            'profit' => ($commission/100) * $amount,
        ];
    }
}
