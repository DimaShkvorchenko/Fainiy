<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Currency;
use App\Services\CurrencyService;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        (new CurrencyService())->getOrSetBasicCurrency();

        Currency::factory(20)->create();
    }
}
