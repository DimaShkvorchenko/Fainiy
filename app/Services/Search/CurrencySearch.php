<?php

namespace App\Services\Search;

use Illuminate\Database\Eloquent\Builder;

class CurrencySearch implements SearchInterface
{
    /**
     * Get search query for filtering of currencies listing
     *
     * @param Builder $query
     * @param array $fields
     * @return Builder
     */
    public function getQuery(Builder $query, array $fields): Builder
    {
        if (empty($fields['search']) && empty($fields['cashboxes'])) {
            return $query;
        }
        $query->select('currencies.*');

        if (!empty($fields['cashboxes'])) {
            $query->join('rates', function ($join) {
                $join->on('rates.from_currency_id', '=', 'currencies.id');
            });
            $query->whereIn('rates.cashbox_id', $fields['cashboxes']);
        }
        if (!empty($fields['search'])) {
            $query->where(function($query) use($fields) {
                $query->orWhere('currencies.name', 'like', '%'.$fields['search'].'%');
                $query->orWhere('currencies.iso_code', 'like', '%'.$fields['search'].'%');
            });
        }
        return $query;
    }
}
