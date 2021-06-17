<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeBunqAccountEnabledFields extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('bunq_account', function(Blueprint $table) {
            $table->boolean('enable_checks')->after('enabled')->default(true)->nullable(false);
            $table->renameColumn('enabled', 'enable_payments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('bunq_account', function(Blueprint $table) {
            $table->dropColumn('enable_checks');
            $table->renameColumn('enable_payments', 'enabled');
        });
    }
}
