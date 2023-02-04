<?php

use App\Models\{Cashbox, Currency, Income, User, Wallet};
use App\Models\Locations\{Country, Region, City, Branch};
use App\Services\Commission\IncomeCommission;
use Symfony\Component\HttpFoundation\Response;
use Faker\Factory;

it('create income with check of wallets changing', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');

    $faker = Factory::create();

    User::factory(1)->create([
        'account_type' => 3,
        'admin_id' => User::admin()->inRandomOrder()->first()
    ]);
    //Currency::factory(1)->create();
    Region::factory(1)->create();
    City::factory(1)->create();
    Branch::factory(1)->create();
    Cashbox::factory(1)->create();

    do {
        $client_id = User::where('account_type', User::CLIENT_TYPE)->inRandomOrder()->first()->id;
        $currency_id = Currency::inRandomOrder()->first()->id;
        $cashbox_id = Cashbox::inRandomOrder()->first()->id;
    } while (
        Wallet::withTrashed()->where('client_id', $client_id)
            ->where('currency_id', $currency_id)
            ->where('cashbox_id', $cashbox_id)->first()
    );
    $incomeAmount = $faker->randomFloat(2, 100, 100000);

    $payload = [
        'client_id' => $client_id,
        'staff_id' => User::where('account_type', User::STAFF_TYPE)->inRandomOrder()->first()->id,
        'amount' => $incomeAmount,
        'currency_id' => $currency_id,
        'code' => $faker->randomNumber(4, true),
        'access_code' => $faker->randomNumber(4, true),
        'cashbox_id' => $cashbox_id,
    ];

    $clientWallet = Wallet::firstOrCreate(['currency_id' => $currency_id, 'client_id' => $client_id], ['amount' => 0]);
    $clientWallet->amount = 0;
    $clientWallet->save();

    $cashboxWallet = Wallet::firstOrCreate(['currency_id' => $currency_id, 'cashbox_id' => $cashbox_id], ['amount' => 0]);
    $cashboxWallet->amount = 0;
    $cashboxWallet->save();

    $response = $this->withToken($token)
        ->json('POST', INCOME_URL, $payload)
        ->assertCreated()
        ->assertJsonStructure([
            'id',
            'client',
            'staff',
            'amount',
            'commission',
            'profit',
            'currency',
            'code',
            'access_code',
            'cashbox'
        ]);

    $income = Income::find($response->json('id'));
    $this->assertEquals($income->amount, $response->json('amount'));
    $this->assertEquals($income->code, $response->json('code'));
    $this->assertEquals($income->access_code, $response->json('access_code'));

    $clientWallet = Wallet::withTrashed()->where('client_id', $client_id)
        ->where('currency_id', $currency_id)->first();
    $this->assertEquals(
        (new IncomeCommission)->getAmountWithoutCommission($income->only(['amount', 'commission'])),
        $clientWallet->amount);

    $cashboxWallet = Wallet::withTrashed()->where('cashbox_id', $cashbox_id)
        ->where('currency_id', $currency_id)->first();
    $this->assertEquals($incomeAmount, $cashboxWallet->amount);
});

it('update income with check of wallets changing', function (
    $amount,
    $code,
    $access_code,
) {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $beforeUpdateIncome = Income::latest()->first();

    $walletRest = 10;

    $beforeUpdateClientWallet = Wallet::firstOrCreate(
        ['currency_id' => $beforeUpdateIncome->currency_id, 'client_id' => $beforeUpdateIncome->client_id],
        ['amount' => 0]
    );
    $beforeUpdateClientWallet->amount = (new IncomeCommission)->getAmountWithoutCommission($beforeUpdateIncome->only(['amount', 'commission']))
        + $walletRest;
    $beforeUpdateClientWallet->save();

    $beforeUpdateCashboxWallet = Wallet::firstOrCreate(
        ['currency_id' => $beforeUpdateIncome->currency_id, 'cashbox_id' => $beforeUpdateIncome->cashbox_id],
        ['amount' => 0]
    );
    $beforeUpdateCashboxWallet->amount = $beforeUpdateIncome->amount + $walletRest;
    $beforeUpdateCashboxWallet->save();

    $response = $this->withToken($token)
        ->json(
            'PATCH', sprintf('%s/%s', INCOME_URL, $beforeUpdateIncome->id),
            compact(
                'amount',
                'code',
                'access_code',
            )
        )
        ->assertStatus(Response::HTTP_ACCEPTED)
    ;

    $afterUpdateIncome = Income::find($response->json('id'));
    $this->assertEquals($afterUpdateIncome->amount, $response->json('amount'));
    $this->assertEquals($afterUpdateIncome->code, $response->json('code'));
    $this->assertEquals($afterUpdateIncome->access_code, $response->json('access_code'));

    $afterUpdateClientWallet = Wallet::withTrashed()->where('client_id', $beforeUpdateIncome->client_id)
        ->where('currency_id', $beforeUpdateIncome->currency_id)->first();
    $this->assertEquals(
        ($walletRest + (new IncomeCommission)->getAmountWithoutCommission($afterUpdateIncome->only(['amount', 'commission']))),
        $afterUpdateClientWallet->amount
    );

    $afterUpdateCashboxWallet = Wallet::withTrashed()->where('cashbox_id', $beforeUpdateIncome->cashbox_id)
        ->where('currency_id', $beforeUpdateIncome->currency_id)->first();
    $this->assertEquals(
        ($walletRest + $afterUpdateIncome->amount),
        $afterUpdateCashboxWallet->amount
    );
})->with('income');

it('update income with wrong amount/code/access_code', function (
    $amount,
    $code,
    $access_code,
) {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Income::latest()->first()->id;

    $this->withToken($token)
        ->json(
            'PATCH', sprintf('%s/%s', INCOME_URL, $id),
            compact(
                'amount',
                'code',
                'access_code',
            )
        )
        ->assertUnprocessable();
})->with('wrong_income');

it('read income list', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Income::latest()->first()->id;
    $responseId = $this->withToken($token)
        ->json('GET', INCOME_URL)
        ->assertOK()
        ->assertJsonStructure(['items' => [[
            'id',
            'client',
            'staff',
            'amount',
            'commission',
            'profit',
            'currency',
            'code',
            'access_code',
            'cashbox'
        ]]])
        ->json('items.0.id');
    $this->assertEquals($responseId, $id);
});

it('read single income', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $id = Income::latest()->first()->id;
    $responseId = $this->withToken($token)
        ->json('GET', sprintf('%s/%s', INCOME_URL, $id))
        ->assertOK()
        ->assertJsonStructure([
            'id',
            'client',
            'staff',
            'amount',
            'commission',
            'profit',
            'currency',
            'code',
            'access_code',
            'cashbox'
        ])
        ->json('id');
    $this->assertEquals($responseId, $id);
});

it('drop income with check of wallets changing', function () {
    createAdmin();
    $token = $this->json('POST', CREATE_TOKEN_URL, ADMIN_CREDENTIALS)->json('jwt');
    $income = Income::latest()->first();
    $walletRest = 10;
    $beforeDropClientWallet = Wallet::firstOrCreate(
        ['currency_id' => $income->currency_id, 'client_id' => $income->client_id],
        ['amount' => 0]
    );
    $beforeDropClientWallet->amount = (new IncomeCommission)->getAmountWithoutCommission($income->only(['amount', 'commission']))
        + $walletRest;
    $beforeDropClientWallet->save();

    $beforeDropCashboxWallet = Wallet::firstOrCreate(
        ['currency_id' => $income->currency_id, 'cashbox_id' => $income->cashbox_id],
        ['amount' => 0]
    );
    $beforeDropCashboxWallet->amount = $income->amount + $walletRest;
    $beforeDropCashboxWallet->save();

    $this->withToken($token)
        ->json('DELETE', sprintf('%s/%s', INCOME_URL, $income->id));
    $this->assertEquals(Income::find($income->id), null);

    $afterDropClientWallet = Wallet::withTrashed()->where('client_id', $income->client_id)
        ->where('currency_id', $income->currency_id)->first();
    $this->assertEquals($walletRest,
        (new IncomeCommission)->getAmountWithoutCommission($afterDropClientWallet->only(['amount', 'commission'])));

    $afterDropCashboxWallet = Wallet::withTrashed()->where('cashbox_id', $income->cashbox_id)
        ->where('currency_id', $income->currency_id)->first();
    $this->assertEquals($walletRest, $afterDropCashboxWallet->amount);
});
