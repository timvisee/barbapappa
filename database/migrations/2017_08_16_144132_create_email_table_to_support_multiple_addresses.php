<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailTableToSupportMultipleAddresses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create the email table
        Schema::create('emails', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('email');
            $table->timestamp('verified_at')->nullable();
            $table->ipAddress('verified_ip')->nullable();
            $table->timestamps();
        });

        // Remove the email field from users
        Schema::table('users', function($table) {
            $table->dropColumn('email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop the email table
        Schema::dropIfExists('emails');

        // Add the email field to the user
        Schema::table('users', function($table) {
            $table->string('email')->unique();
        });
    }
}
