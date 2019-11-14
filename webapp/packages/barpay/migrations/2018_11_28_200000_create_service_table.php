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
        Schema::create('service', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('economy_id')->unsigned()->nullable(true);
            $table->morphs('serviceable');
            $table->boolean('enabled')->setNullable(false)->default(true);
            $table->integer('currency_id')->unsigned()->nullable(false);
            $table->boolean('deposit')->nullable(false);
            $table->boolean('withdraw')->nullable(false);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('economy_id')
                ->references('id')
                ->on('economy')
                ->onDelete('set null');
            $table->foreign('currency_id')
                ->references('id')
                ->on('currency')
                ->onDelete('restrict');

            $table->index(['serviceable_id', 'serviceable_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('service');
    }
}
