<?php

use Faker\Factory;

$faker = Factory::create();

dataset('income', [
    [
        'amount' => fn() => $faker->randomFloat(2, 100, 100000),
        'code' => fn() => $faker->randomNumber(4, true),
        'access_code' => fn() => $faker->randomNumber(4, true),
    ],
    [
        'amount' => fn() => $faker->randomFloat(2, 100, 100000),
        'code' => fn() => $faker->randomNumber(4, true),
        'access_code' => fn() => $faker->randomNumber(4, true),
    ]
]);

dataset('wrong_income', [
    [
        'amount' => fn() => $faker->randomFloat(2, 100, 100000),
        'code' => fn() => $faker->randomNumber(4, true),
        'access_code' => 'string',
    ],
    [
        'amount' => false,
        'code' => fn() => $faker->randomNumber(4, true),
        'access_code' => fn() => $faker->randomNumber(4, true),
    ],
    [
        'amount' => fn() => $faker->randomFloat(2, 100, 100000),
        'code' => 'string',
        'access_code' => fn() => $faker->randomNumber(4, true),
    ]
]);

