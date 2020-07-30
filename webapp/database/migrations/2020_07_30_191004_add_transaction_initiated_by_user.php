<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTransactionInitiatedByUser extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('transaction', function(Blueprint $table) {
            $table->integer('initiated_by_id')->unsigned()->after('owner_id')->nullable(true);
            $table->boolean('initiated_by_other')->after('initiated_by_id')->default(0);

            $table->foreign('initiated_by_id')
                ->references('id')
                ->on('user')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('transaction', function(Blueprint $table) {
            $table->dropForeign(['initiated_by_id']);

            $table->dropColumn('initiated_by_id');
            $table->dropColumn('initiated_by_other');
        });
    }
}
