<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransferResource extends JsonResource
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
            'amount' => $this->amount,
            'commission' => $this->commission,
            'user' => new UserResource($this->user),
            'client' => new UserResource($this->client),
            'from_cashbox' => new CashboxResource($this->from_cashbox),
            'to_cashbox' => new CashboxResource($this->to_cashbox),
            'currency' => new CurrencyResource($this->currency),
            'description' => $this->description,
            'status' => $this->status,
            'time_of_issue' => $this->time_of_issue,
        ];
    }
}
