<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('products', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('economy_id')->unsigned();
            $table->integer('user_id')->unsigned()->nullable(true);
            // TODO: is a type field used for custom products?
            $table->integer('type')->unsigned()->nullable(false);
            $table->string('name')->nullable(false);
            // TODO: category
            $table->boolean('enabled')->default(true)->nullable(false);
            $table->boolean('archived')->default(false)->nullable(false);
            $table->timestamps();

            $table->foreign('economy_id')
                ->references('id')
                ->on('economies')
                ->onDelete('restrict');
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
        Schema::dropIfExists('products');
    }
}
