<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTransactionInitiatedAt extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('transaction', function(Blueprint $table) {
            $table->timestamp('initiated_at')->after('initiated_by_kiosk')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('transaction', function(Blueprint $table) {
            $table->dropColumn('initiated_at');
        });
    }
}
