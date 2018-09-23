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
            $table->string('name')->nullable(false);
            $table->boolean('visible');
            $table->boolean('public');
            $table->string('password')->nullable(true)->default(null);
            $table->string('slug')->index()->unique()->nullable(true)->default(null);
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
