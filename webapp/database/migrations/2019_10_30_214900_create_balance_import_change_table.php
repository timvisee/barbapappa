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
            $table->decimal('cost')->default('0.00')->nullable(false);
            $table->decimal('balance')->default('0.00')->nullable();
            $table->integer('currency_id')->unsigned();
            $table->integer('creator_id')->unsigned()->nullable();
            $table->integer('reviewer_id')->unsigned()->nullable();
            $table->integer('mutation_id')->unsigned()->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('committed_at')->nullable();
            $table->timestamps();

            $table->foreign('event_id')
                ->references('id')
                ->on('balance_import_event')
                ->onDelete('cascade');
            $table->foreign('alias_id')
                ->references('id')
                ->on('balance_import_alias')
                ->onDelete('cascade');
            $table->foreign('creator_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
            $table->foreign('reviewer_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
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
        Schema::dropIfExists('balance_import_change');
    }
}
