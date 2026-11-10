<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateBunqAccountApiContextLength extends Migration {

    /**
     * Run the migrations.
     *
     * The encrypted bunq API context may exceed the 64KB limit of a TEXT
     * column, grow it to a MEDIUMTEXT column (16MB).
     *
     * @return void
     */
    public function up() {
        Schema::table('bunq_account', function (Blueprint $table) {
            $table->mediumText('api_context_encrypted')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('bunq_account', function (Blueprint $table) {
            $table->text('api_context_encrypted')->change();
        });
    }
}
