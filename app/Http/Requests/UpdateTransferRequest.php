<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTransferRequest extends FormRequest
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
            'amount' => 'required|numeric|min:1',
            'to_cashbox_id' => 'required|uuid|exists:App\Models\Cashbox,id',
            'description' => 'string|max:5000',
            'status' => 'string|in:new,edited,done,cancelled',
            'time_of_issue' => 'date_format:Y-m-d H:i:s',
        ];
    }
}
