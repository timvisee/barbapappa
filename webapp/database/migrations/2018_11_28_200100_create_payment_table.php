<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('payments', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('state')->unsigned()->nullable(false);
            $table->integer('payment_service_id')->unsigned()->nullable(true);
            $table->string('reference')->nullable(true);
            $table->decimal('money')->nullable(false);
            $table->integer('currency_id')->unsigned()->nullable(false);
            $table->timestamps();

            $table->foreign('payment_service_id')
                ->references('id')
                ->on('payment_services')
                ->onDelete('set null');
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
        Schema::dropIfExists('payments');
    }
}
