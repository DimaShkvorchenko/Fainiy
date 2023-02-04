<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IncomeResource extends JsonResource
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
            'client' => new UserResource($this->client),
            'staff' => new UserResource($this->staff),
            'amount' => $this->amount,
            'commission' => $this->commission,
            'profit' => $this->profit,
            'currency' => new CurrencyResource($this->currency),
            'code' => $this->code,
            'access_code' => $this->access_code,
            'cashbox' => new CashboxResource($this->cashbox),
        ];
    }
}
