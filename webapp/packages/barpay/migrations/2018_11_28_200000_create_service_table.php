<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServiceTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('services', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('economy_id')->unsigned()->nullable(true);
            $table->morphs('serviceable');
            $table->decimal('deposit_min')->setNullable(false);
            $table->decimal('deposit_max')->setNullable(false);
            $table->decimal('withdraw_min')->setNullable(false);
            $table->decimal('withdraw_max')->setNullable(false);
            $table->boolean('enabled')->setNullable(false)->default(true);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('economy_id')
                ->references('id')
                ->on('economies')
                ->onDelete('set null');

            // TODO: add a field for supported currency/currencies by this service
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('services');
    }
}
