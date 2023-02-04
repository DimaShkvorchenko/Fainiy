<?php

use App\Models\Setting;
use Symfony\Component\HttpFoundation\Response;
use Faker\Factory;

it('create setting', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');

    $faker = Factory::create();

    do {
        $code = $faker->unique()->word();
    } while (!empty(Setting::withTrashed()->where('code', $code)->first()->id));

    $payload = [
        'code' => $code,
        'value' => $faker->word(),
    ];

    $response = $this->withToken($token)
        ->json('POST', SETTING_URL, $payload)
        ->assertCreated()
        ->assertJsonStructure(['id', 'code', 'value']);

    $setting = Setting::find($response->json('id'));
    $this->assertEquals($setting->code, $response->json('code'));
    $this->assertEquals($setting->value, $response->json('value'));
});

it('update setting', function ($value) {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Setting::where('code', '<>', 'basic_currency')->latest()->first()->id;
    $this->withToken($token)
        ->json('PATCH', sprintf('%s/%s', SETTING_URL, $id), array_filter(compact('value')))
        ->assertStatus(Response::HTTP_ACCEPTED);

    if ($value) $this->assertEquals(Setting::find($id)->value, $value);
})->with('settings');

it('update setting with wrong value', function ($value) {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Setting::where('code', '<>', 'basic_currency')->latest()->first()->id;
    $this->withToken($token)
        ->json('PATCH', sprintf('%s/%s', SETTING_URL, $id), array_filter(compact('value')))
        ->assertUnprocessable();
})->with('wrong_settings');

it('read setting list', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Setting::latest()->first()->id;
    $responseId = $this->withToken($token)
        ->json('GET', SETTING_URL)
        ->assertOK()
        ->assertJsonStructure(['items' => [['id', 'code', 'value']]])
        ->json('items.0.id');
    $this->assertEquals($responseId, $id);
});

it('read single setting', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Setting::latest()->first()->id;
    $responseId = $this->withToken($token)
        ->json('GET', sprintf('%s/%s', SETTING_URL, $id))
        ->assertOK()
        ->assertJsonStructure(['id', 'code', 'value'])
        ->json('id');
    $this->assertEquals($responseId, $id);
});

it('drop setting', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Setting::where('code', '<>', 'basic_currency')->latest()->first()->id;
    $this->withToken($token)
        ->json('DELETE', sprintf('%s/%s', SETTING_URL, $id))
        ->assertNoContent();
    $this->assertEquals(Setting::find($id), null);
});
