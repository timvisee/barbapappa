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
        // Get the user, community, find the economy and mutation
        $user = barauth()->getUser();
        $transaction = Transaction::findOrFail($transactionId);

        // Check permission
        // TODO: check this permission in middleware
        if(!Self::hasPermission($transaction))
            return response(view('noPermission'));

        // Get the selected mutation
        $mutation = $transaction->mutations()->findOrFail($mutationId);

        return view('transaction.mutation.show')
            ->with('transaction', $transaction)
            ->with('mutation', $mutation);
    }

    /**
     * Check whether the currently authenticated user has permission to view the
     * given mutation. This is determined in transaction scope.
     *
     * @param Transaction $transaction The transaction model this mutation is
     * part of.
     *
     * @return boolean True if the user can view this transaction/mutation,
     *      false if not.
     */
    static function hasPermission(Transaction $transaction) {
        return TransactionController::hasPermission($transaction);
    }
}
