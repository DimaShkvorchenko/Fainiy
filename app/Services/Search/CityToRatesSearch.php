<?php

namespace App\Services\Search;

use Illuminate\Database\Eloquent\Builder;

class CityToRatesSearch implements SearchInterface
{
    /**
     * Get search query for filtering of cities to rates listing
     *
     * @param Builder $query
     * @param array $fields
     * @return Builder
     */
    public function getQuery(Builder $query, array $fields): Builder
    {
        $query->select('cities.*');

        $query->leftJoin('branches', function ($join) {
            $join->on('branches.city_id', '=', 'cities.id');
        });
        $query->leftJoin('cashboxes', function ($join) {
            $join->on('cashboxes.branch_id', '=', 'branches.id');
        });
        $query->leftJoin('rates', function ($join) {
            $join->on('rates.cashbox_id', '=', 'cashboxes.id');
        });
        if (!empty($fields['currencies']) || !empty($fields['search'])) {
            $query->leftJoin('currencies', function ($join) {
                $join->on('currencies.id', '=', 'rates.from_currency_id')
                    ->orOn('currencies.id', '=', 'rates.to_currency_id');
            });
            $query->whereNull('currencies.deleted_at');
        }
        $query->whereNotNull('rates.id');
        $query->whereNull('branches.deleted_at');
        $query->whereNull('cashboxes.deleted_at');
        $query->whereNull('rates.deleted_at');

        if (!empty($fields['search'])) {
            $query->where(function($query) use($fields) {
                $query->orWhere('cities.name', 'like', '%'.$fields['search'].'%');
                $query->orWhere('branches.name', 'like', '%'.$fields['search'].'%');
                $query->orWhere('cashboxes.name', 'like', '%'.$fields['search'].'%');
                $query->orWhere('rates.amount', 'like', '%'.$fields['search'].'%');
                $query->orWhere('currencies.name', 'like', '%'.$fields['search'].'%');
                $query->orWhere('currencies.iso_code', 'like', '%'.$fields['search'].'%');
            });
        }
        $query->where(function($query) use($fields) {
            if (!empty($fields['cities'])) {
                $query->orWhereIn('cities.id', $fields['cities']);
            }
            if (!empty($fields['branches'])) {
                $query->orWhereIn('branches.id', $fields['branches']);
            }
            if (!empty($fields['cashboxes'])) {
                $query->orWhereIn('cashboxes.id', $fields['cashboxes']);
            }
            if (!empty($fields['currencies'])) {
                $query->orWhereIn('currencies.id', $fields['currencies']);
            }
        });
        $query->groupBy('cities.id');
        return $query;
    }
}
