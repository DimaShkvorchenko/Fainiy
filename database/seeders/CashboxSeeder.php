<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cashbox;

class CashboxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Cashbox::factory(30)->create();
    }
}
