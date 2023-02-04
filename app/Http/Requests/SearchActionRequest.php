<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\User;

class SearchActionRequest extends FormRequest
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
            'date_from' => 'date_format:Y-m-d',
            'date_to' => 'date_format:Y-m-d',
            'currencies' => 'array',
            'currencies.*' => 'uuid|exists:App\Models\Currency,id',
            'clients' => 'array',
            'clients.*' => [
                'uuid',
                Rule::exists('App\Models\User','id')->where(function ($query) {
                    $query->whereNull('deleted_at');
                    return $query->where('account_type', User::CLIENT_TYPE);
                }),
            ],
        ];
    }
}
