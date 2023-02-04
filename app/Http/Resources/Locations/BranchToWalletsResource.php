<?php

namespace App\Http\Resources\Locations;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CashboxToWalletsCollection;

class BranchToWalletsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        $wallets = [];
        foreach ($this->cashboxes as $cashbox) {
            if (!empty($cashbox->wallets)) {
                foreach ($cashbox->wallets as $wallet) {
                    if (!empty($wallet->currency->iso_code)) {
                        $wallets[$wallet->currency->iso_code] = ($wallets[$wallet->currency->iso_code] ?? 0) + $wallet->amount;
                    }
                }
            }
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'wallets' => $wallets,
            'cashboxes' => new CashboxToWalletsCollection($this->cashboxes),
        ];
    }
}
