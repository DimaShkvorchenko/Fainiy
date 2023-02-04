<?php

namespace App\Services\Search;

use Illuminate\Database\Eloquent\Builder;

class WalletSearch implements SearchInterface
{
    /**
     * Get search query for filtering of wallets listing
     *
     * @param Builder $query
     * @param array $fields
     * @return Builder
     */
    public function getQuery(Builder $query, array $fields): Builder
    {
        if (empty($fields['search'])
            && empty($fields['clients'])
            && empty($fields['currencies'])
            && empty($fields['cashboxes'])
        ) {
            return $query;
        }

        $query->select('wallet.*');

        if (!empty($fields['search'])) {
            $query->leftJoin('users', function ($join) {
                $join->on('users.id', '=', 'wallet.client_id');
            });

            $query->leftJoin('currencies', function ($join) {
                $join->on('currencies.id', '=', 'wallet.currency_id');
            });

            $query->leftJoin('cashboxes', function ($join) {
                $join->on('cashboxes.id', '=', 'wallet.cashbox_id');
            });

            $query->orWhere('wallet.amount', 'like', $fields['search'].'%');
            $query->orWhere('users.first_name', 'like', '%'.$fields['search'].'%');
            $query->orWhere('users.last_name', 'like', '%'.$fields['search'].'%');
            $query->orWhere('currencies.name', 'like', '%'.$fields['search'].'%');
            $query->orWhere('currencies.iso_code', 'like', '%'.$fields['search'].'%');
            $query->orWhere('cashboxes.name', 'like', '%'.$fields['search'].'%');
        }
        if (!empty($fields['clients'])) {
            $query->orWhereIn('wallet.client_id', $fields['clients']);
        }
        if (!empty($fields['currencies'])) {
            $query->orWhereIn('wallet.currency_id', $fields['currencies']);
        }
        if (!empty($fields['cashboxes'])) {
            $query->orWhereIn('wallet.cashbox_id', $fields['cashboxes']);
        }
        return $query;
    }
}
