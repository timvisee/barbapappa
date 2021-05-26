<?php

namespace Database\Factories;

use App\Models\Email;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class EmailFactory extends Factory {

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Email::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition() {
        return [
            'email' => $this->faker->unique()->safeEmail,
            'verified_at' => $this->faker->dateTime(),
            'verified_ip' => $this->faker->ipv4
        ];
    }
}
