<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBalanceImportChangeTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('balance_import_change', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('event_id')->unsigned();
            $table->integer('alias_id')->unsigned();
            $table->decimal('balance')->default('0.00')->nullable();
            $table->decimal('cost')->default('0.00')->nullable();
            $table->integer('currency_id')->unsigned();
            $table->integer('submitter_id')->unsigned()->nullable();
            $table->integer('accepter_id')->unsigned()->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->integer('mutation_id')->unsigned()->nullable();
            $table->timestamp('committed_at')->nullable();
            $table->timestamps();

            $table->foreign('event_id')
                ->references('id')
                ->on('balance_import_event')
                ->onDelete('restrict');
            $table->foreign('alias_id')
                ->references('id')
                ->on('balance_import_alias')
                ->onDelete('restrict');
            $table->foreign('submitter_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
            $table->foreign('accepter_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            // Foreign key to mutation is created in mutation migration
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('balance_import_change');
    }
}
