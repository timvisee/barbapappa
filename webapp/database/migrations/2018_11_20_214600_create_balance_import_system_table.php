<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBalanceImportSystemTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('balance_import_system', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('economy_id')->unsigned();
            $table->string('name', 255);
            $table->timestamps();

            $table->foreign('economy_id')
                ->references('id')
                ->on('economy')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('balance_import_system');
    }
}
