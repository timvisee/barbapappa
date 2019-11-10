<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMutationTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('mutations', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('transaction_id')->unsigned();
            $table->integer('economy_id')->unsigned()->nullable(true);
            $table->morphs('mutationable');
            $table->decimal('amount')->nullable(false);
            $table->integer('currency_id')->unsigned();
            $table->integer('state')->unsigned()->nullable(false)->default(1);
            $table->integer('depend_on')->unsigned()->nullable(true);
            $table->integer('owner_id')->unsigned()->nullable(true);
            $table->timestamps();

            $table->foreign('transaction_id')
                ->references('id')
                ->on('transactions')
                ->onDelete('cascade');
            $table->foreign('economy_id')
                ->references('id')
                ->on('economies')
                ->onDelete('set null');
            $table->foreign('currency_id')
                ->references('id')
                ->on('currencies')
                ->onDelete('restrict');
            $table->foreign('depend_on')
                ->references('id')
                ->on('mutations')
                ->onDelete('set null');
            $table->foreign('owner_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });

        // Link balance import changes to mutations
        Schema::table('balance_import_change', function (Blueprint $table) {
            $table->foreign('mutation_id')
                ->references('id')
                ->on('mutations')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        // Linked balance import changes to mutations
        Schema::table('balance_import_change', function (Blueprint $table) {
            $table->dropForeign(['mutation_id']);
        });

        Schema::dropIfExists('mutations');
    }
}
