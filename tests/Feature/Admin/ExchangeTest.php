<?php

use App\Models\{Cashbox, Currency, Exchange, User, Wallet};
use App\Models\Locations\{Country, Region, City, Branch};
use Symfony\Component\HttpFoundation\Response;
use Faker\Factory;

it('create exchange with check of wallets changing', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');

    $faker = Factory::create();

    User::factory(1)->create([
        'account_type' => 3,
        'admin_id' => User::admin()->inRandomOrder()->first()
    ]);
    //Country::factory(1)->create();
    Region::factory(1)->create();
    City::factory(1)->create();
    Branch::factory(1)->create();
    Cashbox::factory(1)->create();

    $from_amount = $faker->randomFloat(2, 100, 100000);
    $to_amount = $faker->randomFloat(2, 100, 100000);
    $from_currency_id = Currency::latest()->first()->id;
    do {
        $to_currency_id = Currency::inRandomOrder()->first()->id;
    } while ($from_currency_id == $to_currency_id);

    $client_id = User::where('account_type', User::CLIENT_TYPE)
        ->where('modules', 'like', '%exchange%')
        ->inRandomOrder()->first()->id;
    $cashbox_id = Cashbox::inRandomOrder()->first()->id;

    $fromCurrencyWalletRest = 10;

    $fromCurrencyClientWallet = Wallet::firstOrCreate(
        ['currency_id' => $from_currency_id, 'client_id' => $client_id],
        ['amount' => ($from_amount + $fromCurrencyWalletRest)]
    );
    $fromCurrencyClientWallet->amount = $from_amount + $fromCurrencyWalletRest;
    $fromCurrencyClientWallet->save();

    $fromCurrencyCashboxWallet = Wallet::firstOrCreate(
        ['currency_id' => $from_currency_id, 'cashbox_id' => $cashbox_id],
        ['amount' => ($from_amount + $fromCurrencyWalletRest)]
    );
    $fromCurrencyCashboxWallet->amount = $from_amount + $fromCurrencyWalletRest;
    $fromCurrencyCashboxWallet->save();

    $toCurrencyClientWallet = Wallet::firstOrCreate(
        ['currency_id' => $to_currency_id, 'client_id' => $client_id],
        ['amount' => 0]
    );
    $toCurrencyClientWallet->amount = 0;
    $toCurrencyClientWallet->save();

    $payload = [
        'access_code' => $faker->randomNumber(4, true),
        'from_amount' => $from_amount,
        'to_amount' => $to_amount,
        'commission' => json_encode([
            'exchange_commission' => $faker->randomFloat(2, 1, 100)
        ]),
        'user_id' => User::where('account_type', User::STAFF_TYPE)->inRandomOrder()->first()->id,
        'client_id' => $client_id,
        'cashbox_id' => $cashbox_id,
        'from_currency_id' => $from_currency_id,
        'to_currency_id' => $to_currency_id,
        'exchange_rate' => $faker->randomFloat(2, 10, 100),
        'description' => $faker->sentence(),
        'time_of_issue' => $faker->dateTimeBetween('-30 days', '+30 days')->format('Y-m-d H:i:s'),
    ];

    $response = $this->withToken($token)
        ->json('POST', EXCHANGE_URL, $payload)
        ->assertCreated()
        ->assertJsonStructure([
            'id',
            'access_code',
            'from_amount',
            'to_amount',
            'commission',
            'user',
            'client',
            'cashbox',
            'from_currency',
            'to_currency',
            'exchange_rate',
            'exchange_type',
            'description',
            'status',
            'time_of_issue',
            'spread',
        ]);

    $exchange = Exchange::find($response->json('id'));
    $this->assertEquals($exchange->access_code, $response->json('access_code'));
    $this->assertEquals($exchange->from_amount, $response->json('from_amount'));
    $this->assertEquals($exchange->to_amount, $response->json('to_amount'));
    $this->assertEquals($exchange->exchange_rate, $response->json('exchange_rate'));
    $this->assertEquals($exchange->exchange_type, $response->json('exchange_type'));
    $this->assertEquals($exchange->description, $response->json('description'));
    $this->assertEquals($exchange->status, $response->json('status'));
    $this->assertEquals($exchange->time_of_issue, $response->json('time_of_issue'));

    $fromCurrencyClientWallet = Wallet::withTrashed()
        ->where('currency_id', $from_currency_id)
        ->where('client_id', $client_id)->first();
    $this->assertEquals($fromCurrencyWalletRest, $fromCurrencyClientWallet->amount);

    $fromCurrencyCashboxWallet = Wallet::withTrashed()
        ->where('currency_id', $from_currency_id)
        ->where('cashbox_id', $cashbox_id)->first();
    $this->assertEquals($fromCurrencyWalletRest, $fromCurrencyCashboxWallet->amount);

    $toCurrencyClientWallet = Wallet::withTrashed()
        ->where('currency_id', $to_currency_id)
        ->where('client_id', $client_id)->first();
    $this->assertEquals($to_amount, $toCurrencyClientWallet->amount);
});

it('update exchange with check of wallet changing', function (
    $access_code,
    $to_amount,
    $to_currency_id,
    $exchange_rate,
    $description,
    $status,
    $time_of_issue,
    $spread
) {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $beforeUpdateExchange = Exchange::latest()->first();

    while ($beforeUpdateExchange->from_currency_id == $to_currency_id) {
        $to_currency_id = Currency::inRandomOrder()->first()->id;
    }

    $walletRest = 10;
    $beforeUpdateToCurrencyWallet = Wallet::firstOrCreate(
        ['currency_id' => $beforeUpdateExchange->to_currency_id, 'client_id' => $beforeUpdateExchange->client_id],
        ['amount' => 0]
    );
    $beforeUpdateToCurrencyWallet->amount = $beforeUpdateExchange->to_amount + $walletRest;
    $beforeUpdateToCurrencyWallet->save();

    $afterUpdateToCurrencyWallet = Wallet::firstOrCreate(
        ['currency_id' => $to_currency_id, 'client_id' => $beforeUpdateExchange->client_id],
        ['amount' => 0]
    );
    $afterUpdateToCurrencyWallet->amount = $walletRest;
    $afterUpdateToCurrencyWallet->save();

    $response = $this->withToken($token)
        ->json(
            'PATCH', sprintf('%s/%s', EXCHANGE_URL, $beforeUpdateExchange->id),
            compact(
                'access_code',
                'to_amount',
                'to_currency_id',
                'exchange_rate',
                'description',
                'status',
                'time_of_issue',
                'spread'
            )
        )
        ->assertStatus(Response::HTTP_ACCEPTED);

    $afterUpdateExchange = Exchange::find($response->json('id'));
    $this->assertEquals($afterUpdateExchange->access_code, $response->json('access_code'));
    $this->assertEquals($afterUpdateExchange->to_amount, $response->json('to_amount'));
    $this->assertEquals($afterUpdateExchange->description, $response->json('description'));
    $this->assertEquals($afterUpdateExchange->status, $response->json('status'));
    $this->assertEquals($afterUpdateExchange->time_of_issue, $response->json('time_of_issue'));
    $this->assertEquals($afterUpdateExchange->spread, $response->json('spread'));

        $beforeUpdateToCurrencyWallet = Wallet::withTrashed()
            ->where('currency_id', $beforeUpdateExchange->to_currency_id)
            ->where('client_id', $beforeUpdateExchange->client_id)->first();
        $this->assertEquals($walletRest, $beforeUpdateToCurrencyWallet->amount);

        $afterUpdateToCurrencyWallet = Wallet::withTrashed()
            ->where('currency_id', $afterUpdateExchange->to_currency_id)
            ->where('client_id', $afterUpdateExchange->client_id)->first();
        $this->assertEquals(($afterUpdateExchange->to_amount + $walletRest), $afterUpdateToCurrencyWallet->amount);

})->with('exchanges');

it('update exchange with wrong access_code/to_amount/to_currency_id', function (
    $access_code,
    $to_amount,
    $to_currency_id,
    $exchange_rate,
    $description,
    $status,
    $time_of_issue,
    $spread
) {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Exchange::latest()->first()->id;
    $this->withToken($token)
        ->json(
            'PATCH', sprintf('%s/%s', EXCHANGE_URL, $id),
            compact(
                'access_code',
                'to_amount',
                'to_currency_id',
                'exchange_rate',
                'description',
                'status',
                'time_of_issue',
                'spread'
            )
        )
        ->assertUnprocessable();
})->with('wrong_exchanges');

it('read exchange list', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Exchange::latest()->first()->id;
    $responseId = $this->withToken($token)
        ->json('GET', EXCHANGE_URL)
        ->assertOK()
        ->assertJsonStructure(['items' => [[
            'id',
            'access_code',
            'from_amount',
            'to_amount',
            'commission',
            'user',
            'client',
            'cashbox',
            'from_currency',
            'to_currency',
            'exchange_rate',
            'exchange_type',
            'description',
            'status',
            'time_of_issue',
            'spread',
        ]]])
        ->json('items.0.id');
    $this->assertEquals($responseId, $id);
});

it('read single exchange', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Exchange::latest()->first()->id;
    $responseId = $this->withToken($token)
        ->json('GET', sprintf('%s/%s', EXCHANGE_URL, $id))
        ->assertOK()
        ->assertJsonStructure([
            'id',
            'access_code',
            'from_amount',
            'to_amount',
            'commission',
            'user',
            'client',
            'cashbox',
            'from_currency',
            'to_currency',
            'exchange_rate',
            'exchange_type',
            'description',
            'status',
            'time_of_issue',
            'spread',
        ])
        ->json('id');
    $this->assertEquals($responseId, $id);
});

it('drop exchange with check of wallets changing', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $exchange = Exchange::latest()->first();

    $toCurrencyWalletRest = 10;
    $toCurrencyWallet = Wallet::firstOrCreate(
        ['client_id' => $exchange->client_id, 'currency_id' => $exchange->to_currency_id],
        ['amount' => ($exchange->to_amount + $toCurrencyWalletRest)]
    );
    $toCurrencyWallet->amount = $exchange->to_amount + $toCurrencyWalletRest;
    $toCurrencyWallet->save();

    $fromCurrencyClientWallet = Wallet::firstOrCreate(
        ['currency_id' => $exchange->from_currency_id, 'client_id' => $exchange->client_id],
        ['amount' => 0]
    );
    $fromCurrencyClientWallet->amount = 0;
    $fromCurrencyClientWallet->save();

    $fromCurrencyCashboxWallet = Wallet::firstOrCreate(
        ['currency_id' => $exchange->from_currency_id, 'cashbox_id' => $exchange->cashbox_id],
        ['amount' => 0]
    );
    $fromCurrencyCashboxWallet->amount = 0;
    $fromCurrencyCashboxWallet->save();

    $this->withToken($token)
        ->json('DELETE', sprintf('%s/%s', EXCHANGE_URL, $exchange->id));
    $this->assertEquals(Exchange::find($exchange->id), null);

    $fromCurrencyClientWallet = Wallet::withTrashed()
        ->where('currency_id', $exchange->from_currency_id)
        ->where('client_id', $exchange->client_id)->first();
    $this->assertEquals($exchange->from_amount, $fromCurrencyClientWallet->amount);

    $fromCurrencyCashboxWallet = Wallet::withTrashed()
        ->where('currency_id', $exchange->from_currency_id)
        ->where('cashbox_id', $exchange->cashbox_id)->first();
    $this->assertEquals($exchange->from_amount, $fromCurrencyCashboxWallet->amount);

    $toCurrencyWallet = Wallet::withTrashed()
        ->where('currency_id', $exchange->to_currency_id)
        ->where('client_id', $exchange->client_id)->first();
    $this->assertEquals($toCurrencyWalletRest, $toCurrencyWallet->amount);
});
