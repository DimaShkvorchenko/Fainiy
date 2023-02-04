<?php

namespace App\Services\Search;

use Illuminate\Database\Eloquent\Builder;

class BranchSearch implements SearchInterface
{
    /**
     * Get search query for filtering of branches listing
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

        $query->select('branches.*');

        $query->join('cities', function ($join) {
            $join->on('cities.id', '=', 'branches.city_id');
        });

        $query->orWhere('branches.name', 'like', '%' . $fields['search'] . '%');
        $query->orWhere('cities.name', 'like', '%' . $fields['search'] . '%');

        return $query;
    }
}
