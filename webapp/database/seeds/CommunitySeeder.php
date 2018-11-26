<?php

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
        factory(Community::class, 5)->create()->each(function($community) {
            // Add random economies
            $community->economies()->save(
                    factory(Economy::class)->make()
                )
                ->each(function($economy) use($community) {
                    $economy->bars()->save(
                        factory(Bar::class)->make([
                            'community_id' => $community->id
                        ])
                    );
                });
        });
    }
}
