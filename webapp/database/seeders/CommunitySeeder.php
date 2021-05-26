<?php

namespace Database\Seeders;

use App\Models\Bar;
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
        Community::factory()->count(5)->create()->each(function($community) {
            // Add random economies
            $community
                ->economies()
                ->save(Economy::factory()->make())
                ->each(function($economy) use($community) {
                    $economy->bars()->save(
                        Bar::factory()->make([
                            'community_id' => $community->id
                        ])
                    );
                });
        });
    }
}
