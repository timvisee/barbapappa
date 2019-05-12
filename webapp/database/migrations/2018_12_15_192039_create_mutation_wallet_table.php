<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMutationWalletTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('mutations_wallet', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('mutation_id')->unsigned()->nullable(false);
            $table->integer('wallet_id')->unsigned()->nullable(true);
            $table->timestamps();

            $table->foreign('mutation_id')
                ->references('id')
                ->on('mutations')
                ->onDelete('cascade');
            $table->foreign('wallet_id')
                ->references('id')
                ->on('wallets')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('mutations_wallet');
    }
}
