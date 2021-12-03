<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEconomyMemberTags extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('economy_member', function(Blueprint $table) {
            $table->string('tags')->after('nickname')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('economy_member', function(Blueprint $table) {
            $table->dropColumn('tags');
        });
    }
}
