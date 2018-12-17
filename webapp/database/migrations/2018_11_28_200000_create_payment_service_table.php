<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentServiceTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('payment_service', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('economy_id')->unsigned()->nullable(true);
            $table->integer('service_type')->unsigned()->nullable(false);
            $table->boolean('deposit')->setNullable(false);
            $table->boolean('withdraw')->setNullable(false);
            $table->boolean('enabled')->setNullable(false)->default(true);
            $table->boolean('archived')->setNullable(false)->default(false);
            $table->timestamps();

            $table->foreign('economy_id')
                ->references('id')
                ->on('economies')
                ->onDelete('set null');

            // TODO: add a field for supported currency/currencies by this service
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('payment_service');
    }
}
