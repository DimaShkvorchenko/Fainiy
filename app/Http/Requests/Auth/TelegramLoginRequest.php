<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class TelegramLoginRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'required|integer',
            'username' => 'required|string|max:30',
            'auth_date' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if ($value < strtotime('-2 hours')) {
                        $fail('Credential is outdated.');
                    }
                },
            ],
            'hash' => [
                'required',
                'string',
                function ($attribute, $hashValue, $fail) {
                    $fields = $this->except(['hash']);
                    $dataCheckArr = [];
                    foreach ($fields as $key => $value) {
                        $dataCheckArr[] = $key . '=' . $value;
                    }
                    sort($dataCheckArr);
                    $secret_key = hash('sha256', config('social.telegram_bot_token'), true);
                    $hash = hash_hmac('sha256', implode("\n", $dataCheckArr), $secret_key);
                    if (strcmp($hash, $hashValue) !== 0) {
                        $fail('Credential is NOT from Telegram.');
                    }
                },
            ],
            'first_name' => 'string|max:30',
            'last_name' => 'string|max:30',
            'photo_url' => 'string|max:200',
        ];
    }
}
