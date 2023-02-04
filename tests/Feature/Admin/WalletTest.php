<?php

use App\Models\Wallet;
use Symfony\Component\HttpFoundation\Response;
use Faker\Factory;

it('read wallet list', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Wallet::latest()->first()->id;
    $responseId = $this->withToken($token)
        ->json('GET', WALLET_URL)
        ->assertOK()
        ->assertJsonStructure(['items' => [[
            'id',
            'client',
            'currency',
            'amount',
            'cashbox'
        ]]])
        ->json('items.0.id');
    //$this->assertEquals($responseId, $id);
});

it('read single wallet', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Wallet::latest()->first()->id;
    $responseId = $this->withToken($token)
        ->json('GET', sprintf('%s/%s', WALLET_URL, $id))
        ->assertOK()
        ->assertJsonStructure([
            'id',
            'client',
            'currency',
            'amount',
            'cashbox'
        ])
        ->json('id');
    $this->assertEquals($responseId, $id);
});
