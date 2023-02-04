<?php

use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

it('update profile', function ($first_name, $last_name) {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    User::factory(1)->create([
        'account_type' => 3,
        'admin_id' => User::admin()->inRandomOrder()->first()
    ]);

    $id = User::where('account_type', User::CLIENT_TYPE)->latest()->first()->id;
    $response = $this->withToken($token)
        ->json(
            'PATCH', sprintf('%s/%s', PROFILE_URL, $id),
            compact('first_name', 'last_name')
        )
        ->assertStatus(Response::HTTP_ACCEPTED);

    $profile = User::find($response->json('id'));
    $this->assertEquals($profile->first_name, $response->json('first_name'));
    $this->assertEquals($profile->last_name, $response->json('last_name'));
})->with('profiles');

it('update profile with wrong first_name/last_name', function ($first_name, $last_name) {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = User::where('account_type', User::CLIENT_TYPE)->latest()->first()->id;
    $response = $this->withToken($token)
        ->json(
            'PATCH', sprintf('%s/%s', PROFILE_URL, $id),
            compact('first_name', 'last_name'))
        ->assertUnprocessable();
})->with('wrong_profiles');

it('read profile', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = User::where('account_type', User::CLIENT_TYPE)->latest()->first()->id;
    $responseId = $this->withToken($token)
        ->json('GET', sprintf('%s/%s', PROFILE_URL, $id))
        ->assertOK()
        ->assertJsonStructure(['id', 'first_name', 'last_name'])
        ->json('id');
    $this->assertEquals($responseId, $id);
});
