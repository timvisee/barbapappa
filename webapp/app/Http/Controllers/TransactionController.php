<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Helpers\ValidationDefaults;
use App\Models\Mutation;
use App\Models\Transaction;
use App\Perms\Builder\Config as PermsConfig;
use App\Perms\CommunityRoles;

class TransactionController extends Controller {

    /**
     * Show an user transaction.
     *
     * @return Response
     */
    public function show($transactionId) {
        // Get the user, community, find the economy and transaction
        $user = barauth()->getUser();
        $transaction = Transaction::findOrFail($transactionId);

        // Check permission
        // TODO: check this permission in middleware, redirect to login
        if(!Self::hasPermission($transaction))
            return response(view('noPermission'));

        // Get the related mutations, partition into from/to
        $mutations = $transaction->mutations()->get();
        list($fromMutations, $toMutations) = $mutations->partition(function($m) {
            return $m->amount >= 0;
        });

        return view('transaction.show')
            ->with('transaction', $transaction)
            ->with('fromMutations', $fromMutations)
            ->with('toMutations', $toMutations);
    }

    /**
     * Check whether the currently authenticated user has permission to view the
     * given transaction.
     *
     * @param Transaction $transaction The transaction model.
     *
     * @return boolean True if the user can view this transaction, false if not.
     */
    static function hasPermission(Transaction $transaction) {
        // The user must be authenticated
        $barauth = barauth();
        if(!$barauth->isAuth())
            return false;
        $user = $barauth->getUser();

        // If the current user initiated the transaction, it's all right
        if($transaction->owner_id == $user->id)
            return true;

        // Get all wallet mutations
        $mutations = $transaction
            ->mutations()
            ->where('type', Mutation::TYPE_WALLET)
            ->get();

        // If the user owns any of the wallets, allow
        foreach($mutations as $mutation)
            if($mutation->mutationData->wallet->user_id == $user->id)
                return true;

        // TODO: allow management users in this economy

        // Could not find link between transaction and authenticated user
        return false;
    }
}
