<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\{Cashbox, User, Transfer, Currency};

class TransferFactory extends Factory
{
    /**
     * The name of factory's corresponding model.
     *
     * @var string
     */
    protected $model = Transfer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $from_cashbox_id = Cashbox::latest()->first()->id;
        do {
            $to_cashbox_id = Cashbox::inRandomOrder()->first()->id;
        } while ($from_cashbox_id == $to_cashbox_id);

        return [
            'access_code' => $this->faker->randomNumber(4, true),
            'amount' => $this->faker->randomFloat(2, 100, 100000),
            'commission' => json_encode([
                'transfer_commission' => $this->faker->randomFloat(2, 1, 100)
            ]),
            'user_id' => User::where('account_type', User::STAFF_TYPE)->inRandomOrder()->first(),
            'client_id' => User::where('account_type', User::CLIENT_TYPE)->inRandomOrder()->first(),
            'from_cashbox_id' => $from_cashbox_id,
            'to_cashbox_id' => $to_cashbox_id,
            'currency_id' => Currency::inRandomOrder()->first(),
            'description' => $this->faker->sentence(),
            'status' => $this->faker->randomElement(['new', 'edited', 'done', 'cancelled']),
            'time_of_issue' => $this->faker->dateTimeBetween('-30 days', '+30 days')->format('Y-m-d H:i:s'),
        ];
    }
}
