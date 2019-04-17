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
        // TODO: do some permission checking! user allowed to see mutation?

        // Get the user, community, find the economy and mutation
        $user = barauth()->getUser();
        // TODO: use more specific query
        $transaction = Transaction::findOrFail($transactionId);
        $mutation = $transaction->mutations()->findOrFail($mutationId);

        return view('transaction.mutation.show')
            ->with('transaction', $transaction)
            ->with('mutation', $mutation);
    }
}
