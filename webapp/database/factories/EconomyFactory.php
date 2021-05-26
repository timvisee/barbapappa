<?php

namespace Database\Factories;

use App\Models\Economy;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class EconomyFactory extends Factory {

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Economy::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition() {
        return [
            'name' => rand(0, 1) == 0 ? "Main economy" : null,
        ];
    }
}
