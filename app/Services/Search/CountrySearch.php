<?php

namespace App\Services\Search;

use Illuminate\Database\Eloquent\Builder;

class CountrySearch implements SearchInterface
{
    /**
     * Get search query for filtering of countries listing
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

        $query->orWhere('name', 'like', '%' . $fields['search'] . '%');
        $query->orWhere('iso_code', 'like', '%' . $fields['search'] . '%');

        return $query;
    }
}
