<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMutationPaymentTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('mutations_payment', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('mutation_id')->unsigned();
            $table->integer('payment_id')->unsigned()->nullable(true);
            $table->timestamps();

            $table->foreign('mutation_id')
                ->references('id')
                ->on('mutations')
                ->onDelete('cascade');
            $table->foreign('payment_id')
                ->references('id')
                ->on('payments')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('mutations_payment');
    }
}
