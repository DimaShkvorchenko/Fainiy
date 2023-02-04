<?php

namespace App\Services\Wallet;

use Illuminate\Support\Arr;
use App\Jobs\WalletInOutcome;

class ExchangeWalletService extends WalletService
{
    /**
     * The "not enough money" error message.
     *
     * @var string
     */
    protected string $notEnoughMoneyMessage = "The exchange amount (?) more than wallet amount (?) so not enough money to rollback wallet";

    /**
     * Changing the client's and cashbox's wallets amount after exchange store
     *
     * @param  array  $fields
     * @return bool
     */
    public function afterStore(array $fields): bool
    {
        $fromCurrencyClientWallet = parent::getOrCreate([
            'currency_id' => $fields['from_currency_id'],
            'client_id' => $fields['client_id'],
        ]);
        WalletInOutcome::dispatchSync($fromCurrencyClientWallet, $fields['from_amount'], null, 'minus');
        $fromCurrencyCashboxWallet = parent::getOrCreate([
            'currency_id' => $fields['from_currency_id'],
            'cashbox_id' => $fields['cashbox_id'],
        ]);
        WalletInOutcome::dispatchSync($fromCurrencyCashboxWallet, $fields['from_amount'], null, 'minus');

        $toCurrencyClientWallet = parent::getOrCreate([
            'currency_id' => $fields['to_currency_id'],
            'client_id' => $fields['client_id'],
        ]);
        WalletInOutcome::dispatchSync($toCurrencyClientWallet, $fields['to_amount'], null);
        return true;
    }

    /**
     * Changing the client's and cashbox's wallets amount after exchange update
     *
     * @param  array  $fields
     * @return bool
     */
    public function afterUpdate(array $fields): bool
    {
        $afterUpdateToCurrencyWallet = parent::getOrCreate([
            'currency_id' => $fields['to_currency_id'],
            'client_id' => $fields['client_id'],
        ]);
        WalletInOutcome::dispatchSync($fields['beforeUpdateToCurrencyWallet'], $fields['beforeUpdateToAmount'], null, 'minus');
        WalletInOutcome::dispatchSync($afterUpdateToCurrencyWallet, $fields['to_amount'], null);
        return true;
    }

    /**
     * Changing the client's and cashbox's wallets amount after exchange destroy
     *
     * @param  array  $fields
     * @return bool
     */
    public function afterDestroy(array $fields): bool
    {
        $fromCurrencyClientWallet = parent::getOrCreate([
            'currency_id' => $fields['from_currency_id'],
            'client_id' => $fields['client_id'],
        ]);
        WalletInOutcome::dispatchSync($fromCurrencyClientWallet, $fields['from_amount'], null);
        $fromCurrencyCashboxWallet = parent::getOrCreate([
            'currency_id' => $fields['from_currency_id'],
            'cashbox_id' => $fields['cashbox_id'],
        ]);
        WalletInOutcome::dispatchSync($fromCurrencyCashboxWallet, $fields['from_amount'], null);

        $toCurrencyClientWallet = parent::getOrCreate([
            'currency_id' => $fields['to_currency_id'],
            'client_id' => $fields['client_id'],
        ]);
        WalletInOutcome::dispatchSync($toCurrencyClientWallet, $fields['to_amount'], null, 'minus');
        return true;
    }
}
