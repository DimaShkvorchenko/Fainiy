<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            AdminSeeder::class,
            StaffSeeder::class,
            ClientSeeder::class,
            CountrySeeder::class,
            RegionSeeder::class,
            CitySeeder::class,
            BranchSeeder::class,
            CashboxSeeder::class,
            CurrencySeeder::class,
            SettingSeeder::class,
            TaskSeeder::class,
            WalletSeeder::class,
            IncomeSeeder::class,
            TransferSeeder::class,
            ExchangeSeeder::class,
            ActionSeeder::class,
            PermissionTableSeeder::class,
            RoleTableSeeder::class,
            RateSeeder::class
        ]);
    }
}
