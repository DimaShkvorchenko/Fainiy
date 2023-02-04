<?php

use Faker\Factory;

$faker = Factory::create();

dataset('clients', [
    [
        'first_name' => fn() => $faker->firstName(),
        'last_name' => fn() => $faker->lastName(),
        'phone' => fn() => $faker->phoneNumber(),
        'telegram' => fn() => $faker->userName() . $faker->lastName() . $faker->randomNumber(4, true),
        'modules' => fn() => json_encode(["transfer", "exchange"]),
        'other_data' => fn() => json_encode([
            'nickname' => $faker->name(),
            'phone2' => $faker->phoneNumber(),
            'referral' => $faker->uuid(),
            'commission' => $faker->randomFloat(2, 1, 50)
        ]),
    ],
    [
        'first_name' => fn() => $faker->firstName(),
        'last_name' => fn() => $faker->lastName(),
        'phone' => fn() => $faker->phoneNumber(),
        'telegram' => fn() => $faker->userName() . $faker->lastName() . $faker->randomNumber(4, true),
        'modules' => fn() => json_encode(["transfer", "exchange"]),
        'other_data' => fn() => json_encode([
            'nickname' => $faker->name(),
            'phone2' => $faker->phoneNumber(),
            'referral' => $faker->uuid(),
            'commission' => $faker->randomFloat(2, 1, 50)
        ]),
    ],
    [
        'first_name' => fn() => $faker->firstName(),
        'last_name' => fn() => $faker->lastName(),
        'phone' => fn() => $faker->phoneNumber(),
        'telegram' => fn() => $faker->userName() . $faker->lastName() . $faker->randomNumber(4, true),
        'modules' => fn() => json_encode(["transfer", "exchange"]),
        'other_data' => fn() => json_encode([
            'nickname' => $faker->name(),
            'phone2' => $faker->phoneNumber(),
            'referral' => $faker->uuid(),
            'commission' => $faker->randomFloat(2, 1, 50)
        ]),
    ],
]);

dataset('missing_clients', ['S', 0, -1, 100000]);
