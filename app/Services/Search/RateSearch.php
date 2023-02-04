<?php

namespace App\Services\Search;

use Illuminate\Database\Eloquent\Builder;

class RateSearch implements SearchInterface
{
    /**
     * Get search query for filtering of rates listing
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
            && empty($fields['cashboxes'])
            && empty($fields['branches'])
            && empty($fields['cities'])
            && empty($fields['regions'])
            && empty($fields['countries'])
        ) {
            return $query;
        }

        $query->select('rates.*');

        if (!empty($fields['search'])) {
            $query->leftJoin('currencies', function ($join) {
                $join->on('currencies.id', '=', 'rates.from_currency_id')
                    ->orOn('currencies.id', '=', 'rates.to_currency_id');
            });

            $query->leftJoin('cashboxes', function ($join) {
                $join->on('cashboxes.id', '=', 'rates.cashbox_id');
            });

            $query->orWhere('rates.amount', 'like', $fields['search'].'%');
            $query->orWhere('currencies.name', 'like', '%'.$fields['search'].'%');
            $query->orWhere('currencies.iso_code', 'like', '%'.$fields['search'].'%');
            $query->orWhere('cashboxes.name', 'like', '%'.$fields['search'].'%');
        }
        if (!empty($fields['currencies'])) {
            $query->orWhereIn('rates.from_currency_id', $fields['currencies']);
            $query->orWhereIn('rates.to_currency_id', $fields['currencies']);
        }
        if (!empty($fields['cashboxes'])) {
            $query->orWhereIn('rates.cashbox_id', $fields['cashboxes']);
        }
        if (!empty($fields['branches']) || !empty($fields['cities']) || !empty($fields['regions']) || !empty($fields['countries'])) {
            if (empty($fields['search'])) {
                $query->leftJoin('cashboxes', function ($join) {
                    $join->on('cashboxes.id', '=', 'rates.cashbox_id');
                });
            }
            if (!empty($fields['branches'])) {
                $query->orWhereIn('cashboxes.branch_id', $fields['branches']);
            }
        }
        if (!empty($fields['cities']) || !empty($fields['regions']) || !empty($fields['countries'])) {
            $query->leftJoin('branches', function ($join) {
                $join->on('branches.id', '=', 'cashboxes.branch_id');
            });
            if (!empty($fields['cities'])) {
                $query->orWhereIn('branches.city_id', $fields['cities']);
            }
        }
        if (!empty($fields['regions']) || !empty($fields['countries'])) {
            $query->leftJoin('cities', function ($join) {
                $join->on('cities.id', '=', 'branches.city_id');
            });
            if (!empty($fields['regions'])) {
                $query->orWhereIn('cities.region_id', $fields['regions']);
            }
        }
        if (!empty($fields['countries'])) {
            $query->leftJoin('regions', function ($join) {
                $join->on('regions.id', '=', 'cities.region_id');
            });
            $query->orWhereIn('regions.country_id', $fields['countries']);
        }
        return $query;
    }
}
