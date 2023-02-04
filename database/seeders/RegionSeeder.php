<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Locations\Region;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Region::factory(20)->create();
    }
}
