<?php

use Faker\Factory;

$faker = Factory::create();

dataset('regions', [
    [
        'name' => fn() => $faker->state()
    ],
    [
        'name' => fn() => $faker->state()
    ]
]);

dataset('wrong_regions', [
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

