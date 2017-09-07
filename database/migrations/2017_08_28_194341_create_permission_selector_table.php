<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionGroupUserSelectorTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('permission_selectors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->int('permission_group_id');
            $table->boolean('is_authenticated')->nullable();
            $table->boolean('is_verified')->nullable();
            $table->boolean('is_community')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('permission_selectors');
    }
}
