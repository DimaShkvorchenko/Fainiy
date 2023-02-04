<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'staff_id' => User::where('account_type', User::STAFF_TYPE)->inRandomOrder()->first(),
            'title' => $this->faker->text(20),
            'description' => $this->faker->text(50),
            'tags'=> $this->faker->text(5),
            'file' => $this->faker->url(),
            'completion_date' => $this->faker->dateTimeBetween('-30 days', '+30 days')->format('Y-m-d H:i:s')
        ];
    }
}
