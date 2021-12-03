<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserMailReceipt extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('user', function(Blueprint $table) {
            $table
                ->boolean('mail_receipt')
                ->after('notify_low_balance')
                ->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('user', function(Blueprint $table) {
            $table->dropColumn('mail_receipt');
        });
    }
}
