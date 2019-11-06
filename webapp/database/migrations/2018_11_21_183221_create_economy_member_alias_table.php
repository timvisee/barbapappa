<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEconomyMemberAliasTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('economy_member_alias', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('member_id')->unsigned();
            $table->integer('alias_id')->unsigned();
            $table->timestamps();

            $table->foreign('member_id')
                ->references('id')
                ->on('economy_member')
                ->onDelete('cascade');
            $table->foreign('alias_id')
                ->references('id')
                ->on('balance_import_alias')
                ->onDelete('restrict');

            $table->unique(['member_id', 'alias_id']);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('economy_member_alias');
    }
}
