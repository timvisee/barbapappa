<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBunqAccountCheckedAt extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('bunq_account', function(Blueprint $table) {
            $table->timestamp('checked_at')->after('last_event_id')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('bunq_account', function(Blueprint $table) {
            $table->dropColumn('checked_at');
        });
    }
}
