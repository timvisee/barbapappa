<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEconomyMemberTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('economy_member', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('economy_id')->unsigned();
            $table->integer('user_id')->unsigned()->nullable();
            $table->timestamps();

            $table->foreign('economy_id')
                ->references('id')
                ->on('economies')
                ->onDelete('cascade');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('restrict');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('economy_member');
    }
}
