<?php

use App\Models\Locations\{Country, Region};
use Symfony\Component\HttpFoundation\Response;
use Faker\Factory;

it('create region', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');

    Country::factory(1)->create();
    $faker = Factory::create();
    $payload = [
        'country_id' => Country::withoutTrashed()->latest()->first()->id,
        'name' => $faker->state()
    ];

    $response = $this->withToken($token)
        ->json('POST', REGION_URL, $payload)
        ->assertCreated()
        ->assertJsonStructure(['id', 'country', 'name']);

    $region = Region::find($response->json('id'));
    $this->assertEquals($region->name, $response->json('name'));
});

it('update region', function ($name) {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Region::latest()->first()->id;
    $country_id = Country::withoutTrashed()->latest()->first()->id;
    $this->withToken($token)
        ->json('PATCH', sprintf('%s/%s', REGION_URL, $id), array_filter(compact('country_id', 'name')))
        ->assertStatus(Response::HTTP_ACCEPTED);

    $region = Region::find($id);
    //$this->assertEquals($region->country_id, $country_id);
    $this->assertEquals($region->name, $name);
})->with('regions');

it('update region with wrong name', function ($name) {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Region::latest()->first()->id;
    $country_id = Country::withoutTrashed()->latest()->first()->id;
    $this->withToken($token)
        ->json('PATCH', sprintf('%s/%s', REGION_URL, $id), array_filter(compact('country_id', 'name')))
        ->assertUnprocessable();
})->with('wrong_regions');

it('read region list', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Region::latest()->first()->id;
    $responseId = $this->withToken($token)
        ->json('GET', REGION_URL)
        ->assertOK()
        ->assertJsonStructure(['items' => [['id', 'country', 'name']]])
        ->json('items.0.id');
    $this->assertEquals($responseId, $id);
});

it('read single region', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Region::latest()->first()->id;
    $responseId = $this->withToken($token)
        ->json('GET', sprintf('%s/%s', REGION_URL, $id))
        ->assertOK()
        ->assertJsonStructure(['id', 'country', 'name'])
        ->json('id');
    $this->assertEquals($responseId, $id);
});

it('drop region', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Region::latest()->first()->id;
    $this->withToken($token)
        ->json('DELETE', sprintf('%s/%s', REGION_URL, $id))
        ->assertNoContent();
    $this->assertEquals(Region::find($id), null);
});

it('bulk drop regions', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Region::latest()->first()->id;
    $this->withToken($token)
        ->json('DELETE', REGION_URL . '?ids[]=' . $id)
        ->assertNoContent();
    $this->assertEquals(Region::find($id), null);
});
