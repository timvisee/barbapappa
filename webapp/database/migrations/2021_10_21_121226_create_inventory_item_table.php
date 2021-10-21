<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventoryItemTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('inventory_item', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('inventory_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->integer('quantity')->nullable(false)->default(0);
            $table->timestamps();

            $table->foreign('inventory_id')
                ->references('id')
                ->on('inventory')
                ->onDelete('cascade');
            $table->foreign('product_id')
                ->references('id')
                ->on('product')
                ->onDelete('cascade');

            $table->unique(['inventory_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('inventory_item');
    }
}
