<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCurrencySupportTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('currency_support', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('economy_id')->unsigned();
            $table->integer('currency_id')->unsigned();
            $table->boolean('enabled')->default(true);
            $table->boolean('allow_wallet')->default(true);
            $table->integer('product_price_default')->defaults(3);
            $table->timestamps();

            $table->foreign('economy_id')
                ->references('id')
                ->on('economies')
                ->onDelete('cascade');
            $table->foreign('currency_id')
                ->references('id')
                ->on('currencies')
                ->onDelete('restrict');

            $table->unique(['economy_id', 'currency_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('currency_support');
    }
}
