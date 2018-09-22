<?php

use App\Models\Community;
use Illuminate\Database\Seeder;

class CommunitySeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        factory(Community::class, 5)->create();
    }
}
