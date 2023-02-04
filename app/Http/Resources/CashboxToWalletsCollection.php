<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class CashboxToWalletsCollection extends BaseCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->filter(function ($value, $key) {
            return $value->wallets->isNotEmpty();
        });
    }
}