<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\User;

class StoreIncomeRequest extends FormRequest
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
            'client_id' => [
                'required',
                'uuid',
                Rule::exists('App\Models\User','id')->where(function ($query) {
                    $query->whereNull('deleted_at');
                    return $query->where('account_type', User::CLIENT_TYPE);
                }),
            ],
            'staff_id' => [
                'required',
                'uuid',
                Rule::exists('App\Models\User','id')->where(function ($query) {
                    $query->whereNull('deleted_at');
                    return $query->where('account_type', User::STAFF_TYPE);
                }),
            ],
            'amount' => 'required|numeric|min:1',
            'currency_id' => 'required|uuid|exists:App\Models\Currency,id',
            'cashbox_id' => 'required|uuid|exists:App\Models\Cashbox,id',
            'code' => 'numeric',
            'access_code' => 'numeric',
        ];
    }
}
