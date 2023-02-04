<?php

use Faker\Factory;

$faker = Factory::create();

dataset('staff', [
    [
        'first_name' => fn() => $faker->firstName(),
        'last_name' => fn() => $faker->lastName(),
        'phone' => fn() => $faker->phoneNumber(),
    ],
    [
        'first_name' => null,
        'last_name' => fn() => $faker->lastName(),
        'phone' => fn() => $faker->phoneNumber(),
    ],
    [
        'first_name' => fn() => $faker->firstName(),
        'last_name' => null,
        'phone' => fn() => $faker->phoneNumber(),
    ],
    [
        'first_name' => fn() => $faker->firstName(),
        'last_name' => fn() => $faker->lastName(),
        'phone' => null,
    ]
]);

dataset('missing_staff', ['S', 0, -1, 100000]);
