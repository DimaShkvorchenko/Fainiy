<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RateWithoutCashboxResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'from_currency' => new CurrencyResource($this->from_currency),
            'to_currency' => new CurrencyResource($this->to_currency),
            'amount' => $this->amount,
        ];
    }
}
