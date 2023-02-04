<?php

namespace App\Services\Search;

use Illuminate\Database\Eloquent\Builder;

class TransferSearch implements SearchInterface
{
    /**
     * Get search query for filtering of transfers listing
     *
     * @param Builder $query
     * @param array $fields
     * @return Builder
     */
    public function getQuery(Builder $query, array $fields): Builder
    {
        if (
            empty($fields['search'])
            && empty($fields['currencies'])
            && empty($fields['statuses'])
        ) {
            return $query;
        }

        $query->select('transfers.*');

        if (!empty($fields['search'])) {
            $query->leftJoin('users', function ($join) {
                $join->on('users.id', '=', 'transfers.client_id')
                    ->orOn('users.id', '=', 'transfers.user_id');
            });

            $query->leftJoin('cashboxes', function ($join) {
                $join->on('cashboxes.id', '=', 'transfers.from_cashbox_id')
                    ->orOn('cashboxes.id', '=', 'transfers.to_cashbox_id');
            });

            $query->leftJoin('currencies', function ($join) {
                $join->on('currencies.id', '=', 'transfers.currency_id');
            });

            $query->orWhere('transfers.access_code', 'like', '%'.$fields['search'].'%');
            $query->orWhere('transfers.amount', 'like', $fields['search'].'%');
            $query->orWhere('transfers.description', 'like', '%'.$fields['search'].'%');
            $query->orWhere('transfers.status', 'like', '%'.$fields['search'].'%');
            $query->orWhere('transfers.commission', 'like', '%'.$fields['search'].'%');
            $query->orWhere('users.first_name', 'like', '%'.$fields['search'].'%');
            $query->orWhere('users.last_name', 'like', '%'.$fields['search'].'%');
            $query->orWhere('cashboxes.name', 'like', '%'.$fields['search'].'%');
            $query->orWhere('currencies.name', 'like', '%'.$fields['search'].'%');
            $query->orWhere('currencies.iso_code', 'like', '%'.$fields['search'].'%');
        }
        if (!empty($fields['currencies'])) {
            $query->orWhereIn('transfers.currency_id', $fields['currencies']);
        }
        if (!empty($fields['statuses'])) {
            $query->orWhereIn('transfers.status', $fields['statuses']);
        }
        return $query;
    }
}
