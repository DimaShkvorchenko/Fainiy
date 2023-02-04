<?php

namespace Database\Factories;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\{Income, Exchange, Transfer, Wallet, Action};

class ActionFactory extends Factory
{
    /**
     * The name of factory's corresponding model.
     *
     * @var string
     */
    protected $model = Action::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $payload = [];
        $payload['type'] = $this->faker->randomElement(['income', 'exchange', 'transfer']);
        $payload['event'] = 'store';
        switch ($payload['type']) {
            case 'income':
                $parent = Income::inRandomOrder()->first();
                $payload['amount'] = $parent?->amount;
                $payload['parent_id'] = $parent?->id;
                $payload['staff_id'] = $parent?->staff_id;
                $payload['client_id'] = $parent?->client_id;
                $payload['currency_id'] = $parent?->currency_id;
                $payload['cashbox_id'] = $parent?->cashbox_id;
                $payload['other_data'] = json_encode($parent->only([
                    'code', 'access_code'
                ]));
                break;
            case 'exchange':
                $parent = Exchange::inRandomOrder()->first();
                $payload['amount'] = $parent?->from_amount;
                $payload['parent_id'] = $parent?->id;
                $payload['staff_id'] = $parent?->user_id;
                $payload['client_id'] = $parent?->client_id;
                $payload['currency_id'] = $parent?->from_currency_id;
                $payload['cashbox_id'] = $parent?->cashbox_id;
                $payload['other_data'] = json_encode($parent->only([
                    'to_amount', 'to_currency_id', 'exchange_rate', 'exchange_type',
                    'status', 'spread', 'commission', 'time_of_issue'
                ]));
                break;
            case 'transfer':
                $parent = Transfer::inRandomOrder()->first();
                $payload['amount'] = $parent?->amount;
                $payload['parent_id'] = $parent?->id;
                $payload['staff_id'] = $parent?->user_id;
                $payload['client_id'] = $parent?->client_id;
                $payload['currency_id'] = $parent?->currency_id;
                $payload['cashbox_id'] = $parent?->from_cashbox_id;
                $payload['other_data'] = json_encode($parent->only([
                    'to_cashbox_id', 'status', 'time_of_issue'
                ]));
                break;
        }
        return $payload;
    }
}
