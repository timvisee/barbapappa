<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEconomyMemberNickname extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('economy_member', function(Blueprint $table) {
            $table->string('nickname')->after('user_id')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('economy_member', function(Blueprint $table) {
            $table->dropColumn('nickname');
        });
    }
}
