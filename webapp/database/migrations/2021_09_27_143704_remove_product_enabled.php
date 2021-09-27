<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RemoveProductEnabled extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        // Trash products that are currently disabled, this cannot be rolled
        // back, but is considered fine
        DB::transaction(function() {
            // Manually trashing by setting timestamp, not using delete() to
            // prevent accidentally deletes if SoftDeletes is ever removed from
            // the model
            Product::where('enabled', false)
                ->update(['deleted_at' => now()]);
        });

        Schema::table('product', function(Blueprint $table) {
            $table->dropColumn('enabled');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('product', function(Blueprint $table) {
            $table->boolean('enabled')->after('tags')->default(true)->nullable(false);
        });
    }
}
