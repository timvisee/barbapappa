<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductPriceTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('product_prices', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('product_id')->unsigned()->nullable(false);
            $table->integer('currency_id')->unsigned()->nullable(false);
            $table->decimal('price')->nullable(false);
            $table->timestamps();

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');
            $table->foreign('currency_id')
                ->references('id')
                ->on('economy_currencies')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('product_prices');
    }
}
