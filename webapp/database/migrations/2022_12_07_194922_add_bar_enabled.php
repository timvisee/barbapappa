<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBarEnabled extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('bar', function(Blueprint $table) {
            $table->boolean('enabled')->after('description')->default(true)->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('bar', function(Blueprint $table) {
            $table->dropColumn('enabled');
        });
    }
}
