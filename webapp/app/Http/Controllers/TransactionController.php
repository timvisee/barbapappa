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
        // TODO: do some permission checking! user allowed to see transaction?

        // Get the user, community, find the economy and transaction
        $user = barauth()->getUser();
        // TODO: use more specific query
        $transaction = Transaction::findOrFail($transactionId);
        $mutations = $transaction->mutations();

        // Check permission
        if(!$this->hasPermission($transaction))
            return response(view('noPermission'));

        return view('transaction.show')
            ->with('transaction', $transaction)
            ->with('mutations', $mutations->get());
    }

    /**
     * Check whether the currently authenticated user has permission to view the
     * given transaction.
     *
     * @param Transaction $transaction The transaction model.
     *
     * @return boolean True if the user can view this transaction, false if not.
     */
    function hasPermission(Transaction $transaction) {
        // The user must be authenticated
        $barauth = barauth();
        if(!$barauth->isAuth())
            return false;
        $user = $barauth->getUser();

        // If the current user initiated the transaction, it's all right
        if($transaction->created_by == $user->id)
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
