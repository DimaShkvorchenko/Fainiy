<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory(30)->create([
            'account_type' => 2,
            'admin_id' => User::admin()->inRandomOrder()->first()->id
        ]);
    }
}
