<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBarTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('bars', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('community_id')->unsigned();
            $table->integer('economy_id')->unsigned();
            $table->string('name', 255)->nullable(false);
            $table->string('slug', 64)->index()->unique()->nullable(true)->default(null);
            $table->string('description', 2048)->nullable(true)->default(null);
            $table->boolean('show_explore');
            $table->boolean('show_community');
            $table->boolean('self_enroll');
            $table->string('password', 4096)->nullable(true)->default(null);
            $table->timestamps();

            $table->foreign('community_id')
                ->references('id')
                ->on('communities')
                ->onDelete('cascade');
            $table->foreign('economy_id')
                ->references('id')
                ->on('economies')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('bars');
    }
}
