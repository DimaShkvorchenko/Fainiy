<?php

namespace App\Services\Search;

use Illuminate\Database\Eloquent\Builder;

class ActionSearch implements SearchInterface
{
    /**
     * Get search query for filtering of actions listing
     *
     * @param Builder $query
     * @param array $fields
     * @return Builder
     */
    public function getQuery(Builder $query, array $fields): Builder
    {
        if (
            empty($fields['search'])
            && empty($fields['date_from'])
            && empty($fields['date_to'])
            && empty($fields['clients'])
            && empty($fields['currencies'])
        ) {
            return $query;
        }

        $query->select('actions.*');

        if (!empty($fields['date_from'])) {
            $query->where('actions.created_at', '>=', $fields['date_from'] . ' 00:00:00');
        }
        if (!empty($fields['date_to'])) {
            $query->where('actions.created_at', '<=', $fields['date_to'] . ' 23:59:59');
        }
        if (!empty($fields['search'])) {
            $query->leftJoin('users', function ($join) {
                $join->on('users.id', '=', 'actions.client_id')
                    ->orOn('users.id', '=', 'actions.staff_id');
            });

            $query->leftJoin('currencies', function ($join) {
                $join->on('currencies.id', '=', 'actions.currency_id');
            });

            $query->where(function($query) use($fields) {
                $query->orWhere('actions.amount', 'like', '%' . $fields['search'] . '%');
                $query->orWhere('actions.other_data', 'like', '%' . $fields['search'] . '%');
                $query->orWhere('users.first_name', 'like', '%' . $fields['search'] . '%');
                $query->orWhere('users.last_name', 'like', '%' . $fields['search'] . '%');
                $query->orWhere('currencies.name', 'like', '%' . $fields['search'] . '%');
                $query->orWhere('currencies.iso_code', 'like', '%' . $fields['search'] . '%');
            });
        }
        if (!empty($fields['clients']) || !empty($fields['currencies'])) {
            $query->where(function($query) use($fields) {
                if (!empty($fields['clients'])) {
                    $query->orWhereIn('actions.client_id', $fields['clients']);
                }
                if (!empty($fields['currencies'])) {
                    $query->orWhereIn('actions.currency_id', $fields['currencies']);
                }
            });
        }

        return $query;
    }
}
