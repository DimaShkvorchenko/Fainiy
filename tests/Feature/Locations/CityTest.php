<?php

use App\Models\Locations\{Country, Region, City};
use Symfony\Component\HttpFoundation\Response;
use Faker\Factory;

it('create city', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');

    Country::factory(1)->create();
    Region::factory(1)->create();
    $faker = Factory::create();
    $payload = [
        'region_id' => Region::withoutTrashed()->latest()->first()->id,
        'name' => $faker->city()
    ];

    $response = $this->withToken($token)
        ->json('POST', CITY_URL, $payload)
        ->assertCreated()
        ->assertJsonStructure(['id', 'region', 'name']);

    $city = City::find($response->json('id'));
    $this->assertEquals($city->name, $response->json('name'));
});

it('update city', function ($name) {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = City::latest()->first()->id;
    $region_id = Region::withoutTrashed()->latest()->first()->id;
    $this->withToken($token)
        ->json('PATCH', sprintf('%s/%s', CITY_URL, $id), array_filter(compact('region_id', 'name')))
        ->assertStatus(Response::HTTP_ACCEPTED);

    $city = City::find($id);
    //$this->assertEquals($city->region_id, $region_id);
    $this->assertEquals($city->name, $name);
})->with('cities');

it('update city with wrong name', function ($name) {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = City::latest()->first()->id;
    $region_id = Region::withoutTrashed()->latest()->first()->id;
    $this->withToken($token)
        ->json('PATCH', sprintf('%s/%s', CITY_URL, $id), array_filter(compact('region_id', 'name')))
        ->assertUnprocessable();
})->with('wrong_cities');

it('read city list', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = City::latest()->first()->id;
    $responseId = $this->withToken($token)
        ->json('GET', CITY_URL)
        ->assertOK()
        ->assertJsonStructure(['items' => [['id', 'region', 'name']]])
        ->json('items.0.id');
    $this->assertEquals($responseId, $id);
});

it('read single city', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = City::latest()->first()->id;
    $responseId = $this->withToken($token)
        ->json('GET', sprintf('%s/%s', CITY_URL, $id))
        ->assertOK()
        ->assertJsonStructure(['id', 'region', 'name'])
        ->json('id');
    $this->assertEquals($responseId, $id);
});

it('drop city', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = City::latest()->first()->id;
    $this->withToken($token)
        ->json('DELETE', sprintf('%s/%s', CITY_URL, $id))
        ->assertNoContent();
    $this->assertEquals(City::find($id), null);
});

it('bulk drop cities', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = City::latest()->first()->id;
    $this->withToken($token)
        ->json('DELETE', CITY_URL . '?ids[]=' . $id)
        ->assertNoContent();
    $this->assertEquals(City::find($id), null);
});
