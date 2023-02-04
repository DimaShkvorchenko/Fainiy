<?php

use Faker\Factory;

$faker = Factory::create();

dataset('currencies', [
    [
        'name' => fn() => $faker->word(),
        'is_visible' => fn() => $faker->randomElement([0, 1]),
    ],
    [
        'name' => fn() => $faker->word(),
        'is_visible' => fn() => $faker->randomElement([0, 1]),
    ]
]);

dataset('wrong_currencies', [
    [
        'name' => null
    ],
    [
        'name' => false
    ],
    [
        'name' => 9
    ]
]);

