<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProductUsers extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('product', function(Blueprint $table) {
            // Remove unused user_id column
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');

            // Add two new user columns
            $table->integer('created_user_id')->after('enabled')->unsigned()->nullable(true);
            $table->integer('updated_user_id')->after('created_user_id')->unsigned()->nullable(true);

            $table->foreign('created_user_id')
                ->references('id')
                ->on('user')
                ->onDelete('set null');
            $table->foreign('updated_user_id')
                ->references('id')
                ->on('user')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('product', function(Blueprint $table) {
            // Remove two new user columns
            $table->dropForeign(['created_user_id']);
            $table->dropForeign(['updated_user_id']);
            $table->dropColumn('created_user_id');
            $table->dropColumn('updated_user_id');

            // Add unused user_id column
            $table->integer('user_id')->after('economy_id')->unsigned()->nullable(true);

            $table->foreign('user_id')
                ->references('id')
                ->on('user')
                ->onDelete('set null');
        });
    }
}
