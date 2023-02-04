<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transfer;

class TransferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Transfer::factory(30)->create();
    }
}
