<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSessionLinkSessionId extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('session_link', function(Blueprint $table) {
            $table->string('laravel_session_id')->after('intended_url')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('session_link', function(Blueprint $table) {
            $table->dropColumn('laravel_session_id');
        });
    }
}
