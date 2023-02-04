<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class ActionCollection extends BaseCollection
{
    public function __construct(
        mixed $resource,
        public ?float $total_amount = null
    )
    {
        parent::__construct($resource);
    }

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @param  Request  $request
     * @return array
     */
    public function with($request)
    {
        return [
            'meta' => [
                'per_page_amount' => $this->collection->sum('amount'),
                'total_amount' => $this->total_amount,
            ],
        ];
    }
}
