<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSessionUserAgent extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('session', function(Blueprint $table) {
            $table->string('created_user_agent')->after('created_ip')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('session', function(Blueprint $table) {
            $table->dropColumn('created_user_agent');
        });
    }
}
