<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewCurrencyTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('new_currency', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('economy_id')->unsigned();
            $table->string('name');
            $table->string('code', 10)->nullable();
            $table->string('symbol', 25);
            $table->string('format', 50);
            $table->boolean('enabled')->default(true);
            $table->boolean('allow_wallet')->default(true);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('economy_id')
                ->references('id')
                ->on('economy')
                ->onDelete('cascade');

            $table->unique(['economy_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('new_currency');
    }
}
