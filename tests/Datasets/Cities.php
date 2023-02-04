<?php

use Faker\Factory;

$faker = Factory::create();

dataset('cities', [
    [
        'name' => fn() => $faker->city()
    ],
    [
        'name' => fn() => $faker->city()
    ]
]);

dataset('wrong_cities', [
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

