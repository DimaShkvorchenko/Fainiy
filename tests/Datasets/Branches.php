<?php

use Faker\Factory;

$faker = Factory::create();

dataset('branches', [
    [
        'name' => fn() => $faker->streetAddress()
    ],
    [
        'name' => fn() => $faker->streetAddress()
    ]
]);

dataset('wrong_branches', [
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

