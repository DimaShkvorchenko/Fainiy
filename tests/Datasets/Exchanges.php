<?php

use App\Models\Currency;
use Faker\Factory;

$faker = Factory::create();

dataset('exchanges', [
    [
        'access_code' => fn() => $faker->randomNumber(4, true),
        'to_amount' => fn() => $faker->randomFloat(2, 100, 100000),
        'to_currency_id' => fn() => Currency::inRandomOrder()->first()->id,
        'exchange_rate' => fn() => $faker->randomFloat(2, 10, 100),
        'description' => fn() => $faker->sentence(),
        'status' => fn() => $faker->randomElement(['created', 'completed', 'cancelled']),
        'time_of_issue' => fn() => $faker->dateTimeBetween('-30 days', '+30 days')->format('Y-m-d H:i:s'),
        'spread' => fn() => $faker->randomFloat(2, 10, 100),
    ],
    [
        'access_code' => fn() => $faker->randomNumber(4, true),
        'to_amount' => fn() => $faker->randomFloat(2, 100, 100000),
        'to_currency_id' => fn() => Currency::inRandomOrder()->first()->id,
        'exchange_rate' => fn() => $faker->randomFloat(2, 10, 100),
        'description' => fn() => $faker->sentence(),
        'status' => fn() => $faker->randomElement(['created', 'completed', 'cancelled']),
        'time_of_issue' => fn() => $faker->dateTimeBetween('-30 days', '+30 days')->format('Y-m-d H:i:s'),
        'spread' => 0,
    ]
]);

dataset('wrong_exchanges', [
    [
        'access_code' => 'string',
        'to_amount' => fn() => $faker->randomFloat(2, 100, 100000),
        'to_currency_id' => fn() => Currency::inRandomOrder()->first()->id,
        'exchange_rate' => fn() => $faker->randomFloat(2, 10, 100),
        'description' => fn() => $faker->sentence(),
        'status' => fn() => $faker->randomElement(['created', 'completed', 'cancelled']),
        'time_of_issue' => fn() => $faker->dateTimeBetween('-30 days', '+30 days')->format('Y-m-d H:i:s'),
        'spread' => fn() => $faker->randomFloat(2, 10, 100),
    ],
    [
        'access_code' => fn() => $faker->randomNumber(4, true),
        'to_amount' => false,
        'to_currency_id' => fn() => Currency::inRandomOrder()->first()->id,
        'exchange_rate' => fn() => $faker->randomFloat(2, 10, 100),
        'description' => fn() => $faker->sentence(),
        'status' => fn() => $faker->randomElement(['created', 'completed', 'cancelled']),
        'time_of_issue' => fn() => $faker->dateTimeBetween('-30 days', '+30 days')->format('Y-m-d H:i:s'),
        'spread' => fn() => $faker->randomFloat(2, 10, 100),
    ],
    [
        'access_code' => fn() => $faker->randomNumber(4, true),
        'to_amount' => fn() => $faker->randomFloat(2, 100, 100000),
        'to_currency_id' => '',
        'exchange_rate' => fn() => $faker->randomFloat(2, 10, 100),
        'description' => fn() => $faker->sentence(),
        'status' => fn() => $faker->randomElement(['created', 'completed', 'cancelled']),
        'time_of_issue' => fn() => $faker->dateTimeBetween('-30 days', '+30 days')->format('Y-m-d H:i:s'),
        'spread' => fn() => $faker->randomFloat(2, 10, 100),
    ]
]);

