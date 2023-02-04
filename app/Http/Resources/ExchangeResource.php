<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExchangeResource extends JsonResource
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
            'access_code' => $this->access_code,
            'from_amount' => $this->from_amount,
            'to_amount' => $this->to_amount,
            'commission' => $this->commission,
            'user' => new UserResource($this->user),
            'client' => new UserResource($this->client),
            'cashbox' => new CashboxResource($this->cashbox),
            'from_currency' => new CurrencyResource($this->from_currency),
            'to_currency' => new CurrencyResource($this->to_currency),
            'exchange_rate' => $this->exchange_rate,
            'exchange_type' => $this->exchange_type,
            'description' => $this->description,
            'status' => $this->status,
            'time_of_issue' => $this->time_of_issue,
            'spread' => $this->spread,
        ];
    }
}
