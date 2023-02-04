<?php

namespace App\Services\Search;

use Illuminate\Database\Eloquent\Builder;

class BranchToWalletsSearch implements SearchInterface
{
    /**
     * Get search query for filtering of branches to wallets listing
     *
     * @param Builder $query
     * @param array $fields
     * @return Builder
     */
    public function getQuery(Builder $query, array $fields): Builder
    {
        $query->select('branches.*');

        $query->leftJoin('cashboxes', function ($join) {
            $join->on('cashboxes.branch_id', '=', 'branches.id');
        });
        $query->leftJoin('wallet', function ($join) {
            $join->on('wallet.cashbox_id', '=', 'cashboxes.id');
        });
        if (!empty($fields['currencies']) || !empty($fields['search'])) {
            $query->leftJoin('currencies', function ($join) {
                $join->on('currencies.id', '=', 'wallet.currency_id');
            });
            $query->whereNull('currencies.deleted_at');
        }
        $query->whereNotNull('wallet.id');
        $query->whereNull('cashboxes.deleted_at');
        $query->whereNull('wallet.deleted_at');

        if (!empty($fields['search'])) {
            $query->where(function($query) use($fields) {
                $query->orWhere('branches.name', 'like', '%'.$fields['search'].'%');
                $query->orWhere('cashboxes.name', 'like', '%'.$fields['search'].'%');
                $query->orWhere('wallet.amount', 'like', '%'.$fields['search'].'%');
                $query->orWhere('currencies.name', 'like', '%'.$fields['search'].'%');
                $query->orWhere('currencies.iso_code', 'like', '%'.$fields['search'].'%');
            });
        }
        $query->where(function($query) use($fields) {
            if (!empty($fields['branches'])) {
                $query->orWhereIn('branches.id', $fields['branches']);
            }
            if (!empty($fields['cashboxes'])) {
                $query->orWhereIn('cashboxes.id', $fields['cashboxes']);
            }
            if (!empty($fields['wallets'])) {
                $query->orWhereIn('wallet.id', $fields['wallets']);
            }
            if (!empty($fields['currencies'])) {
                $query->orWhereIn('currencies.id', $fields['currencies']);
            }
        });
        $query->groupBy('branches.id');
        return $query;
    }
}
