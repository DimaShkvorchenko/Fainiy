<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use App\Models\User;

class StoreTransferRequest extends FormRequest
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
            'commission' => 'json',
            'user_id' => [
                'required',
                'uuid',
                Rule::exists('App\Models\User','id')->where(function ($query) {
                    $query->whereNull('deleted_at');
                    return $query->where('account_type', User::STAFF_TYPE);
                }),
            ],
            'client_id' => [
                'required',
                'uuid',
                Rule::exists('App\Models\User','id')->where(function ($query) {
                    $query->whereNull('deleted_at');
                    return $query->where('account_type', User::CLIENT_TYPE);
                }),
                function ($attribute, $value, $fail) {
                    if ($client = User::find($value)) {
                        if (Str::contains($client?->modules, 'transfer') !== TRUE) {
                            $fail('Client has not got transfer module.');
                        }
                    }
                },
            ],
            'from_cashbox_id' => 'required|uuid|exists:App\Models\Cashbox,id',
            'to_cashbox_id' => 'required|uuid|different:from_cashbox_id|exists:App\Models\Cashbox,id',
            'currency_id' => 'required|uuid|exists:App\Models\Currency,id',
            'description' => 'string|max:5000',
            'status' => 'required|string|in:new,edited,done,cancelled',
            'time_of_issue' => 'date_format:Y-m-d H:i:s',
        ];
    }
}
