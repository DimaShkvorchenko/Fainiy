<?php

use App\Models\{Cashbox, Currency, Transfer, User};
use App\Models\Locations\{Country, Region, City, Branch};
use Symfony\Component\HttpFoundation\Response;
use Faker\Factory;

it('create transfer', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');

    User::factory(1)->create([
        'account_type' => 3,
        'admin_id' => User::admin()->inRandomOrder()->first()
    ]);
    //Country::factory(1)->create();
    Region::factory(1)->create();
    City::factory(1)->create();
    Branch::factory(2)->create();

    $from_cashbox_id = Cashbox::latest()->first()->id;
    do {
        $to_cashbox_id = Cashbox::inRandomOrder()->first()->id;
    } while ($from_cashbox_id == $to_cashbox_id);

    $faker = Factory::create();
    $time_of_issue = $faker->dateTimeBetween('-30 days', '+30 days');
    $payload = [
        'access_code' => $faker->randomNumber(4, true),
        'amount' => $faker->randomFloat(2, 100, 100000),
        'commission' => json_encode([
            'transfer_commission' => $faker->randomFloat(2, 1, 100)
        ]),
        'user_id' => User::where('account_type', User::STAFF_TYPE)->inRandomOrder()->first()->id,
        'client_id' => User::where('account_type', User::CLIENT_TYPE)
            ->where('modules', 'like', '%transfer%')
            ->inRandomOrder()->first()->id,
        'from_cashbox_id' => $from_cashbox_id,
        'to_cashbox_id' => $to_cashbox_id,
        'currency_id' => Currency::inRandomOrder()->first()->id,
        'description' => $faker->sentence(),
        'status' => 'new',
        'time_of_issue' => $time_of_issue->format('Y-m-d H:i:s'),
    ];

    $response = $this->withToken($token)
        ->json('POST', TRANSFER_URL, $payload)
        ->assertCreated()
        ->assertJsonStructure([
            'id',
            'access_code',
            'amount',
            'commission',
            'user',
            'client',
            'from_cashbox',
            'to_cashbox',
            'currency',
            'description',
            'status',
            'time_of_issue',
        ]);

    $transfer = Transfer::find($response->json('id'));
    $this->assertEquals($transfer->access_code, $response->json('access_code'));
    $this->assertEquals($transfer->amount, $response->json('amount'));
    $this->assertEquals($transfer->description, $response->json('description'));
    $this->assertEquals($transfer->status, $response->json('status'));
    $this->assertEquals($transfer->time_of_issue, $response->json('time_of_issue'));
});

it('update transfer', function (
    $access_code,
    $amount,
    $to_cashbox_id,
    $description,
    $status,
    $time_of_issue
) {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Transfer::latest()->first()->id;
    $response = $this->withToken($token)
        ->json(
            'PATCH', sprintf('%s/%s', TRANSFER_URL, $id),
            compact(
                'access_code',
                'amount',
                'to_cashbox_id',
                'description',
                'status',
                'time_of_issue'
            )
        )
        ->assertStatus(Response::HTTP_ACCEPTED);

    $transfer = Transfer::find($response->json('id'));
    $this->assertEquals($transfer->access_code, $response->json('access_code'));
    $this->assertEquals($transfer->amount, $response->json('amount'));
    $this->assertEquals($transfer->description, $response->json('description'));
    $this->assertEquals($transfer->status, $response->json('status'));
    $this->assertEquals($transfer->time_of_issue, $response->json('time_of_issue'));

})->with('transfers');

it('update transfer with wrong fields', function (
    $access_code,
    $amount,
    $to_cashbox_id,
    $description,
    $status,
    $time_of_issue
) {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Transfer::latest()->first()->id;
    $this->withToken($token)
        ->json(
            'PATCH', sprintf('%s/%s', TRANSFER_URL, $id),
            compact(
                'access_code',
                'amount',
                'to_cashbox_id',
                'description',
                'status',
                'time_of_issue'
            )
        )
        ->assertUnprocessable();
})->with('wrong_transfers');

it('read transfer list', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Transfer::latest()->first()->id;
    $responseId = $this->withToken($token)
        ->json('GET', TRANSFER_URL)
        ->assertOK()
        ->assertJsonStructure(['items' => [[
            'id',
            'access_code',
            'amount',
            'commission',
            'user',
            'client',
            'from_cashbox',
            'to_cashbox',
            'currency',
            'description',
            'status',
            'time_of_issue',
        ]]])
        ->json('items.0.id');
    $this->assertEquals($responseId, $id);
});

it('read single transfer', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Transfer::latest()->first()->id;
    $responseId = $this->withToken($token)
        ->json('GET', sprintf('%s/%s', TRANSFER_URL, $id))
        ->assertOK()
        ->assertJsonStructure([
            'id',
            'access_code',
            'amount',
            'commission',
            'user',
            'client',
            'from_cashbox',
            'to_cashbox',
            'currency',
            'description',
            'status',
            'time_of_issue',
        ])
        ->json('id');
    $this->assertEquals($responseId, $id);
});

it('drop transfer', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Transfer::latest()->first()->id;
    $this->withToken($token)
        ->json('DELETE', sprintf('%s/%s', TRANSFER_URL, $id));
    $this->assertEquals(Transfer::find($id), null);
});

it('set status done for transfer', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    Transfer::factory(1)->create(['status' => 'new']);
    $id = Transfer::where('status', 'new')->first()->id;
    $response = $this->withToken($token)
        ->json('POST', str_replace("ID", $id, TRANSFER_STATUS_DONE_URL))
        ->assertStatus(Response::HTTP_ACCEPTED);

    $transfer = Transfer::find($response->json('id'));
    $this->assertEquals($transfer->status, 'done');
});

it('set status cancelled for transfer', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    Transfer::factory(1)->create(['status' => 'new']);
    $id = Transfer::where('status', 'new')->first()->id;
    $response = $this->withToken($token)
        ->json('POST', str_replace("ID", $id, TRANSFER_STATUS_CANCELLED_URL))
        ->assertStatus(Response::HTTP_ACCEPTED);

    $transfer = Transfer::find($response->json('id'));
    $this->assertEquals($transfer->status, 'cancelled');
});
