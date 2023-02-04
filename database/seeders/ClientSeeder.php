<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory;
use App\Services\Commission\IncomeCommission;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();
        $income_commission = (new IncomeCommission())->getOrSetCommissionSetting()->value;

        User::factory(30)->create([
            'account_type' => 3,
            'admin_id' => User::admin()->inRandomOrder()->first(),
            'other_data' => json_encode([
                'nickname' => $faker->name(),
                'phone2' => $faker->phoneNumber(),
                'referral' => $faker->uuid(),
                'income_commission' => $income_commission
            ]),
        ]);
    }
}
