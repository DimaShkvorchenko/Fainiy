<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'amount' => 'required|numeric',
            'cashboxes' => 'array',
            'cashboxes.*' => 'uuid|exists:App\Models\Cashbox,id',
            'branches' => 'array',
            'branches.*' => 'uuid|exists:App\Models\Locations\Branch,id',
            'cities' => 'array',
            'cities.*' => 'uuid|exists:App\Models\Locations\City,id',
            'regions' => 'array',
            'regions.*' => 'uuid|exists:App\Models\Locations\Region,id',
            'countries' => 'array',
            'countries.*' => 'uuid|exists:App\Models\Locations\Country,id',
        ];
    }
}
