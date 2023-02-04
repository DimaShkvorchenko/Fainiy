<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\{
    SearchRequest,
    StoreExchangeRequest,
    UpdateExchangeRequest
};
use App\Services\Search\ExchangeSearch;
use App\Services\Wallet\ExchangeWalletService;
use App\Http\Resources\{ExchangeCollection, ExchangeResource};
use App\Models\Exchange;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ExchangeController extends BaseCollectionController
{
    /**
     * Display a listing of the resource.
     *
     * @param  SearchRequest  $request
     * @param  ExchangeSearch  $search
     * @return ExchangeCollection
     */
    public function index(SearchRequest $request, ExchangeSearch $search): ExchangeCollection
    {
        $exchanges = $search->getQuery(Exchange::query(), $request->validated());
        return new ExchangeCollection($exchanges->paginate($request->per_page ?? 10));
    }

    /**
     * Store a newly created exchange in storage and change the client's and cashbox's wallets amount
     *
     * @param  StoreExchangeRequest  $request
     * @param  ExchangeWalletService  $walletService
     * @return ExchangeResource
     */
    public function store(StoreExchangeRequest $request, ExchangeWalletService $walletService): ExchangeResource
    {
        $fields = $request->validated();
        $fields['status'] = 'created';
        $fields['exchange_type'] = 'cross';
        if ($basicCurrency = $this->currencyService->getOrSetBasicCurrency()) {
            if ($fields['from_currency_id'] == $basicCurrency->id) {
                $fields['exchange_type'] = 'buy';
            } elseif ($fields['to_currency_id'] == $basicCurrency->id) {
                $fields['exchange_type'] = 'sell';
            }
        }
        if (empty($fields['spread'])) {
            $fields['spread'] = $this->rateService->getSpread([
                'from_currency_id' => $fields['from_currency_id'],
                'to_currency_id' => $fields['to_currency_id'],
                'cashbox_id' => $fields['cashbox_id']
            ]);
        }

        $exchange = Exchange::create($fields);
        $this->actionService->log($exchange, 'store');

        $walletService->afterStore($exchange->only([
            'client_id', 'cashbox_id',
            'from_currency_id', 'from_amount',
            'to_currency_id', 'to_amount'
        ]));

        return new ExchangeResource($exchange);
    }

    /**
     * Display the specified resource.
     *
     * @param  Exchange  $exchange
     * @return ExchangeResource
     */
    public function show(Exchange $exchange): ExchangeResource
    {
        return new ExchangeResource($exchange);
    }

    /**
     * Update the specified exchange in storage and change the wallet amount
     *
     * @param  UpdateExchangeRequest  $request
     * @param  Exchange  $exchange
     * @param  ExchangeWalletService  $walletService
     * @return JsonResponse
     */
    public function update(UpdateExchangeRequest $request, Exchange $exchange, ExchangeWalletService $walletService): JsonResponse|Response
    {
        $beforeUpdateToCurrencyWallet = $walletService->checkAndGetIfEnoughMoney([
            'currency_id' => $exchange->to_currency_id,
            'client_id' => $exchange->client_id,
            'amount' => $exchange->to_amount,
            'errorMessage' => "The old to_amount (?) more than old to_currency client's wallet amount (?)"
        ]);
        $beforeUpdateToAmount = $exchange->to_amount;

        if (empty($request->spread)) {
            $request->spread = $this->rateService->getSpread([
                'from_currency_id' => $exchange->from_currency_id,
                'to_currency_id' => $request->to_currency_id,
                'cashbox_id' => $exchange->cashbox_id
            ]);
        }

        $exchange->update($request->safe()->only([
            'access_code',
            'to_amount',
            'to_currency_id',
            'exchange_rate',
            'description',
            'status',
            'time_of_issue',
            'spread'
        ]));
        $this->actionService->log($exchange, 'update');

        $walletService->afterUpdate(
            $exchange->only(['to_currency_id', 'client_id', 'to_amount']) +
            ['beforeUpdateToCurrencyWallet' => $beforeUpdateToCurrencyWallet, 'beforeUpdateToAmount'=> $beforeUpdateToAmount]
        );

        return (new ExchangeResource($exchange))->response()->setStatusCode(Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified exchange from storage with check if wallet amount is enough
     *
     * @param  Exchange  $exchange
     * @param  ExchangeWalletService  $walletService
     * @return JsonResponse|Response
     */
    public function destroy(Exchange $exchange, ExchangeWalletService $walletService): JsonResponse|Response
    {
        $walletService->checkAndGetIfEnoughMoney([
            'currency_id' => $exchange->to_currency_id,
            'client_id' => $exchange->client_id,
            'amount' => $exchange->to_amount,
            'errorMessage' => "The to_amount (?) more than to_currency client's wallet amount (?)"
        ]);
        if ($result = parent::delete($exchange)) {
            $this->actionService->log($exchange, 'destroy');
            $walletService->afterDestroy($exchange->only([
                'client_id', 'cashbox_id',
                'from_currency_id', 'from_amount',
                'to_currency_id', 'to_amount'
            ]));
        }
        return $result;
    }
}
