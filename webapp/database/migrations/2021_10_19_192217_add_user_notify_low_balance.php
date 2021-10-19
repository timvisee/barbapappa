<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserNotifyLowBalance extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('user', function(Blueprint $table) {
            $table
                ->boolean('notify_low_balance')
                ->after('locale')
                ->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('user', function(Blueprint $table) {
            $table->dropColumn('notify_low_balance');
        });
    }
}
