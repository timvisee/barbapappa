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
        Schema::create('payment', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('state')->unsigned()->nullable(false);
            $table->integer('service_id')->unsigned()->nullable(true);
            $table->integer('user_id')->unsigned()->nullable(true);
            $table->morphs('paymentable');
            $table->string('reference', 12)->unique()->nullable(false);
            $table->decimal('money')->nullable(false);
            $table->integer('currency_id')->unsigned()->nullable(false);
            $table->timestamps();

            $table->foreign('service_id')
                ->references('id')
                ->on('service')
                ->onDelete('set null');
            $table->foreign('user_id')
                ->references('id')
                ->on('user')
                ->onDelete('set null');
            $table->foreign('currency_id')
                ->references('id')
                ->on('new_currency')
                ->onDelete('restrict');

            $table->index(['paymentable_id', 'paymentable_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('payment');
    }
}
