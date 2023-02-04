<?php

use App\Models\User;
use Symfony\Component\HttpFoundation\Response;
use Faker\Factory;

it('create staff', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');

    $faker = Factory::create();

    do {
        $email = $faker->unique()->safeEmail();
    } while (!empty(User::withTrashed()->where('email', $email)->first()->id));

    $payload = [
        'first_name' => $faker->firstName(),
        'last_name' => $faker->lastName(),
        'email' => $email,
        'password' => '123456',
        'password_confirmation' => '123456',
        'phone' => $faker->phoneNumber(),
        'role' => '["cashier"]',
    ];

    $response = $this->withToken($token)
        ->json('POST', STAFF_URL, $payload)
        ->assertCreated()
        ->assertJsonStructure([
            'id',
            'first_name',
            'last_name',
            'email',
            'phone',
            'role',
        ]);

    $staff = User::find($response->json('id'));
    $this->assertEquals($staff->first_name, $response->json('first_name'));
    $this->assertEquals($staff->last_name, $response->json('last_name'));
    $this->assertEquals($staff->email, $response->json('email'));
    $this->assertEquals($staff->phone, $response->json('phone'));
    $this->assertEquals($staff->role, $response->json('role'));
    $this->assertEquals($staff->account_type, User::STAFF_TYPE);

    $this->withToken($token)
        ->json('POST', STAFF_URL, $payload)
        ->assertUnprocessable();
});

it('update staff', function ($first_name, $last_name, $phone) {
    createAdmin();
    $id = User::where('account_type', User::STAFF_TYPE)->latest()->first()->id;
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $this->withToken($token)
        ->json('PATCH',
            sprintf('%s/%s', STAFF_URL, $id),
            array_filter(compact('first_name', 'last_name', 'phone')))
        ->assertStatus(Response::HTTP_ACCEPTED);
    $staff = User::find($id);
    if ($first_name) $this->assertEquals($staff->first_name, $first_name);
    if ($last_name) $this->assertEquals($staff->last_name, $last_name);
    if ($phone) $this->assertEquals($staff->phone, $phone);
    $this->assertEquals($staff->account_type, User::STAFF_TYPE);

})->with('staff');

it('read staff list', function () {
    createAdmin();
    $id = User::where('account_type', User::STAFF_TYPE)->latest()->first()->id;
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $responseId = $this->withToken($token)
        ->json('GET', STAFF_URL)
        ->assertOK()
        ->assertJsonStructure(['items' => [['id', 'first_name', 'last_name', 'email', 'phone', 'role']]])
        ->json('items.0.id');
    $this->assertEquals($responseId, $id);
});

it('read single staff', function () {
    createAdmin();
    $id = User::where('account_type', User::STAFF_TYPE)->latest()->first()->id;
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $responseId = $this->withToken($token)
        ->json('GET', sprintf('%s/%s', STAFF_URL, $id))
        ->assertOK()
        ->assertJsonStructure(['id', 'first_name', 'last_name', 'email', 'phone', 'role'])
        ->json('id');
    $this->assertEquals($responseId, $id);
});

it('missing staff', function ($id) {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    foreach(['GET', 'PUT', 'DELETE'] as $method) {
        $this->withToken($token)
            ->json($method, sprintf('%s/%s', CLIENTS_URL, $id))
            ->assertNotFound();
    }
})->with('missing_staff');

it('drop staff', function () {
    createAdmin();
    $id = User::where('account_type', User::STAFF_TYPE)->latest()->first()->id;
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $this->withToken($token)
        ->json('DELETE', sprintf('%s/%s', STAFF_URL, $id))
        ->assertNoContent();
    $this->assertEquals(User::find($id), null);
});

it('bulk drop staff', function () {
    createAdmin();
    $id = User::where('account_type', User::STAFF_TYPE)->latest()->first()->id;
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $this->withToken($token)
        ->json('DELETE', STAFF_URL . '?ids[]=' . $id)
        ->assertNoContent();
    $this->assertEquals(User::find($id), null);
});
