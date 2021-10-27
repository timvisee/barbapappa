<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductInventoryItemTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('product_inventory_item', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->integer('inventory_product_id')->unsigned();
            $table->integer('quantity')->nullable(false)->default(1);
            $table->timestamps();

            $table->foreign('product_id')
                ->references('id')
                ->on('product')
                ->onDelete('cascade');
            $table->foreign('inventory_product_id')
                ->references('id')
                ->on('product')
                ->onDelete('cascade');

            $table->unique(['product_id', 'inventory_product_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('product_inventory_item');
    }
}
