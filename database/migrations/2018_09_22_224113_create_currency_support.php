<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCurrencySupport extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('currency_support', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('economy_id');
            $table->boolean('enabled');
            $table->integer('currency_id');
            $table->boolean('allow_wallet');
            $table->integer('product_price_default')->defaults(3);
            $table->timestamps();
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
