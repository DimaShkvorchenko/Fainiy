<?php

namespace App\Services\Search;

use Illuminate\Database\Eloquent\Builder;

class SettingSearch implements SearchInterface
{
    /**
     * Get search query for filtering of settings listing
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

        $query->orWhere('code', 'like', '%' . $fields['search'] . '%');
        $query->orWhere('value', 'like', '%' . $fields['search'] . '%');

        return $query;
    }
}
