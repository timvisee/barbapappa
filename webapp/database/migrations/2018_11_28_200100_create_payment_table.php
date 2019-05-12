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
            $table->string('reference');
            $table->decimal('money')->nullable(false);
            // TODO: is this the correct data type, foreign key to currencies table instead?
            $table->string('currency')->nullable(false);
            $table->timestamps();

            $table->foreign('payment_service_id')
                ->references('id')
                ->on('payment_services')
                ->onDelete('set null');
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
