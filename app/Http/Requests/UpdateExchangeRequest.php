<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExchangeRequest extends FormRequest
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
            'access_code' => 'numeric',
            'to_amount' => 'required|numeric|min:1',
            'to_currency_id' => 'required|uuid|exists:App\Models\Currency,id',
            'exchange_rate' => 'required|numeric',
            'description' => 'string|max:5000',
            'status' => 'string|in:created,completed,cancelled',
            'time_of_issue' => 'date_format:Y-m-d H:i:s',
            'spread' => 'numeric',
        ];
    }
}
