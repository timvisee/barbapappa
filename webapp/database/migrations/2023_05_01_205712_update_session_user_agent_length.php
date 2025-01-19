<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSessionUserAgentLength extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('session', function (Blueprint $table) {
            $table->string('created_user_agent', 8192)->change();
        });
        Schema::table('kiosk_session', function (Blueprint $table) {
            $table->string('created_user_agent', 8192)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('kiosk_session', function (Blueprint $table) {
            $table->string('created_user_agent', 191)->change();
        });
        Schema::table('session', function (Blueprint $table) {
            $table->string('created_user_agent', 191)->change();
        });
    }
}
