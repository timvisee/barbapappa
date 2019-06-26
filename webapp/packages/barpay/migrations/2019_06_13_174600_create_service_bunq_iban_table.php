<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServiceBunqIbanTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('service_bunq_iban', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('bunq_account_id')->unsigned()->nullable(false);

            // Target account, account holder name, IBAN and optional BIC
            $table->string('account_holder')->nullable(false);
            $table->string('iban', 32)->nullable(false);
            $table->string('bic', 11)->nullable(true);
            $table->timestamps();

            $table->foreign('bunq_account_id')
                ->references('id')
                ->on('bunq_accounts')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('service_bunq_iban');
    }
}
