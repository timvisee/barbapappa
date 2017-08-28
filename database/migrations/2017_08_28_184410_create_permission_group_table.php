<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionGroupTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('permission_groups', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->boolean('enabled')->defaults(true);
            $table->int('community_id')->nullable();
            $table->int('user_id')->nullable();
            $table->int('inherit_permission_group_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('permission_groups');
    }
}
