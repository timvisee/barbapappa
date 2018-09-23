<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailVerificationTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('email_verifications', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('email_id')->unsigned();
            $table->string('token')->unique();
            $table->timestamp('expire_at');
            $table->timestamps();

            $table->foreign('email_id')
                ->references('id')
                ->on('emails')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('email_verifications');
    }
}
