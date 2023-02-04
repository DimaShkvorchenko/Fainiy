<?php

namespace App\Services\Wallet;

use App\Models\Wallet;
use Illuminate\Support\{Arr, Str};
use Symfony\Component\HttpFoundation\Response;

abstract class WalletService
{
    /**
     * The "not enough money" error message.
     *
     * @var string
     */
    protected string $notEnoughMoneyMessage = "The amount (?) more than wallet amount (?) so not enough money to rollback wallet";

    /**
     * Get or create wallet by search fields
     *
     * @param  array  $walletSearchFields
     * @return Wallet|bool
     */
    public function getOrCreate(array $walletSearchFields): Wallet|bool
    {
        if (!empty($walletSearchFields['cashbox_id'])) {
            $walletSearchFields = Arr::only($walletSearchFields, ['currency_id', 'cashbox_id']);
        } elseif (!empty($walletSearchFields['client_id'])) {
            $walletSearchFields = Arr::only($walletSearchFields, ['currency_id', 'client_id']);
        } else {
            return false;
        }
        return Wallet::firstOrCreate($walletSearchFields, ['amount' => 0]);
    }

    /**
     * Check and get wallet if enough money in wallet
     *
     * @param  array  $fields
     * @return Response|Wallet
     */
    public function checkAndGetIfEnoughMoney(array $fields): Response|Wallet
    {
        $wallet = $this->getOrCreate($fields);
        if ($wallet && !empty($fields['amount'])) {
            if ($fields['amount'] > $wallet->amount) {
                $errorMessage = (!empty($fields['errorMessage']))? $fields['errorMessage'] : $this->notEnoughMoneyMessage;
                $errorMessage = Str::replaceArray('?', [$fields['amount'], $wallet->amount], $errorMessage);
                abort(response(['message' => $errorMessage], Response::HTTP_UNPROCESSABLE_ENTITY));
            }
        }
        return $wallet;
    }

    /**
     * Changing wallets after store
     *
     * @param  array  $fields
     * @return mixed
     */
    abstract public function afterStore(array $fields): mixed;

    /**
     * Changing wallets after update
     *
     * @param  array  $fields
     * @return mixed
     */
    abstract public function afterUpdate(array $fields): mixed;

    /**
     * Changing wallets after destroy
     *
     * @param  array  $fields
     * @return mixed
     */
    abstract public function afterDestroy(array $fields): mixed;
}
