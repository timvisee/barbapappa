<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationsPaymentSettledTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('notifications_payment_settled', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('payment_id')->unsigned();
            $table->timestamps();

            // TODO: cascade? should remove main notification type as well
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
        Schema::dropIfExists('notifications_payment_settled');
    }
}
