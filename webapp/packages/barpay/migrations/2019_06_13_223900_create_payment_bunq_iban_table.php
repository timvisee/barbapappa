<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentBunqIbanTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('payment_bunq_iban', function(Blueprint $table) {
            $table->increments('id')->unsigned();

            // Source account details, from user
            $table->string('from_iban', 32)->nullable(true);

            // State
            $table->datetime('transferred_at')->nullable(true);
            $table->datetime('settled_at')->nullable(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('payment_bunq_iban');
    }
}
