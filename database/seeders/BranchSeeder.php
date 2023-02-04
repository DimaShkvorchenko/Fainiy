<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Locations\Branch;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Branch::factory(30)->create();
    }
}
