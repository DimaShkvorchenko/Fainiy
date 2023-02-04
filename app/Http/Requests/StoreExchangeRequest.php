<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use App\Models\{User, Wallet};

class StoreExchangeRequest extends FormRequest
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
            'from_amount' => [
                'required',
                'numeric',
                'min:1',
                function ($attribute, $value, $fail) {
                    $fromCurrencyWalletAmount = 0;
                    if ($fromCurrencyCashboxWallet = Wallet::withTrashed()
                        ->where('currency_id', $this->from_currency_id)
                        ->where('cashbox_id', $this->cashbox_id)->first()) {
                        $fromCurrencyWalletAmount = $fromCurrencyCashboxWallet->amount;
                    }
                    if ($value > $fromCurrencyWalletAmount) {
                        $fail("The ".$attribute." more than cashbox's wallet amount (" . $fromCurrencyWalletAmount . ")");
                    }
                    if (empty($this->not_check_client_wallet)) {
                        $fromCurrencyWalletAmount = 0;
                        if ($fromCurrencyClientWallet = Wallet::withTrashed()
                            ->where('currency_id', $this->from_currency_id)
                            ->where('client_id', $this->client_id)->first()) {
                            $fromCurrencyWalletAmount = $fromCurrencyClientWallet->amount;
                        }
                        if ($value > $fromCurrencyWalletAmount) {
                            $fail("The ".$attribute." more than client's wallet amount (".$fromCurrencyWalletAmount.")");
                        }
                    }
                },
            ],
            'to_amount' => 'required|numeric|min:1',
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
                        if (Str::contains($client?->modules, 'exchange') !== TRUE) {
                            $fail('Client has not got exchange module.');
                        }
                    }
                },
            ],
            'not_check_client_wallet' => 'boolean',
            'cashbox_id' => 'required|uuid|exists:App\Models\Cashbox,id',
            'from_currency_id' => 'required|uuid|exists:App\Models\Currency,id',
            'to_currency_id' => 'required|uuid|different:from_currency_id|exists:App\Models\Currency,id',
            'exchange_rate' => 'required|numeric',
            'exchange_type' => 'string|in:buy,sell,cross',
            'description' => 'string|max:5000',
            'status' => 'string|in:created,completed,cancelled',
            'time_of_issue' => 'date_format:Y-m-d H:i:s',
            'spread' => 'numeric',
        ];
    }
}
