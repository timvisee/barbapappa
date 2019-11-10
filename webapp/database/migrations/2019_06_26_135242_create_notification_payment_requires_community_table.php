<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationPaymentRequiresCommunityTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('notification_payment_requires_community', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('payment_id')->unsigned();
            $table->timestamps();

            // TODO: cascade? should remove main notification type as well
            $table->foreign('payment_id')
                ->references('id')
                ->on('payment')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('notification_payment_requires_community');
    }
}
