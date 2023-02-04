<?php

namespace App\Services;

use App\Models\{Income, Exchange, Transfer, Wallet, Action};

class ActionService
{
    /**
     * Log incomes, exchanges, transfers, wallets in actions table
     *
     * @param  Income|Exchange|Transfer|Wallet $resource
     * @param string $event
     * @return void
     */
    public function log(Income|Exchange|Transfer|Wallet $resource, string $event): void
    {
        $payload = [
            'event' => $event,
            'parent_id' => $resource?->id,
            'client_id' => $resource?->client_id
        ];

        if ($resource instanceof Income) {
            $payload['type'] = 'income';
            $payload['amount'] = $resource?->amount;
            $payload['staff_id'] = $resource?->staff_id;
            $payload['currency_id'] = $resource?->currency_id;
            $payload['cashbox_id'] = $resource?->cashbox_id;
            $payload['other_data'] = json_encode($resource->only([
                'commission', 'profit', 'code', 'access_code'
            ]));
        } elseif ($resource instanceof Exchange) {
            $payload['type'] = 'exchange';
            $payload['amount'] = $resource?->from_amount;
            $payload['staff_id'] = $resource?->user_id;
            $payload['currency_id'] = $resource?->from_currency_id;
            $payload['cashbox_id'] = $resource?->cashbox_id;
            $payload['other_data'] = json_encode($resource->only([
                'to_amount', 'to_currency_id', 'exchange_rate', 'exchange_type',
                'status', 'spread', 'commission', 'time_of_issue'
            ]));
        } elseif ($resource instanceof Transfer) {
            $payload['type'] = 'transfer';
            $payload['amount'] = $resource?->amount;
            $payload['staff_id'] = $resource?->user_id;
            $payload['currency_id'] = $resource?->currency_id;
            $payload['cashbox_id'] = $resource?->from_cashbox_id;
            $payload['other_data'] = json_encode($resource->only([
                'to_cashbox_id', 'status', 'time_of_issue'
            ]));
        } elseif ($resource instanceof Wallet) {
            $payload['type'] = 'wallet';
            $payload['amount'] = $resource?->amount;
            $payload['currency_id'] = $resource?->currency_id;
            $payload['cashbox_id'] = $resource?->cashbox_id;
        }

        Action::create($payload);
    }
}
