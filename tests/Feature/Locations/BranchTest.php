<?php

use App\Models\Locations\{Country, Region, City, Branch};
use Symfony\Component\HttpFoundation\Response;
use Faker\Factory;

it('create branch', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');

    Country::factory(1)->create();
    Region::factory(1)->create();
    City::factory(1)->create();
    $faker = Factory::create();
    $payload = [
        'city_id' => City::inRandomOrder()->first()->id,
        'name' => $faker->streetAddress()
    ];

    $response = $this->withToken($token)
        ->json('POST', BRANCH_URL, $payload)
        ->assertCreated()
        ->assertJsonStructure(['id', 'city', 'name']);

    $branch = Branch::find($response->json('id'));
    $this->assertEquals($branch->name, $response->json('name'));
});

it('update branch', function ($name) {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Branch::latest()->first()->id;
    $city_id = City::inRandomOrder()->first()->id;
    $this->withToken($token)
        ->json('PATCH', sprintf('%s/%s', BRANCH_URL, $id), array_filter(compact('city_id', 'name')))
        ->assertStatus(Response::HTTP_ACCEPTED);

    $branch = Branch::find($id);
    $this->assertEquals($branch->name, $name);
})->with('branches');

it('update branch with wrong name', function ($name) {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Branch::latest()->first()->id;
    $city_id = City::inRandomOrder()->first()->id;
    $this->withToken($token)
        ->json('PATCH', sprintf('%s/%s', BRANCH_URL, $id), array_filter(compact('city_id', 'name')))
        ->assertUnprocessable();
})->with('wrong_branches');

it('read branch list', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Branch::latest()->first()->id;
    $responseId = $this->withToken($token)
        ->json('GET', BRANCH_URL)
        ->assertOK()
        ->assertJsonStructure(['items' => [['id', 'city', 'name']]])
        ->json('items.0.id');
    $this->assertEquals($responseId, $id);
});

it('read single branch', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Branch::latest()->first()->id;
    $responseId = $this->withToken($token)
        ->json('GET', sprintf('%s/%s', BRANCH_URL, $id))
        ->assertOK()
        ->assertJsonStructure(['id', 'city', 'name'])
        ->json('id');
    $this->assertEquals($responseId, $id);
});

it('drop branch', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Branch::latest()->first()->id;
    $this->withToken($token)
        ->json('DELETE', sprintf('%s/%s', BRANCH_URL, $id))
        ->assertNoContent();
    $this->assertEquals(Branch::find($id), null);
});

it('bulk drop branches', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Branch::latest()->first()->id;
    $this->withToken($token)
        ->json('DELETE', BRANCH_URL . '?ids[]=' . $id)
        ->assertNoContent();
    $this->assertEquals(Branch::find($id), null);
});
