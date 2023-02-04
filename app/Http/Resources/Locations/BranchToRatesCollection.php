<?php

namespace App\Http\Resources\Locations;

use App\Http\Resources\BaseCollection;
use Illuminate\Http\Request;

class BranchToRatesCollection extends BaseCollection
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
            return $value->cashboxes->isNotEmpty();
        });
    }
}
