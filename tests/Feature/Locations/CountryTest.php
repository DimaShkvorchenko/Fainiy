<?php

use App\Models\Locations\Country;
use Symfony\Component\HttpFoundation\Response;
use Faker\Factory;

it('create country', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');

    $faker = Factory::create();

    do {
        $iso_code = $faker->unique()->countryCode();
    } while (Country::withTrashed()->where('iso_code', $iso_code)->first());

    $payload = [
        'iso_code' => $iso_code,
        'name' => $faker->country(),
    ];

    $response = $this->withToken($token)
        ->json('POST', COUNTRY_URL, $payload)
        ->assertCreated()
        ->assertJsonStructure(['id', 'iso_code', 'name']);

    $country = Country::find($response->json('id'));
    $this->assertEquals($country->iso_code, $response->json('iso_code'));
    $this->assertEquals($country->name, $response->json('name'));

    $this->withToken($token)
        ->json('POST', COUNTRY_URL, $payload)
        ->assertUnprocessable();
});

it('update country', function ($name) {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Country::latest()->first()->id;
    $this->withToken($token)
        ->json('PATCH', sprintf('%s/%s', COUNTRY_URL, $id), array_filter(compact('name')))
        ->assertStatus(Response::HTTP_ACCEPTED);

    if ($name) $this->assertEquals(Country::find($id)->name, $name);
})->with('countries');

it('update country with wrong name', function ($name) {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Country::latest()->first()->id;
    $this->withToken($token)
        ->json('PATCH', sprintf('%s/%s', COUNTRY_URL, $id), array_filter(compact('name')))
        ->assertUnprocessable();
})->with('wrong_countries');

it('read country list', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Country::latest()->first()->id;
    $responseId = $this->withToken($token)
        ->json('GET', COUNTRY_URL)
        ->assertOK()
        ->assertJsonStructure(['items' => [['id', 'iso_code', 'name']]])
        ->json('items.0.id');
    $this->assertEquals($responseId, $id);
});

it('read single country', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Country::latest()->first()->id;
    $responseId = $this->withToken($token)
        ->json('GET', sprintf('%s/%s', COUNTRY_URL, $id))
        ->assertOK()
        ->assertJsonStructure(['id', 'iso_code', 'name'])
        ->json('id');
    $this->assertEquals($responseId, $id);
});

it('drop country', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Country::latest()->first()->id;
    $this->withToken($token)
        ->json('DELETE', sprintf('%s/%s', COUNTRY_URL, $id))
        ->assertNoContent();
    $this->assertEquals(Country::find($id), null);
});

it('bulk drop countries', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Country::latest()->first()->id;
    $this->withToken($token)
        ->json('DELETE', COUNTRY_URL . '?ids[]=' . $id)
        ->assertNoContent();
    $this->assertEquals(Country::find($id), null);
});
