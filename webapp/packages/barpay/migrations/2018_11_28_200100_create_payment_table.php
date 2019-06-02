<?php

use BarApp\Models\Payment;
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
            $table->integer('state')->unsigned()->nullable(false)->defaults(Payment::STATE_INIT);
            $table->integer('service_id')->unsigned()->nullable(true);
            $table->morphs('paymentable');
            $table->string('reference', 32)->unique()->nullable(true);
            $table->decimal('money')->nullable(false);
            $table->integer('currency_id')->unsigned()->nullable(false);
            $table->timestamps();

            $table->foreign('service_id')
                ->references('id')
                ->on('services')
                ->onDelete('set null');
            $table->foreign('currency_id')
                ->references('id')
                ->on('currencies')
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
        Schema::dropIfExists('payments');
    }
}
