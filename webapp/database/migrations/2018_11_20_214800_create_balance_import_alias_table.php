<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBalanceImportAliasTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('balance_import_alias', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('economy_id')->unsigned();
            $table->integer('user_id')->unsigned()->nullable();
            $table->string('name', 255)->nullable();
            $table->string('email', 255);
            $table->timestamps();

            $table->foreign('economy_id')
                ->references('id')
                ->on('economies')
                ->onDelete('cascade');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('balance_import_alias');
    }
}
