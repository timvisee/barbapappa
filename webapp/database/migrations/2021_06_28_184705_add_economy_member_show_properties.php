<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEconomyMemberShowProperties extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('economy_member', function(Blueprint $table) {
            $table->boolean('show_in_buy')->after('user_id')->default(true);
            $table->boolean('show_in_kiosk')->after('user_id')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('economy_member', function(Blueprint $table) {
            $table->dropColumn(['show_in_buy', 'show_in_kiosk']);
        });
    }
}
