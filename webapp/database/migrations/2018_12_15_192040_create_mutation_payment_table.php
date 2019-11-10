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
        Schema::create('mutation_payment', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('payment_id')->unsigned()->nullable(true);
            $table->timestamps();

            $table->foreign('payment_id')
                ->references('id')
                ->on('payment')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('mutation_payment');
    }
}
