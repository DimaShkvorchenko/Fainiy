<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchTransferRequest extends FormRequest
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
            'search' => 'string|max:30',
            'per_page' => 'numeric|min:10|max:500',
            'currencies' => 'array',
            'currencies.*' => 'uuid|exists:App\Models\Currency,id',
            'statuses.*' => 'string|in:new,edited,done,cancelled',
        ];
    }
}
