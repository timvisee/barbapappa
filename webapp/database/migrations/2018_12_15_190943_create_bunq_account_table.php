<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBunqAccountTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('bunq_accounts', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('community_id')->unsigned()->nullable(true);
            $table->boolean('enabled')->default(true)->nullable(false);
            $table->string('name')->nullable(false);
            $table->text('api_context_encrypted');
            $table->bigInteger('monetary_account_id');
            $table->string('account_holder');
            $table->string('iban', 32);
            $table->string('bic', 8)->nullable(true);
            $table->softDeletes();
            $table->timestamps();

            // TODO: include an expiry time, which sets token to null?

            $table->foreign('community_id')
                ->references('id')
                ->on('communities')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('bunq_accounts');
    }
}
