<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\User;

class UpdateTaskRequest extends FormRequest
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
                'required_without_all:title,description,tags,file,completion_date',
                'uuid',
                Rule::exists('App\Models\User','id')->where(function ($query) {
                    $query->whereNull('deleted_at');
                    return $query->where('account_type', User::STAFF_TYPE);
                }),
            ],
            'title' => 'required_without_all:staff_id,description,tags,file,completion_date|string|max:30',
            'description' => 'required_without_all:staff_id,title,tags,file,completion_date|string|max:3000',
            'tags' => 'required_without_all:staff_id,title,description,file,completion_date|string|max:200',
            'file' => 'required_without_all:staff_id,title,description,tags,completion_date|string|max:200',
            'completion_date' => 'required_without_all:staff_id,title,description,tags,file|date_format:Y-m-d H:i:s',
        ];
    }
}
