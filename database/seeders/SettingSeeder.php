<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;
use App\Services\CurrencyService;
use App\Services\Commission\IncomeCommission;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        (new CurrencyService())->getOrSetBasicCurrency();
        (new IncomeCommission())->getOrSetCommissionSetting();

        Setting::factory(10)->create();
    }
}
