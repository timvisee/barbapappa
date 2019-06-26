<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentBunqMeTabTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('payment_bunqme_tab', function(Blueprint $table) {
            $table->increments('id')->unsigned();

            // The BunqMe Tab ID and share URL
            $table->bigInteger('bunq_tab_id')->nullable(true)->unique();
            $table->string('bunq_tab_url')->nullable(true);

            // State
            $table->datetime('transferred_at')->nullable(true);
            $table->datetime('settled_at')->nullable(true);

            $table->timestamps();

            // TODO: require to cancel bunqme tab at bunq first before delete?
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
