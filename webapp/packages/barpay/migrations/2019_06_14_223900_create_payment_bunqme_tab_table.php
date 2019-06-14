<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentBunqmeTabTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('payment_bunqme_tab', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('payment_id')->unsigned()->nullable(false);

            // The BunqMe Tab ID
            $table->bigInteger('bunq_tab_id')->nullable(true);

            // State
            $table->datetime('transferred_at')->nullable(true);
            $table->datetime('settled_at')->nullable(true);

            $table->timestamps();

            // TODO: should we cascade? We might need to cancel bunqme tab
            // payment first
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
        Schema::dropIfExists('payment_bunqme_tab');
    }
}
