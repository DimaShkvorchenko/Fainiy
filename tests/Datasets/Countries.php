<?php

use Faker\Factory;

$faker = Factory::create();

dataset('countries', [
    [
        'name' => fn() => $faker->country()
    ],
    [
        'name' => fn() => $faker->country()
    ]
]);

dataset('wrong_countries', [
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

