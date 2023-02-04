<?php

use App\Models\{Cashbox, Currency, Rate};
use App\Models\Locations\{Country, Region, City, Branch};
use Symfony\Component\HttpFoundation\Response;
use Faker\Factory;

it('create rate', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');

    //Country::factory(1)->create();
    Region::factory(1)->create();
    City::factory(1)->create();
    Branch::factory(1)->create();
    Cashbox::factory(1)->create();

    $faker = Factory::create();
    $payload = [
        'cashbox_id' => Cashbox::inRandomOrder()->first()->id,
    ];

    $this->withToken($token)
        ->json('POST', RATE_URL . '?currencies[]=' . Currency::inRandomOrder()->first()->id, $payload)
        ->assertCreated();
});

it('read rate list', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Rate::latest()->first()->id;
    $responseId = $this->withToken($token)
        ->json('GET', RATE_URL)
        ->assertOK()
        ->assertJsonStructure(['items' => [['id', 'from_currency', 'to_currency', 'amount', 'cashbox']]])
        ->json('items.0.id');
    //$this->assertEquals($responseId, $id);
});

it('read single rate', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Rate::latest()->first()->id;
    $responseId = $this->withToken($token)
        ->json('GET', sprintf('%s/%s', RATE_URL, $id))
        ->assertOK()
        ->assertJsonStructure(['items' => [['id', 'from_currency', 'to_currency', 'amount', 'cashbox']]])
        ->json('items.0.id');
    $this->assertEquals($responseId, $id);
});

it('update rate', function ($amount, $params) {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Rate::latest()->first()->id;
    $this->withToken($token)
        ->json('PATCH', RATE_URL . '/'. $id . $params, array_filter(compact('amount')))
        ->assertStatus(Response::HTTP_ACCEPTED);

    $this->assertEquals(Rate::find($id)->amount, $amount);
})->with('rates');

it('update rate with wrong amount/branches', function ($amount, $params) {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Rate::latest()->first()->id;
    $this->withToken($token)
        ->json('PATCH', RATE_URL . '/'. $id . $params, array_filter(compact('amount')))
        ->assertUnprocessable();
})->with('wrong_rates');

it('drop rate', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Rate::latest()->first()->id;
    $this->withToken($token)
        ->json('DELETE', sprintf('%s/%s', RATE_URL, $id))
        ->assertNoContent();
    $this->assertEquals(Rate::find($id), null);
});

it('bulk drop rates', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Rate::latest()->first()->id;
    $this->withToken($token)
        ->json('DELETE', RATE_URL . '?ids[]=' . $id)
        ->assertNoContent();
    $this->assertEquals(Rate::find($id), null);
});
