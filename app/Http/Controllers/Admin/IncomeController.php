<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\{
    SearchRequest,
    StoreIncomeRequest,
    UpdateIncomeRequest
};
use App\Services\Search\IncomeSearch;
use App\Services\Wallet\IncomeWalletService;
use App\Services\Commission\IncomeCommission;
use App\Http\Resources\{IncomeCollection, IncomeResource};
use App\Models\Income;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class IncomeController extends BaseCollectionController
{
    /**
     * Display a listing of the resource.
     *
     * @param  SearchRequest  $request
     * @param  IncomeSearch  $search
     * @return IncomeCollection
     */
    public function index(SearchRequest $request, IncomeSearch $search): IncomeCollection
    {
        $income = $search->getQuery(Income::query(), $request->validated());
        return new IncomeCollection($income->paginate($request->per_page ?? 10));
    }

    /**
     * Store a newly created income in storage
     * and log in actions table
     * and change the client's and cashbox's wallets amount
     *
     * @param  StoreIncomeRequest  $request
     * @param  IncomeWalletService  $walletService
     * @param  IncomeCommission $commission
     * @return IncomeResource
     */
    public function store(
        StoreIncomeRequest $request,
        IncomeWalletService $walletService,
        IncomeCommission $commission
    ): IncomeResource
    {
        $fields = $request->validated();
        $fields['commission'] = $commission->getOrSetClientIncomeCommission($fields['client_id']);
        $fields['profit'] = round(($fields['commission'] * $fields['amount'] / 100), 2);

        $income = Income::create($fields);
        $this->actionService->log($income, 'store');
        $walletService->afterStore($income->only(['currency_id', 'client_id', 'cashbox_id', 'amount', 'commission']));
        return new IncomeResource($income);
    }

    /**
     * Display the specified resource.
     *
     * @param  Income  $income
     * @return IncomeResource
     */
    public function show(Income $income): IncomeResource
    {
        return new IncomeResource($income);
    }

    /**
     * Update the specified income in storage
     * and log in actions table
     * and change the client's and cashbox's wallets amount
     *
     * @param  UpdateIncomeRequest  $request
     * @param  Income $income
     * @param  IncomeWalletService $walletService
     * @param  IncomeCommission $commission
     * @return JsonResponse|Response
     */
    public function update(
        UpdateIncomeRequest $request,
        Income $income,
        IncomeWalletService $walletService,
        IncomeCommission $commission
    ): JsonResponse|Response
    {
        $walletService->checkAndGetIfEnoughMoney(
            $income->only(['currency_id', 'client_id']) +
            ['amount' => $commission->getAmountWithoutCommission($income->only(['amount', 'commission'])),
             'errorMessage' => "The old amount (?) more than client's wallet amount (?) so not enough money to rollback wallet"]
        );
        $walletService->checkAndGetIfEnoughMoney(
            $income->only(['currency_id', 'cashbox_id', 'amount']) +
            ['errorMessage' => "The old amount (?) more than cashbox's wallet amount (?) so not enough money to rollback wallet"]
        );
        $oldAmount = $income->amount;
        $fields = $request->safe()->only(['amount', 'code', 'access_code']);
        $fields['profit'] = round(($income?->commission * $fields['amount'] / 100), 2);

        $income->update($fields);
        $this->actionService->log($income, 'update');

        $walletService->afterUpdate(
            $income->only(['currency_id', 'client_id', 'cashbox_id', 'amount', 'commission']) +
            ['oldAmount' => $oldAmount]
        );
        return (new IncomeResource($income))->response()->setStatusCode(Response::HTTP_ACCEPTED);
    }

    /**
     * Remove the specified income from storage
     * with check if client's and cashbox's wallets amount is enough
     * and log in actions table
     * and change the client's and cashbox's wallets amount
     *
     * @param  Income  $income
     * @param  IncomeWalletService  $walletService
     * @param  IncomeCommission $commission
     * @return JsonResponse|Response
     */
    public function destroy(
        Income $income,
        IncomeWalletService $walletService,
        IncomeCommission $commission
    ): JsonResponse|Response
    {
        $walletService->checkAndGetIfEnoughMoney(
            $income->only(['currency_id', 'client_id']) +
            ['amount' => $commission->getAmountWithoutCommission($income->only(['amount', 'commission'])),
             'errorMessage' => "The income amount (?) more than client's wallet amount (?) so not enough money to rollback wallet"]
        );
        $walletService->checkAndGetIfEnoughMoney(
            $income->only(['currency_id', 'cashbox_id', 'amount']) +
            ['errorMessage' => "The income amount (?) more than cashbox's wallet amount (?) so not enough money to rollback wallet"]
        );
        if ($result = parent::delete($income)) {
            $this->actionService->log($income, 'destroy');
            $walletService->afterDestroy($income->only(['currency_id', 'client_id', 'cashbox_id', 'amount', 'commission']));
        }
        return $result;
    }
}
