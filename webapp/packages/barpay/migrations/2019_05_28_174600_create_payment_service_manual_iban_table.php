<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentServiceManualIbanTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('payment_service_manual_iban', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('payment_service_id')->unsigned()->nullable(false);

            // Target account, account holder name, IBAN and optional BIC
            $table->string('account_holder')->nullable(false);
            $table->string('iban', 32)->nullable(false);
            $table->string('bic', 8)->nullable(true);

            $table->timestamps();

            $table->foreign('payment_service_id')
                ->references('id')
                ->on('payment_services')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('payment_service_manual_iban');
    }
}
