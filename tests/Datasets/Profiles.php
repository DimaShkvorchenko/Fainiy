<?php

use Faker\Factory;

$faker = Factory::create();

dataset('profiles', [
    [
        'first_name' => fn() => $faker->firstName(),
        'last_name' => fn() => $faker->lastName(),
    ],
    [
        'first_name' => fn() => $faker->firstName(),
        'last_name' => fn() => $faker->lastName()
    ]
]);

dataset('wrong_profiles', [
    [
        'first_name' => null,
        'last_name' => fn() => $faker->lastName()
    ],
    [
        'first_name' => fn() => $faker->firstName(),
        'last_name' => false
    ]
]);

