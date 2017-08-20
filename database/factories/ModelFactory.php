<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Email;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * User factory.
 */
$factory->define(User::class, function(Faker\Generator $faker) {
    static $password;

    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'password' => Hash::make('secret'),
    ];
});

/**
 * Email factory.
 */
$factory->define(Email::class, function(Faker\Generator $faker) {
    return [
        'email' => $faker->unique()->safeEmail,
        'verified_at' => $faker->dateTime(),
        'verified_ip' => $faker->ipv4
    ];
});
