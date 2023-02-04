<?php

namespace App\Services\Search;

use Illuminate\Database\Eloquent\Builder;

class ExchangeSearch implements SearchInterface
{
    /**
     * Get search query for filtering of exchanges listing
     *
     * @param Builder $query
     * @param array $fields
     * @return Builder
     */
    public function getQuery(Builder $query, array $fields): Builder
    {
        if (empty($fields['search'])) {
            return $query;
        }

        $query->select('exchanges.*');

        $query->leftJoin('users', function ($join) {
            $join->on('users.id', '=', 'exchanges.client_id')
                ->orOn('users.id', '=', 'exchanges.user_id');
        });

        $query->leftJoin('cashboxes', function ($join) {
            $join->on('cashboxes.id', '=', 'exchanges.cashbox_id');
        });

        $query->leftJoin('currencies', function ($join) {
            $join->on('currencies.id', '=', 'exchanges.from_currency_id')
                ->orOn('currencies.id', '=', 'exchanges.to_currency_id');
        });

        $query->orWhere('exchanges.access_code', 'like', '%' . $fields['search'] . '%');
        $query->orWhere('exchanges.from_amount', 'like', $fields['search'] . '%');
        $query->orWhere('exchanges.to_amount', 'like', $fields['search'] . '%');
        $query->orWhere('exchanges.description', 'like', '%' . $fields['search'] . '%');
        $query->orWhere('exchanges.status', 'like', '%' . $fields['search'] . '%');
        $query->orWhere('exchanges.commission', 'like', '%' . $fields['search'] . '%');
        $query->orWhere('users.first_name', 'like', '%' . $fields['search'] . '%');
        $query->orWhere('users.last_name', 'like', '%' . $fields['search'] . '%');
        $query->orWhere('cashboxes.name', 'like', '%' . $fields['search'] . '%');
        $query->orWhere('currencies.name', 'like', '%' . $fields['search'] . '%');
        $query->orWhere('currencies.iso_code', 'like', '%' . $fields['search'] . '%');

        return $query;
    }
}
