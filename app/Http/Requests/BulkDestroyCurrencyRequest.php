<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Services\CurrencyService;

class BulkDestroyCurrencyRequest extends FormRequest
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
        $basicCurrency = (new CurrencyService())->getOrSetBasicCurrency();

        return [
            'ids' => 'required|array',
            'ids.*' => [
                'uuid',
                Rule::exists('App\Models\Currency','id')->where(function ($query) use ($basicCurrency) {
                    if ($basicCurrency) {
                        $query->where('id', '<>', $basicCurrency->id);
                    }
                    return $query;
                }),
            ],
        ];
    }
}
