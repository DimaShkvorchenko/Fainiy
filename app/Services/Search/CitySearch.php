<?php

namespace App\Services\Search;

use Illuminate\Database\Eloquent\Builder;

class CitySearch implements SearchInterface
{
    /**
     * Get search query for filtering of cities listing
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

        $query->select('cities.*');

        $query->join('regions', function ($join) {
            $join->on('regions.id', '=', 'cities.region_id');
        });

        $query->orWhere('cities.name', 'like', '%' . $fields['search'] . '%');
        $query->orWhere('regions.name', 'like', '%' . $fields['search'] . '%');

        return $query;
    }
}
