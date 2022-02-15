<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSessionLinkEmail extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('session_link', function(Blueprint $table) {
            $table->integer('email_id')->after('user_id')->unsigned()->nullable(true);

            $table->foreign('email_id')
                ->references('id')
                ->on('email')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('session_link', function(Blueprint $table) {
            $table->dropForeign(['email_id']);
            $table->dropColumn('email_id');
        });
    }
}
