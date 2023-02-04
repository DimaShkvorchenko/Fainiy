<?php

namespace App\Services\Search;

use Illuminate\Database\Eloquent\Builder;

class UserSearch implements SearchInterface
{
    /**
     * Get search query for filtering of users listing
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

        $query->where(function($query) use ($fields) {
            $query->orWhere('first_name', 'like', '%' . $fields['search'] . '%');
            $query->orWhere('last_name', 'like', '%' . $fields['search'] . '%');
            $query->orWhere('phone', 'like', '%' . $fields['search'] . '%');
            $query->orWhere('email', 'like', '%' . $fields['search'] . '%');
        });

        return $query;
    }
}
