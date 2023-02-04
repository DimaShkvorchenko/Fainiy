<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRateRequest extends FormRequest
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
            'cashbox_id' => 'required|uuid|exists:App\Models\Cashbox,id',
            'currencies' => 'required|array',
            'currencies.*' => 'uuid|exists:App\Models\Currency,id',
        ];
    }
}
