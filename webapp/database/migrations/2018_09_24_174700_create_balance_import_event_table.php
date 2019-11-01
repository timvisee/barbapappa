<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBalanceImportEventTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('balance_import_event', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('system_id')->unsigned();
            $table->string('name', 255);
            $table->timestamps();

            $table->foreign('system_id')
                ->references('id')
                ->on('balance_import_system')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('balance_import_event');
    }
}
