<?php

namespace App\Services\Search;

use Illuminate\Database\Eloquent\Builder;

class RegionSearch implements SearchInterface
{
    /**
     * Get search query for filtering of regions listing
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

        $query->select('regions.*');

        $query->join('countries', function ($join) {
            $join->on('countries.id', '=', 'regions.country_id');
        });

        $query->orWhere('regions.name', 'like', '%' . $fields['search'] . '%');
        $query->orWhere('countries.name', 'like', '%' . $fields['search'] . '%');
        $query->orWhere('countries.iso_code', 'like', '%' . $fields['search'] . '%');

        return $query;
    }
}
