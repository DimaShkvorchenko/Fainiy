<?php

use App\Models\Action;
use Symfony\Component\HttpFoundation\Response;
use Faker\Factory;

it('read action list', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Action::latest()->first()->id;
    $responseId = $this->withToken($token)
        ->json('GET', ACTION_URL)
        ->assertOK()
        ->assertJsonStructure(['items' => [[
            'id',
            'created_at',
            'type',
            'amount',
            'parent_id',
            'staff',
            'client',
            'currency',
            'cashbox',
            'other_data'
        ]]])
        ->json('items.0.id');
    $this->assertEquals($responseId, $id);
});

it('read action list with date filter', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $date_from = Action::oldest()->first()->created_at->format('Y-m-d');
    $date_to = Action::latest()->first()->created_at->format('Y-m-d');
    $params = '?date_from=' . $date_from . '&date_to=' . $date_to;
    $responseId = $this->withToken($token)
        ->json('GET', ACTION_URL . $params)
        ->assertOK()
        ->assertJsonStructure(['items' => [[
            'id',
            'created_at',
            'type',
            'amount',
            'parent_id',
            'staff',
            'client',
            'currency',
            'cashbox',
            'other_data'
        ]]])
        ->json('items.0.id');
    $this->assertEquals(Action::find($responseId)->created_at->format('Y-m-d'), $date_to);
});

it('read single action', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Action::latest()->first()->id;
    $responseId = $this->withToken($token)
        ->json('GET', sprintf('%s/%s', ACTION_URL, $id))
        ->assertOK()
        ->assertJsonStructure([
            'id',
            'created_at',
            'type',
            'amount',
            'parent_id',
            'staff',
            'client',
            'currency',
            'cashbox',
            'other_data'
        ])
        ->json('id');
    $this->assertEquals($responseId, $id);
});

it('read single action with wrong id', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = 123;
    $this->withToken($token)
        ->json('GET', sprintf('%s/%s', ACTION_URL, $id))
        ->assertStatus(Response::HTTP_NOT_FOUND);
});
