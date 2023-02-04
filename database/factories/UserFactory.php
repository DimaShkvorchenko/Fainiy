<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        do {
            $email = $this->faker->unique()->safeEmail();
        } while (User::withTrashed()->where('email', $email)->first());

        do {
            $firstName = $this->faker->unique()->firstName();
            $lastName = $this->faker->unique()->lastName();
            $telegram = $firstName . $lastName;
        } while (User::withTrashed()->where('telegram', $telegram)->first());

        return [
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'phone' => $this->faker->phoneNumber(),
            'email_verified_at' => now(),
            'password' => Hash::make('123456'),
            'remember_token' => Str::random(10),
            'telegram' => $telegram,
            'modules' => json_encode(["transfer", "exchange"]),
            'role' => '["cashier"]',
            'registration_data' => json_encode([
                'IP' => $this->faker->ipv4(),
                'time_of_issue' => $this->faker->dateTimeBetween('-30 days', '+30 days')->format('Y-m-d H:i:s')
            ]),
            'code' => $this->faker->ean8(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
