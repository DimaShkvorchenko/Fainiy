<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
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
            'first_name' => 'required|string|max:30',
            'last_name' => 'required|string|max:30',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|max:30|confirmed',
            'phone' => 'string|max:30',
            'telegram' => 'string|max:30|unique:App\Models\User',
            'code' => 'string|max:30',
            'modules' => 'json',
            'registration_data' => 'json',
            'other_data' => 'json',
        ];
    }
}
