<?php

use App\Models\Email;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        // Make the users
        factory(User::class, 50)->create()->each(function($user) {
            // Add an email address
            $user->emails()->save(factory(Email::class)->make());
        });
    }
}
