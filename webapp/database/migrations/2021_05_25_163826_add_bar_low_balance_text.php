<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBarLowBalanceText extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('bar', function(Blueprint $table) {
            $table->string('low_balance_text', 2048)->after('password')->nullable(true)->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('bar', function(Blueprint $table) {
            $table->dropColumn('low_balance_text');
        });
    }
}
