<?php

use Faker\Factory;

$faker = Factory::create();

dataset('login_admin_failed', [
    [
        'email' => 'john.smith@test.com',
        'password' => '12345',
    ],
    [
        'email' => 'john.smith.fail@test.com',
        'password' => '12345',
    ],
    [
        'email' => 'john.smith',
        'password' => '123456',
    ],
    [
        'email' => '',
        'password' => '123456',
    ],
    [
        'email' => null,
        'password' => '123456',
    ],
    [
        'email' => 'john.smith',
        'password' => '',
    ],
    [
        'email' => 'john.smith',
        'password' => null,
    ],
]);

dataset('wrong_telegram_data', [
    [
        'id' => fn() => $faker->randomNumber(9, true),
        'username' => fn() => $faker->firstName(),
        'auth_date' => fn() => $faker->unixTime(),
        'hash' => '',
        'first_name' => fn() => $faker->firstName(),
        'last_name' => fn() => $faker->lastName()
    ],
    [
        'id' => fn() => $faker->randomNumber(9, true),
        'username' => fn() => $faker->firstName(),
        'auth_date' => fn() => $faker->unixTime(),
        'hash' => '',
        'first_name' => fn() => $faker->firstName(),
        'last_name' => fn() => $faker->lastName()
    ]
]);

