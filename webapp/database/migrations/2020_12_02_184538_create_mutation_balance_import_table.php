<?php

use App\Models\BalanceImportChange;
use App\Models\Mutation;
use App\Models\MutationBalanceImport;
use App\Models\MutationMagic;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateMutationBalanceImportTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('mutation_balance_import', function(Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->integer('balance_import_change_id')->unsigned()->nullable(true);
            $table->timestamps();

            $table->foreign('balance_import_change_id')
                ->references('id')
                ->on('balance_import_change')
                ->onDelete('set null');
        });

        DB::transaction(function() {
            $changes = BalanceImportChange::whereNotNull('mutation_id')->get();
            foreach($changes as $change) {
                $mutation = Mutation::findOrFail($change->mutation_id);

                // Get existing magic mutationable
                $mut_magic = $mutation->mutationable();

                // Create new balance import mutationable to replace
                $mut_import = MutationBalanceImport::create([
                    'balance_import_change_id' => $change->id,
                ]);
                $mutation->setMutationable($mut_import, true, true);

                // Delete old magic mutationable
                $mut_magic->delete();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        DB::transaction(function() {
            $changes = BalanceImportChange::whereNotNull('mutation_id')->get();
            foreach($changes as $change) {
                $mutation = Mutation::findOrFail($change->mutation_id);

                // Get existing balance import mutationable
                $mut_import = $mutation->mutationable();

                // Create new magic mutationable to replace
                $mutation->setMutationable(MutationMagic::create(), true, true);

                // Delete old balance import mutationable
                $mut_import->delete();
            }
        });

        Schema::dropIfExists('mutation_balance_import');
    }
}
