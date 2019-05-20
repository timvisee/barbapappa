<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommunityTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('communities', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('name', 255);
            $table->string('slug', 64)->index()->unique()->nullable(true)->default(null);
            $table->string('description', 2048)->nullable(true)->default(null);
            $table->boolean('show_explore');
            $table->boolean('self_enroll');
            $table->string('password', 4096)->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('communities');
    }
}
