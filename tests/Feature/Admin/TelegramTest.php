<?php

use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

it('update telegram', function ($telegram) {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    User::factory(1)->create([
        'account_type' => 3,
        'admin_id' => User::admin()->inRandomOrder()->first()
    ]);

    $id = User::where('account_type', User::CLIENT_TYPE)->latest()->first()->id;
    $response = $this->withToken($token)
        ->json(
            'PATCH', sprintf('%s/%s', TELEGRAM_URL, $id),
            compact('telegram')
        )
        ->assertStatus(Response::HTTP_ACCEPTED);

    $profile = User::find($response->json('id'));
    $this->assertEquals($profile->telegram, $response->json('telegram'));
})->with('telegram');

it('update telegram with wrong telegram', function ($telegram) {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = User::where('account_type', User::CLIENT_TYPE)->latest()->first()->id;
    $this->withToken($token)
        ->json(
            'PATCH', sprintf('%s/%s', TELEGRAM_URL, $id),
            compact('telegram'))
        ->assertUnprocessable();
})->with('wrong_telegram');

it('read telegram', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = User::where('account_type', User::CLIENT_TYPE)->latest()->first()->id;
    $responseId = $this->withToken($token)
        ->json('GET', sprintf('%s/%s', TELEGRAM_URL, $id))
        ->assertOK()
        ->assertJsonStructure(['id', 'telegram'])
        ->json('id');
    $this->assertEquals($responseId, $id);
});
