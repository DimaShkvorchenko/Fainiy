<?php

namespace App\Services\Search;

use Illuminate\Database\Eloquent\Builder;

class IncomeSearch implements SearchInterface
{
    /**
     * Get search query for filtering of incomes listing
     *
     * @param Builder $query
     * @param array $fields
     * @return Builder
     */
    public function getQuery(Builder $query, array $fields): Builder
    {
        if (empty($fields['search']) && empty($fields['currencies'])) {
            return $query;
        }

        $query->select('income.*');

        if (!empty($fields['search'])) {
            $query->leftJoin('users', function ($join) {
                $join->on('users.id', '=', 'income.client_id')
                    ->orOn('users.id', '=', 'income.staff_id');
            });

            $query->leftJoin('currencies', function ($join) {
                $join->on('currencies.id', '=', 'income.currency_id');
            });

            $query->leftJoin('cashboxes', function ($join) {
                $join->on('cashboxes.id', '=', 'income.cashbox_id');
            });

            $query->orWhere('income.amount', 'like', $fields['search'].'%');
            $query->orWhere('income.code', 'like', '%'.$fields['search'].'%');
            $query->orWhere('income.access_code', 'like', '%'.$fields['search'].'%');
            $query->orWhere('users.first_name', 'like', '%'.$fields['search'].'%');
            $query->orWhere('users.last_name', 'like', '%'.$fields['search'].'%');
            $query->orWhere('currencies.name', 'like', '%'.$fields['search'].'%');
            $query->orWhere('currencies.iso_code', 'like', '%'.$fields['search'].'%');
            $query->orWhere('cashboxes.name', 'like', '%'.$fields['search'].'%');
        }
        if (!empty($fields['currencies'])) {
            $query->orWhereIn('income.currency_id', $fields['currencies']);
        }
        return $query;
    }
}
