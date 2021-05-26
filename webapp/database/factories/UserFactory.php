<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory {

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition() {
        // Get a random locale
        $locale = config('app.locales')[array_rand(config('app.locales'))];

        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'locale' => rand(0, 1) == 0 ? $locale : null,
            'password' => Hash::make('secret'),
        ];
    }
}
