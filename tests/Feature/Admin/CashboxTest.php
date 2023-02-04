<?php

use App\Models\Cashbox;
use App\Models\Locations\{Country, Region, City, Branch};
use Symfony\Component\HttpFoundation\Response;
use Faker\Factory;

it('create cashbox', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');

    Country::factory(1)->create();
    Region::factory(1)->create();
    City::factory(1)->create();
    Branch::factory(1)->create();

    $faker = Factory::create();
    $payload = [
        'number' => $faker->randomNumber(6, true),
        'name' => $faker->word(),
        'branch_id' => Branch::inRandomOrder()->first()->id,
        'settings' => '{"show_on_exchange_page": ' . $faker->randomElement([1, 0]) . '}',
    ];

    $response = $this->withToken($token)
        ->json('POST', CASHBOX_URL, $payload)
        ->assertCreated()
        ->assertJsonStructure([
            'id',
            'number',
            'name',
            'branch',
            'settings',
        ]);

    $cashbox = Cashbox::find($response->json('id'));
    $this->assertEquals($cashbox->number, $response->json('number'));
    $this->assertEquals($cashbox->name, $response->json('name'));
    $this->assertEquals($cashbox->settings, $response->json('settings'));
});

it('update cashbox', function (
    $name,
    $branch_id,
    $settings,
) {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Cashbox::latest()->first()->id;
    $response = $this->withToken($token)
        ->json(
            'PATCH', sprintf('%s/%s', CASHBOX_URL, $id),
            compact('name','branch_id', 'settings')
        )
        ->assertStatus(Response::HTTP_ACCEPTED);

    $cashbox = Cashbox::find($response->json('id'));
    $this->assertEquals($cashbox->name, $response->json('name'));
    $this->assertEquals($cashbox->settings, $response->json('settings'));

})->with('cashboxes');

it('update cashbox with wrong name/branch_id', function (
    $name,
    $branch_id,
    $settings,
) {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Cashbox::latest()->first()->id;
    $this->withToken($token)
        ->json(
            'PATCH', sprintf('%s/%s', CASHBOX_URL, $id),
            compact('name','branch_id', 'settings')
        )
        ->assertUnprocessable();
})->with('wrong_cashboxes');

it('read cashbox list', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Cashbox::latest()->first()->id;
    $responseId = $this->withToken($token)
        ->json('GET', CASHBOX_URL)
        ->assertOK()
        ->assertJsonStructure(['items' => [[
            'id',
            'number',
            'name',
            'branch',
            'settings',
        ]]])
        ->json('items.0.id');
    $this->assertEquals($responseId, $id);
});

it('read single cashbox', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Cashbox::latest()->first()->id;
    $responseId = $this->withToken($token)
        ->json('GET', sprintf('%s/%s', CASHBOX_URL, $id))
        ->assertOK()
        ->assertJsonStructure([
            'id',
            'number',
            'name',
            'branch',
            'settings',
        ])
        ->json('id');
    $this->assertEquals($responseId, $id);
});

it('drop cashbox', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Cashbox::latest()->first()->id;
    $this->withToken($token)
        ->json('DELETE', sprintf('%s/%s', CASHBOX_URL, $id))
        ->assertNoContent();
    $this->assertEquals(Cashbox::find($id), null);
});

it('bulk drop cashboxes', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Cashbox::latest()->first()->id;
    $this->withToken($token)
        ->json('DELETE', CASHBOX_URL . '?ids[]=' . $id)
        ->assertNoContent();
    $this->assertEquals(Cashbox::find($id), null);
});
