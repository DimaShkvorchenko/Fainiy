<?php

use App\Models\User;
use Faker\Factory;

$faker = Factory::create();

dataset('tasks', [
    [
        'staff_id' => fn() => User::where('account_type', User::STAFF_TYPE)->inRandomOrder()->first()->id,
        'title' => fn() => $faker->text(20),
        'description' => fn() => $faker->text(50),
        'tags' => fn() => $faker->text(5),
        'file' => fn() => $faker->url(),
        'completion_date' => fn() => $faker->dateTimeBetween('-30 days', '+30 days')->format('Y-m-d H:i:s'),

    ],
    [
        'staff_id' => fn() => User::where('account_type', User::STAFF_TYPE)->inRandomOrder()->first()->id,
        'title' => fn() => $faker->text(20),
        'description' => fn() => $faker->text(50),
        'tags' => fn() => $faker->text(5),
        'file' => fn() => $faker->url(),
        'completion_date' => fn() => $faker->dateTimeBetween('-30 days', '+30 days')->format('Y-m-d H:i:s'),
    ]
]);

dataset('wrong_tasks', [
    [
        'staff_id' => 'string',
        'title' => fn() => $faker->text(20),
        'description' => fn() => $faker->text(50),
        'tags' => fn() => $faker->text(5),
        'file' => fn() => $faker->url(),
        'completion_date' => fn() => $faker->dateTimeBetween('-30 days', '+30 days')->format('Y-m-d H:i:s'),
    ],
    [
        'staff_id' => fn() => User::where('account_type', User::STAFF_TYPE)->inRandomOrder()->first()->id,
        'title' => 9,
        'description' => fn() => $faker->text(50),
        'tags' => fn() => $faker->text(5),
        'file' => fn() => $faker->url(),
        'completion_date' => fn() => $faker->dateTimeBetween('-30 days', '+30 days')->format('Y-m-d H:i:s'),
    ],
    [
        'staff_id' => fn() => User::where('account_type', User::STAFF_TYPE)->inRandomOrder()->first()->id,
        'title' => fn() => $faker->text(20),
        'description' => 9,
        'tags' => fn() => $faker->text(5),
        'file' => fn() => $faker->url(),
        'completion_date' => fn() => $faker->dateTimeBetween('-30 days', '+30 days')->format('Y-m-d H:i:s'),
    ],
    [
        'staff_id' => fn() => User::where('account_type', User::STAFF_TYPE)->inRandomOrder()->first()->id,
        'title' => fn() => $faker->text(20),
        'description' => fn() => $faker->text(50),
        'tags' => 9,
        'file' => fn() => $faker->url(),
        'completion_date' => fn() => $faker->dateTimeBetween('-30 days', '+30 days')->format('Y-m-d H:i:s'),
    ],
    [
        'staff_id' => fn() => User::where('account_type', User::STAFF_TYPE)->inRandomOrder()->first()->id,
        'title' => fn() => $faker->text(20),
        'description' => fn() => $faker->text(50),
        'tags' => fn() => $faker->text(5),
        'file' => 9,
        'completion_date' => fn() => $faker->dateTimeBetween('-30 days', '+30 days')->format('Y-m-d H:i:s'),
    ],
    [
        'staff_id' => fn() => User::where('account_type', User::STAFF_TYPE)->inRandomOrder()->first()->id,
        'title' => fn() => $faker->text(20),
        'description' => fn() => $faker->text(50),
        'tags' => fn() => $faker->text(5),
        'file' => fn() => $faker->url(),
        'completion_date' => 'data',
    ]
]);

