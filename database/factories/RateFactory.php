<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\{Cashbox, Currency, Rate};

class RateFactory extends Factory
{
    /**
     * The name of factory's corresponding model.
     *
     * @var string
     */
    protected $model = Rate::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        do {
            $from_currency_id = Currency::inRandomOrder()->first();
            $to_currency_id = Currency::inRandomOrder()->first();
            $cashbox_id = Cashbox::inRandomOrder()->first();
        } while (Rate::withTrashed()
            ->where('from_currency_id', $from_currency_id)
            ->where('to_currency_id', $to_currency_id)
            ->where('cashbox_id', $cashbox_id)
            ->first()
        );
        return [
            'from_currency_id' => $from_currency_id,
            'to_currency_id' => $to_currency_id,
            'amount' => $this->faker->randomFloat(2, 100, 100000),
            'cashbox_id' => $cashbox_id,
        ];
    }
}
