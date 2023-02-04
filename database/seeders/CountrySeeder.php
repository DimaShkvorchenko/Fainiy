<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Locations\Country;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Country::factory(20)->create();
    }
}
