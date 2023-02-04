<?php

use App\Models\{Setting, Currency};
use Symfony\Component\HttpFoundation\Response;
use Faker\Factory;

it('create currency', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');

    $faker = Factory::create();

    $payload = [
        'name' => $faker->word(),
        'is_visible' => $faker->randomElement([0, 1]),
    ];

    $iso_code = $faker->unique()->currencyCode();
    if (empty(Currency::withTrashed()->where('iso_code', $iso_code)->first()->id)) {
        $payload['iso_code'] = $iso_code;
    }

    $response = $this->withToken($token)
        ->json('POST', CURRENCY_URL, $payload)
        ->assertCreated()
        ->assertJsonStructure(['id', 'iso_code', 'name', "is_visible"]);

    $currency = Currency::find($response->json('id'));
    $this->assertEquals($currency->name, $response->json('name'));
    if (!empty($payload['iso_code'])) {
        $this->assertEquals($currency->iso_code, $response->json('iso_code'));

        $this->withToken($token)
            ->json('POST', CURRENCY_URL, $payload)
            ->assertUnprocessable();
    }
});

it('update currency', function ($name, $is_visible) {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $basicCurrencySetting = Setting::withTrashed()->where('code', 'basic_currency')->first();
    $id = Currency::where('id', '<>', $basicCurrencySetting->value)->latest()->first()->id;
    $this->withToken($token)
        ->json('PATCH', sprintf('%s/%s', CURRENCY_URL, $id), array_filter(compact('name', 'is_visible')))
        ->assertStatus(Response::HTTP_ACCEPTED);

    if ($name) $this->assertEquals(Currency::find($id)->name, $name);
})->with('currencies');

it('update currency with wrong name', function ($name) {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $basicCurrencySetting = Setting::withTrashed()->where('code', 'basic_currency')->first();
    $id = Currency::where('id', '<>', $basicCurrencySetting->value)->latest()->first()->id;
    $this->withToken($token)
        ->json('PATCH', sprintf('%s/%s', CURRENCY_URL, $id), array_filter(compact('name')))
        ->assertUnprocessable();
})->with('wrong_currencies');

it('read currency list', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Currency::latest()->first()->id;
    $responseId = $this->withToken($token)
        ->json('GET', CURRENCY_URL)
        ->assertOK()
        ->assertJsonStructure(['items' => [['id', 'iso_code', 'name', "is_visible"]]])
        ->json('items.0.id');
    $this->assertEquals($responseId, $id);
});

it('read single currency', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Currency::latest()->first()->id;
    $responseId = $this->withToken($token)
        ->json('GET', sprintf('%s/%s', CURRENCY_URL, $id))
        ->assertOK()
        ->assertJsonStructure(['id', 'iso_code', 'name', "is_visible"])
        ->json('id');
    $this->assertEquals($responseId, $id);
});

it('drop currency', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $basicCurrencySetting = Setting::withTrashed()->where('code', 'basic_currency')->first();
    $id = Currency::where('id', '<>', $basicCurrencySetting->value)->latest()->first()->id;
    $this->withToken($token)
        ->json('DELETE', sprintf('%s/%s', CURRENCY_URL, $id))
        ->assertNoContent();
    $this->assertEquals(Currency::find($id), null);
});

it('bulk drop currencies', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $basicCurrencySetting = Setting::withTrashed()->where('code', 'basic_currency')->first();
    $id = Currency::where('id', '<>', $basicCurrencySetting->value)->latest()->first()->id;
    $this->withToken($token)
        ->json('DELETE', CURRENCY_URL . '?ids[]=' . $id)
        ->assertNoContent();
    $this->assertEquals(Currency::find($id), null);
});
