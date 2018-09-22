<?php

use App\Models\Community;
use App\Models\Economy;
use Illuminate\Database\Seeder;

class CommunitySeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        factory(Community::class, 5)->create()->each(function($community) {
            // Add an economy
            $community->economies()->save(factory(Economy::class)->make());
        });
    }
}
