<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServiceManualIbanTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('service_manual_iban', function(Blueprint $table) {
            $table->increments('id')->unsigned();

            // Target account, account holder name, IBAN and optional BIC
            $table->string('account_holder')->nullable(false);
            $table->string('iban', 32)->nullable(false);
            $table->string('bic', 11)->nullable(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('service_manual_iban');
    }
}
