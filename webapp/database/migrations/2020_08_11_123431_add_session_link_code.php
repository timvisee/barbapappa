<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSessionLinkCode extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('session_link', function(Blueprint $table) {
            $table->string('code')->after('laravel_session_id')->nullable(true);
            $table->datetime('code_expire_at')->after('code')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('session_link', function(Blueprint $table) {
            $table->dropColumn('code');
            $table->dropColumn('code_expire_at');
        });
    }
}
