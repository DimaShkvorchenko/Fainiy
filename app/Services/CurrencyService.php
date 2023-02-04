<?php

namespace App\Services;

use App\Models\{Currency, Setting};

class CurrencyService
{
    /**
     * Get or set up basic currency
     *
     * @return Currency
     */
    public function getOrSetBasicCurrency(): Currency
    {
        if (!$basicCurrencySetting = Setting::where('code', 'basic_currency')->first()) {
            $basicCurrency = Currency::firstOrCreate(
                ['iso_code' => 'UAH'],
                ['name' => 'Гривна']
            );
            Setting::create([
                'code' => 'basic_currency',
                'value' => $basicCurrency->id,
            ]);
            if (empty(Currency::where('iso_code', 'EUR')->first()->id)) {
                Currency::create([
                    'iso_code' => 'EUR',
                    'name' => 'Евро',
                ]);
            }
            if (empty(Currency::where('iso_code', 'USD')->first()->id)) {
                Currency::create([
                    'iso_code' => 'USD',
                    'name' => 'Доллар',
                ]);
            }
            return $basicCurrency;
        }
        return Currency::find($basicCurrencySetting->value);
    }
}
