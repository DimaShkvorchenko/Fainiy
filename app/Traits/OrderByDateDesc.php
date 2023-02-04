<?php

namespace App\Traits;

use App\Scopes\OrderByDateDescScope;

trait OrderByDateDesc
{
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::addGlobalScope(new OrderByDateDescScope);
    }
}
