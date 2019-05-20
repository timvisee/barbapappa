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

use App\Models\Bar;
use App\Models\Community;
use App\Models\Economy;
use App\Models\Email;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * User factory.
 */
$factory->define(User::class, function(Faker\Generator $faker) {
    // Get a random locale
    $locale = config('app.locales')[array_rand(config('app.locales'))];

    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'locale' => rand(0, 1) == 0 ? $locale : null,
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

/**
 * Community factory.
 */
$factory->define(Community::class, function(Faker\Generator $faker) {
    return [
        'name' => $faker->company,
        'show_explore' => $faker->boolean,
        'self_enroll' => $faker->boolean,
        'password' => rand(0, 1) == 0 ? $faker->numberBetween(1000, 9999) : null,
    ];
});

/**
 * Economy factory.
 */
$factory->define(Economy::class, function(Faker\Generator $faker) {
    return [
        'name' => rand(0, 1) == 0 ? "Main economy" : null,
    ];
});

/**
 * Bar factory.
 */
$factory->define(Bar::class, function(Faker\Generator $faker) {
    return [
        'name' => $faker->company,
        'show_explore' => $faker->boolean,
        'show_community' => $faker->boolean,
        'self_enroll' => $faker->boolean,
        'password' => rand(0, 1) == 0 ? $faker->numberBetween(1000, 9999) : null,
        'slug' => rand(0, 1) == 0 ? $faker->userName : null,
    ];
});
