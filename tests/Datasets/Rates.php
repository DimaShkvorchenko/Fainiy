<?php

use App\Models\Locations\{Country, Region, City, Branch};
use App\Models\Cashbox;
use Faker\Factory;

$faker = Factory::create();

dataset('rates', [
    [
        'amount' => $faker->randomFloat(2, 100, 10000),
        'params' => fn() => '?cashboxes[]=' . Cashbox::inRandomOrder()->first()->id
            . '&branches[]=' . Branch::inRandomOrder()->first()->id
            . '&cities[]=' . City::inRandomOrder()->first()->id
            . '&regions[]=' . Region::inRandomOrder()->first()->id
            . '&countries[]=' . Country::inRandomOrder()->first()->id,
    ],
    [
        'amount' => $faker->randomFloat(2, 100, 10000),
        'params' => fn() => '?cashboxes[]=' . Cashbox::inRandomOrder()->first()->id,
    ],
    [
        'amount' => $faker->randomFloat(2, 100, 10000),
        'params' => fn() => '?branches[]=' . Branch::inRandomOrder()->first()->id,
    ],
    [
        'amount' => $faker->randomFloat(2, 100, 10000),
        'params' => fn() => '?cities[]=' . City::inRandomOrder()->first()->id,
    ],
    [
        'amount' => $faker->randomFloat(2, 100, 10000),
        'params' => fn() => '?regions[]=' . Region::inRandomOrder()->first()->id,
    ],
    [
        'amount' => $faker->randomFloat(2, 100, 10000),
        'params' => fn() => '?countries[]=' . Country::inRandomOrder()->first()->id,
    ]
]);

dataset('wrong_rates', [
    [
        'amount' => null,
        'params' => fn() => '?cashboxes[]=' . Cashbox::inRandomOrder()->first()->id
            . '&branches[]=' . Branch::inRandomOrder()->first()->id
            . '&cities[]=' . City::inRandomOrder()->first()->id
            . '&regions[]=' . Region::inRandomOrder()->first()->id
            . '&countries[]=' . Country::inRandomOrder()->first()->id
    ],
    [
        'amount' => false,
        'params' => fn() => '?branches[]=' . Branch::inRandomOrder()->first()->id
    ],
    [
        'amount' => $faker->randomFloat(2, 100, 10000),
        'params' => fn() => '?cashboxes[]=string'
    ],
    [
        'amount' => $faker->randomFloat(2, 100, 10000),
        'params' => fn() => '?branches[]=string'
    ],
    [
        'amount' => $faker->randomFloat(2, 100, 10000),
        'params' => fn() => '?cities[]=string'
    ],
    [
        'amount' => $faker->randomFloat(2, 100, 10000),
        'params' => fn() => '?regions[]=string'
    ],
    [
        'amount' => $faker->randomFloat(2, 100, 10000),
        'params' => fn() => '?countries[]=string'
    ]
]);

