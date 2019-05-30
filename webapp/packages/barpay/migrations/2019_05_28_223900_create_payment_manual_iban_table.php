<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentManualIbanTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('payment_manual_iban', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('payment_id')->unsigned()->nullable(false);

            // Target account details, from serviceable
            $table->string('to_account_holder')->nullable(false);
            $table->string('to_iban', 32)->nullable(false);
            $table->string('to_bic', 11)->nullable(true);

            // Source account details, from user
            $table->string('from_iban', 32)->nullable(false);

            // State
            $table->datetime('transferred_at')->nullable(true);
            $table->datetime('confirmed_at')->nullable(true);

            $table->timestamps();

            $table->foreign('payment_id')
                ->references('id')
                ->on('payments')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('payment_manual_iban');
    }
}
