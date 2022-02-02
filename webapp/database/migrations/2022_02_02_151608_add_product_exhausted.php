<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProductExhausted extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('product', function(Blueprint $table) {
            // Defines whether a product is considered 'exhausted' based on it's
            // inventory. This property is periodically updated, and may be
            // incorrect. It can be used for better sorting however.
            // If no inventory is used this defaults to false.
            $table->boolean('exhausted')->after('tags')->default(false)->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('product', function(Blueprint $table) {
            $table->dropColumn('exhausted');
        });
    }
}
