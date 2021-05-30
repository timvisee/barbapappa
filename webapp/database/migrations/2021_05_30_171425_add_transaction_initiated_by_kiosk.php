<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTransactionInitiatedByKiosk extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('transaction', function(Blueprint $table) {
            $table->boolean('initiated_by_kiosk')->after('initiated_by_other')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('transaction', function(Blueprint $table) {
            $table->dropColumn('initiated_by_kiosk');
        });
    }
}
