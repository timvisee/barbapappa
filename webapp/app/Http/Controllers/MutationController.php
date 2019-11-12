<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Helpers\ValidationDefaults;
use App\Models\Transaction;
use App\Perms\Builder\Config as PermsConfig;
use App\Perms\CommunityRoles;

class MutationController extends Controller {

    /**
     * Show an transaction mutation.
     *
     * @return Response
     */
    public function show($transactionId, $mutationId) {
        // Get the transaction, find the mutation
        $transaction = Transaction::findOrFail($transactionId);
        $mutation = $transaction->mutations()->findOrFail($mutationId);

        return view('transaction.mutation.show')
            ->with('transaction', $transaction)
            ->with('mutation', $mutation);
    }
}
