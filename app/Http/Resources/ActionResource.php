<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActionResource extends JsonResource
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
            'created_at' => $this->created_at,
            'type' => $this->type,
            'event' => $this->event,
            'amount' => $this->amount,
            'parent_id' => $this->parent_id,
            'staff' => new UserResource($this->staff),
            'client' => new UserResource($this->client),
            'currency' => new CurrencyResource($this->currency),
            'cashbox' => new CashboxResource($this->cashbox),
            'other_data' => $this->other_data,
        ];
    }
}
