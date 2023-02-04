<?php

namespace App\Services\Search;

use Illuminate\Database\Eloquent\Builder;

class CashboxSearch implements SearchInterface
{
    /**
     * Get search query for filtering of cashboxes listing
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

        $query->select('cashboxes.*');

        $query->leftJoin('branches', function ($join) {
            $join->on('branches.id', '=', 'cashboxes.branch_id');
        });

        $query->orWhere('cashboxes.number', 'like', '%' . $fields['search'] . '%');
        $query->orWhere('cashboxes.name', 'like', '%' . $fields['search'] . '%');
        $query->orWhere('cashboxes.settings', 'like', '%' . $fields['search'] . '%');
        $query->orWhere('branches.name', 'like', '%' . $fields['search'] . '%');

        return $query;
    }
}
