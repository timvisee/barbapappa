<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWalletTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('wallets', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('economy_member_id')->unsigned();
            $table->string('name');
            $table->decimal('balance')->default('0.00');
            $table->integer('currency_id')->unsigned();
            $table->timestamps();

            $table->foreign('economy_member_id')
                ->references('id')
                ->on('economy_member')
                ->onDelete('restrict');
            $table->foreign('currency_id')
                ->references('id')
                ->on('currencies')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('wallets');
    }
}
