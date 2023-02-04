<?php

use Faker\Factory;

$faker = Factory::create();

dataset('telegram', [
    [
        'telegram' => fn() => '@' . $faker->userName() . $faker->lastName()
    ],
    [
        'telegram' => fn() => '@' . $faker->userName() . $faker->lastName()
    ]
]);

dataset('wrong_telegram', [
    [
        'telegram' => null
    ],
    [
        'telegram' => 9
    ]
]);

