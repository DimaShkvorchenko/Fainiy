<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Locations\City;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        City::factory(30)->create();
    }
}
