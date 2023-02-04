<?php

namespace App\Services\Search;

use Illuminate\Database\Eloquent\Builder;

class TaskSearch implements SearchInterface
{
    /**
     * Get search query for filtering of tasks listing
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

        $query->select('tasks.*');

        $query->leftJoin('users', function ($join) {
            $join->on('users.id', '=', 'tasks.staff_id');
        });

        $query->orWhere('tasks.title', 'like', '%' . $fields['search'] . '%');
        $query->orWhere('tasks.description', 'like', '%' . $fields['search'] . '%');
        $query->orWhere('tasks.tags', 'like', '%' . $fields['search'] . '%');
        $query->orWhere('tasks.file', 'like', '%' . $fields['search'] . '%');
        $query->orWhere('users.first_name', 'like', '%'.$fields['search'].'%');
        $query->orWhere('users.last_name', 'like', '%'.$fields['search'].'%');

        return $query;
    }
}
