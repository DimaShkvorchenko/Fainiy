<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\User;

class StoreTaskRequest extends FormRequest
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
            'staff_id' => [
                'uuid',
                Rule::exists('App\Models\User','id')->where(function ($query) {
                    $query->whereNull('deleted_at');
                    return $query->where('account_type', User::STAFF_TYPE);
                }),
            ],
            'title' => 'required|string|max:100',
            'description' => 'string|max:3000',
            'tags' => 'string|max:200',
            'file' => 'string|max:200',
            'completion_date' => 'date_format:Y-m-d H:i:s',
        ];
    }
}
