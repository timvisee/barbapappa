<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBarPublicHistoryAndTallies extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('bar', function(Blueprint $table) {
            $table->boolean('show_history')->after('show_community')->default(true)->nullable(false);
            $table->boolean('show_tallies')->after('show_history')->default(true)->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('bar', function(Blueprint $table) {
            $table->dropColumn('show_tallies');
            $table->dropColumn('show_history');
        });
    }
}
