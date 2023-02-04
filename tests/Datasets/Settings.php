<?php

use Faker\Factory;

$faker = Factory::create();

dataset('settings', [
    [
        'value' => fn() => $faker->word()
    ],
    [
        'value' => fn() => $faker->word()
    ]
]);

dataset('wrong_settings', [
    [
        'value' => null
    ],
    [
        'value' => false
    ],
    [
        'value' => 9
    ]
]);

