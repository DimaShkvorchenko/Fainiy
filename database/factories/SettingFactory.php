<?php

namespace Database\Factories;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Factories\Factory;

class SettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        do {
            $code = $this->faker->unique()->word();
        } while (!empty(Setting::withTrashed()->where('code', $code)->first()->id));

        return [
            'code' => $code,
            'value' => $this->faker->word(),
        ];
    }
}
