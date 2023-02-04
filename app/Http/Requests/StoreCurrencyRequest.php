<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCurrencyRequest extends FormRequest
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
            'iso_code' => [
                'string',
                'max:3',
                Rule::unique('App\Models\Currency','iso_code')->where(function ($query) {
                    return $query->whereNull('deleted_at');
                }),
            ],
            'name' => 'required|string|max:50',
            'is_visible' => 'boolean',
        ];
    }
}
