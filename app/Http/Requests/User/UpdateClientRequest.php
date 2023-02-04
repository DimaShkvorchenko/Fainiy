<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClientRequest extends FormRequest
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
            'first_name' => 'string|max:30|required_without:last_name,phone,telegram,code,modules,other_data',
            'last_name' => 'string|max:30|required_without:first_name,phone,telegram,code,modules,other_data',
            'phone' => 'string|max:30|required_without:first_name,last_name,telegram,code,modules,other_data',
            'telegram' => 'string|max:30|unique:App\Models\User|required_without:first_name,last_name,phone,code,modules,other_data',
            'code' => 'string|max:30|required_without:first_name,last_name,phone,telegram,modules,other_data',
            'modules' => 'json|required_without:first_name,last_name,phone,telegram,code,other_data',
            'other_data' => 'json|required_without:first_name,last_name,phone,telegram,code,modules',
        ];
    }
}
