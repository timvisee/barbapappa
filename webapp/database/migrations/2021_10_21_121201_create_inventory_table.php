<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventoryTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('inventory', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('name')->nullable(false);
            $table->integer('economy_id')->unsigned();
            $table->timestamps();

            $table->foreign('economy_id')
                ->references('id')
                ->on('economy')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('inventory');
    }
}
