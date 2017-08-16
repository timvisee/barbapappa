<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserAddFullName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function($table) {
            // Add the name fields
            $table->string('first_name');
            $table->string('last_name');

            // Drop the full name field
            $table->dropColumn('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function($table) {
            // Drop the name fields
            $table->dropColumn('first_name');
            $table->dropColumn('last_name');

            // Add the full name field
            $table->string('name');
        });
    }
}
