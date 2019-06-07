<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMutationSpecialTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('mutations_special', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('mutation_id')->unsigned()->nullable(false);
            $table->string('description', 2048)->nullable(true)->default(null);
            $table->timestamps();

            $table->foreign('mutation_id')
                ->references('id')
                ->on('mutations')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('mutations_special');
    }
}
