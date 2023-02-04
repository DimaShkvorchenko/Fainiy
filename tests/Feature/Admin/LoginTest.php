<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

it('login as admin', function () {
    createAdmin();
    $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)
        ->assertOk()
        ->assertJsonStructure(['jwt'])
        ->json('jwt');
});

it('failed login as admin', function ($email, $password) {
    $this->json('POST', CREATE_TOKEN_URL, compact('email', 'password'))
        ->assertUnprocessable();
})->with('login_admin_failed');

it('failed login by telegram', function (
    $id,
    $username,
    $auth_date,
    $hash,
    $first_name,
    $last_name
) {
    $this->json('GET', LOGIN_BY_TELEGRAM_URL,
        compact(
            'id',
            'username',
            'auth_date',
            'hash',
            'first_name',
            'last_name'
        ))
        ->assertUnprocessable();
})->with('wrong_telegram_data');

