<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStaffRequest extends FormRequest
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
            'first_name' => 'string|max:30|required_without:last_name,phone',
            'last_name' => 'string|max:30|required_without:first_name,phone',
            'phone' => 'string|max:30|required_without:first_name,last_name',
        ];
    }
}
