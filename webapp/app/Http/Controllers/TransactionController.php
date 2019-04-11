<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Helpers\ValidationDefaults;
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
        $community = \Request::get('transaction');
        // TODO: use more specific query
        $transaction = Transaction::findOrFail($transactionId);
        $mutations = $transaction->mutations();

        return view('transaction.show')
            ->with('transaction', $transaction)
            ->with('mutations', $mutations->get());
    }
}
