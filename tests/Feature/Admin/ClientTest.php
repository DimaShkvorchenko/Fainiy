<?php

use App\Models\User;
use App\Services\Commission\IncomeCommission;
use Symfony\Component\HttpFoundation\Response;
use Faker\Factory;

it('create client', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');

    $faker = Factory::create();

    do {
        $email = $faker->unique()->safeEmail();
    } while (User::withTrashed()->where('email', $email)->first());

    do {
        $firstName = $faker->unique()->firstName();
        $lastName = $faker->unique()->lastName();
        $telegram = $firstName . $lastName;
    } while (User::withTrashed()->where('telegram', $telegram)->first());

    $firstName = $faker->firstName();
    $income_commission = (new IncomeCommission())->getOrSetCommissionSetting()->value;

    $payload = [
        'first_name' => $firstName,
        'last_name' => $lastName,
        'email' => $email,
        'password' => '123456',
        'password_confirmation' => '123456',
        'phone' => $faker->phoneNumber(),
        'telegram' => $telegram,
        'code' => $faker->ean8(),
        'modules' => json_encode(["transfer", "exchange"]),
        'registration_data' => json_encode([
            'IP' => $faker->ipv4(),
            'time_of_issue' => $faker->dateTimeBetween('-30 days', '+30 days')->format('Y-m-d H:i:s')
        ]),
        'other_data' => json_encode([
            'nickname' => $faker->name(),
            'phone2' => $faker->phoneNumber(),
            'referral' => $faker->uuid(),
            'income_commission' => $income_commission
        ]),
    ];

    $response = $this->withToken($token)
        ->json('POST', CLIENTS_URL, $payload)
        ->assertCreated()
        ->assertJsonStructure([
            'id',
            'first_name',
            'last_name',
            'phone',
            'telegram',
            'code',
            'modules',
            'registration_data',
            'other_data'
        ]);

    $client = User::find($response->json('id'));
    $this->assertEquals($client->first_name, $response->json('first_name'));
    $this->assertEquals($client->last_name, $response->json('last_name'));
    $this->assertEquals($client->phone, $response->json('phone'));
    $this->assertEquals($client->telegram, $response->json('telegram'));
    $this->assertEquals($client->code, $response->json('code'));
    $this->assertEquals($client->account_type, User::CLIENT_TYPE);

    $this->withToken($token)
        ->json('POST', CLIENTS_URL, $payload)
        ->assertUnprocessable();
});

it('update client', function (
    $first_name,
    $last_name,
    $phone,
    $telegram,
    $modules,
    $other_data
) {
    createAdmin();
    $id = User::where('account_type', User::CLIENT_TYPE)->latest()->first()->id;
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $this->withToken($token)
        ->json('PATCH',
            sprintf('%s/%s', CLIENTS_URL, $id),
            compact(
                'first_name',
                'last_name',
                'phone',
                'telegram',
                'modules',
                'other_data'
            )
        )
        ->assertStatus(Response::HTTP_ACCEPTED);
    $client = User::find($id);
    if ($first_name) {
        $this->assertEquals($client->first_name, $first_name);
    }
    if ($last_name) {
        $this->assertEquals($client->last_name, $last_name);
    }
    if ($phone) {
        $this->assertEquals($client->phone, $phone);
    }
    if ($telegram) {
        $this->assertEquals($client->telegram, $telegram);
    }
    $this->assertEquals($client->account_type, User::CLIENT_TYPE);
})->with('clients');

it('read clients', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = User::where('account_type', User::CLIENT_TYPE)->latest()->first()->id;
    $responseId = $this->withToken($token)
        ->json('GET', CLIENTS_URL)
        ->assertOK()
        ->assertJsonStructure(['items' => [[
            'id',
            'first_name',
            'last_name',
            'phone',
            'telegram',
            'code',
            'modules',
            'registration_data',
            'other_data'
        ]]])
        ->json('items.0.id');
    $this->assertEquals($responseId, $id);
});

it('read single client', function () {
    createAdmin();
    $id = User::where('account_type', User::CLIENT_TYPE)->latest()->first()->id;
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $responseId = $this->withToken($token)
        ->json('GET', sprintf('%s/%s', CLIENTS_URL, $id))
        ->assertOK()
        ->assertJsonStructure([
            'id',
            'first_name',
            'last_name',
            'phone',
            'telegram',
            'code',
            'modules',
            'registration_data',
            'other_data'
        ])
        ->json('id');
    $this->assertEquals($responseId, $id);
});

it('missing client', function ($id) {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    foreach(['GET', 'PUT', 'DELETE'] as $method){
        $this->withToken($token)
            ->json($method, sprintf('%s/%s', CLIENTS_URL, $id))
            ->assertNotFound();
    }
})->with('missing_clients');

it('drop client', function () {
    createAdmin();
    $id = User::where('account_type', User::CLIENT_TYPE)->latest()->first()->id;
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $this->withToken($token)
        ->json('DELETE', sprintf('%s/%s', CLIENTS_URL, $id))
        ->assertNoContent();
    $this->assertEquals(User::find($id), null);
});

it('bulk drop clients', function () {
    createAdmin();
    $id = User::where('account_type', User::CLIENT_TYPE)->latest()->first()->id;
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $this->withToken($token)
        ->json('DELETE', CLIENTS_URL . '?ids[]=' . $id)
        ->assertNoContent();
    $this->assertEquals(User::find($id), null);
});
