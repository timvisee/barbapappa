<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInventoryItemChangeTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('inventory_item_change', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('item_id')->unsigned();
            $table->integer('quantity')->nullable(false);
            $table->integer('user_id')->unsigned()->nullable(true);
            $table->string('comment')->nullable(true);
            $table->integer('mutation_product_id')->unsigned()->nullable(true);
            $table->timestamps();

            $table->foreign('item_id')
                ->references('id')
                ->on('inventory_item')
                ->onDelete('cascade');
            $table->foreign('user_id')
                ->references('id')
                ->on('user')
                ->onDelete('set null');
            $table->foreign('mutation_product_id')
                ->references('id')
                ->on('mutation_product')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('inventory_item_change');
    }
}
