<?php

use App\Models\Cashbox;
use Faker\Factory;

$faker = Factory::create();

dataset('transfers', [
    [
        'access_code' => fn() => $faker->randomNumber(4, true),
        'amount' => fn() => $faker->randomFloat(2, 100, 100000),
        'to_cashbox_id' => fn() => Cashbox::inRandomOrder()->first()->id,
        'description' => fn() => $faker->sentence(),
        'status' => 'new',
        'time_of_issue' => fn() => $faker->dateTimeBetween('-30 days', '+30 days')->format('Y-m-d H:i:s'),
    ],
    [
        'access_code' => fn() => $faker->randomNumber(4, true),
        'amount' => fn() => $faker->randomFloat(2, 100, 100000),
        'to_cashbox_id' => fn() => Cashbox::inRandomOrder()->first()->id,
        'description' => fn() => $faker->sentence(),
        'status' => 'new',
        'time_of_issue' => fn() => $faker->dateTimeBetween('-30 days', '+30 days')->format('Y-m-d H:i:s'),
    ]
]);

dataset('wrong_transfers', [
    [
        'access_code' => 'string',
        'amount' => fn() => $faker->randomFloat(2, 100, 100000),
        'to_cashbox_id' => fn() => Cashbox::inRandomOrder()->first()->id,
        'description' => fn() => $faker->sentence(),
        'status' => 'edited',
        'time_of_issue' => fn() => $faker->dateTimeBetween('-30 days', '+30 days')->format('Y-m-d H:i:s'),
    ],
    [
        'access_code' => fn() => $faker->randomNumber(4, true),
        'amount' => false,
        'to_cashbox_id' => fn() => Cashbox::inRandomOrder()->first()->id,
        'description' => fn() => $faker->sentence(),
        'status' => 'done',
        'time_of_issue' => fn() => $faker->dateTimeBetween('-30 days', '+30 days')->format('Y-m-d H:i:s'),
    ],
    [
        'access_code' => fn() => $faker->randomNumber(4, true),
        'amount' => fn() => $faker->randomFloat(2, 100, 100000),
        'to_cashbox_id' => '',
        'description' => fn() => $faker->sentence(),
        'status' => 'done',
        'time_of_issue' => fn() => $faker->dateTimeBetween('-30 days', '+30 days')->format('Y-m-d H:i:s'),
    ],
    [
        'access_code' => fn() => $faker->randomNumber(4, true),
        'amount' => fn() => $faker->randomFloat(2, 100, 100000),
        'to_cashbox_id' => fn() => Cashbox::inRandomOrder()->first()->id,
        'description' => fn() => $faker->sentence(),
        'status' => 'old',
        'time_of_issue' => fn() => $faker->dateTimeBetween('-30 days', '+30 days')->format('Y-m-d H:i:s'),
    ]
]);

