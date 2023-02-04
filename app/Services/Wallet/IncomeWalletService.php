<?php

namespace App\Services\Wallet;

use Illuminate\Support\Arr;
use App\Jobs\WalletInOutcome;

class IncomeWalletService extends WalletService
{
    /**
     * The "not enough money" error message.
     *
     * @var string
     */
    protected string $notEnoughMoneyMessage = "The income amount (?) more than wallet amount (?) so not enough money to rollback wallet";

    /**
     * Changing the client's and cashbox's wallets amount after income store
     *
     * @param  array  $fields
     * @return bool
     */
    public function afterStore(array $fields): bool
    {
        $clientWallet = parent::getOrCreate(Arr::only($fields, ['currency_id', 'client_id']));
        $amountWithOutCommission = (!empty($fields['commission']))? $fields['amount'] - round(($fields['commission'] * $fields['amount'] / 100), 2) : $fields['amount'];
        WalletInOutcome::dispatchSync($clientWallet, $amountWithOutCommission, null);
        $cashboxWallet = parent::getOrCreate(Arr::only($fields, ['currency_id', 'cashbox_id']));
        WalletInOutcome::dispatchSync($cashboxWallet, $fields['amount'], null);
        return true;
    }

    /**
     * Changing the client's and cashbox's wallets amount after income update
     *
     * @param  array  $fields
     * @return bool
     */
    public function afterUpdate(array $fields): bool
    {
        $clientWallet = parent::getOrCreate(Arr::only($fields, ['currency_id', 'client_id']));
        $oldAmountWithOutCommission = (!empty($fields['commission']))? $fields['oldAmount'] - round(($fields['commission'] * $fields['oldAmount'] / 100), 2) : $fields['oldAmount'];
        $amountWithOutCommission = (!empty($fields['commission']))? $fields['amount'] - round(($fields['commission'] * $fields['amount'] / 100), 2) : $fields['amount'];
        WalletInOutcome::dispatchSync($clientWallet, $amountWithOutCommission, $oldAmountWithOutCommission, 'minus_plus');
        $cashboxWallet = parent::getOrCreate(Arr::only($fields, ['currency_id', 'cashbox_id']));
        WalletInOutcome::dispatchSync($cashboxWallet, $fields['amount'], $fields['oldAmount'], 'minus_plus');
        return true;
    }

    /**
     * Changing the client's and cashbox's wallets amount after income destroy
     *
     * @param  array  $fields
     * @return bool
     */
    public function afterDestroy(array $fields): bool
    {
        $clientWallet = parent::getOrCreate(Arr::only($fields, ['currency_id', 'client_id']));
        $amountWithOutCommission = (!empty($fields['commission']))? $fields['amount'] - round(($fields['commission'] * $fields['amount'] / 100), 2) : $fields['amount'];
        WalletInOutcome::dispatchSync($clientWallet, $amountWithOutCommission, null, 'minus');
        $cashboxWallet = parent::getOrCreate(Arr::only($fields, ['currency_id', 'cashbox_id']));
        WalletInOutcome::dispatchSync($cashboxWallet, $fields['amount'], null, 'minus');
        return true;
    }
}
