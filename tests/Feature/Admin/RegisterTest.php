<?php

use App\Models\User;
use Faker\Factory;

it('create admin user', function () {
    $faker = Factory::create();

    for ($i=0; $i < 100; $i++) {
        $email = $faker->unique()->safeEmail();
        if (empty(User::where('email', $email)->first()->id)) {
            break;
        }
    }
    $payload = [
        'first_name' => $faker->firstName(),
        'last_name' => $faker->lastName(),
        'email' => $email,
        'password' => '123456',
        'password_confirmation' => '123456',
    ];

    $responseId = $this->json('POST', REGISTER_URL, $payload)
        ->assertCreated()
        ->json('id');
    $id = User::where('email', $email)->first()->id;
    $this->assertEquals($responseId, $id);
    $this->assertEquals(User::find($id)->account_type, User::ADMIN_TYPE);

    $this->json('POST', REGISTER_URL, $payload)
        ->assertUnprocessable();
});
