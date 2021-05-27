<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKioskSessionTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('kiosk_session', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('bar_id')->unsigned();
            $table->string('token')->index();
            $table->integer('user_id')->unsigned();
            $table->ipAddress('created_ip');
            $table->timestamp('expire_at')->nullable();
            $table->timestamps();

            $table->foreign('bar_id')
                ->references('id')
                ->on('bar')
                ->onDelete('cascade');
            $table->foreign('user_id')
                ->references('id')
                ->on('user')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('kiosk_session');
    }
}
