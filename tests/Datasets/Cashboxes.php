<?php

use App\Models\Locations\Branch;
use Faker\Factory;

$faker = Factory::create();

dataset('cashboxes', [
    [
        'name' => fn() => $faker->word(),
        'branch_id' => fn() => Branch::inRandomOrder()->first()->id,
        'settings' => fn() => '{"show_on_exchange_page": ' . $faker->randomElement([1, 0]) . '}',
    ],
    [
        'name' => fn() => $faker->word(),
        'branch_id' => fn() => Branch::inRandomOrder()->first()->id,
        'settings' => fn() => '{"show_on_exchange_page": ' . $faker->randomElement([1, 0]) . '}',
    ]
]);

dataset('wrong_cashboxes', [
    [
        'name' => false,
        'branch_id' => fn() => Branch::inRandomOrder()->first()->id,
        'settings' => fn() => '{"show_on_exchange_page": ' . $faker->randomElement([1, 0]) . '}',
    ],
    [
        'name' => fn() => $faker->word(),
        'branch_id' => '',
        'settings' => fn() => '{"show_on_exchange_page": ' . $faker->randomElement([1, 0]) . '}',
    ]
]);

