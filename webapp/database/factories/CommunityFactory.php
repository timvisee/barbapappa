<?php

namespace Database\Factories;

use App\Models\Community;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommunityFactory extends Factory {

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Community::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition() {
        return [
            'name' => $this->faker->company,
            'show_explore' => $this->faker->boolean,
            'self_enroll' => $this->faker->boolean,
            'password' => rand(0, 1) == 0 ? $this->faker->numberBetween(1000, 9999) : null,
        ];
    }
}
