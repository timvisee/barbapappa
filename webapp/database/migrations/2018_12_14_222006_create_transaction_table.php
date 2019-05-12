<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('transactions', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('description')->nullable(true)->default(null);
            $table->integer('state')->unsigned()->nullable(false);
            $table->integer('reference_to')->unsigned()->nullable(true);
            $table->integer('owner_id')->unsigned()->nullable(true);
            $table->timestamps();

            $table->foreign('reference_to')
                ->references('id')
                ->on('transactions')
                ->onDelete('set null');
            $table->foreign('owner_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('transactions');
    }
}
