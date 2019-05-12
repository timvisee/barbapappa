<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommunityUserTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('community_user', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('community_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('role')->unsigned()->default(0);
            $table->timestamp('visited_at')->nullable(true);
            $table->timestamps();

            $table->foreign('community_id')
                ->references('id')
                ->on('communities')
                ->onDelete('cascade');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->unique(['community_id', 'user_id']);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('community_user');
    }
}
