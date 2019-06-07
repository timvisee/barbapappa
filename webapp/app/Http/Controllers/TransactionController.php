<?php

namespace App\Http\Controllers;

use App\Helpers\ValidationDefaults;
use App\Models\Mutation;
use App\Models\MutationWallet;
use App\Models\Transaction;
use App\Perms\Builder\Config as PermsConfig;
use App\Perms\CommunityRoles;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Validator;

class TransactionController extends Controller {

    /**
     * Show an user transaction.
     *
     * @return Response
     */
    public function show($transactionId) {
        // Get the transaction
        $transaction = Transaction::findOrFail($transactionId);

        // Check permission
        // TODO: check this permission in middleware, redirect to login
        if(!Self::hasPermission($transaction))
            return response(view('noPermission'));

        // Get the related mutations, partition into from/to
        $mutations = $transaction->mutations()->get();
        list($fromMutations, $toMutations) = $mutations->partition(function($m) {
            return $m->amount > 0;
        });

        return view('transaction.show')
            ->with('transaction', $transaction)
            ->with('fromMutations', $fromMutations)
            ->with('toMutations', $toMutations);
    }

    /**
     * Undo a user transaction.
     *
     * @return Response
     */
    public function undo($transactionId) {
        // Get the transaction
        $transaction = Transaction::findOrFail($transactionId);

        // Make sure we can undo
        if(!$transaction->canUndo())
            // Redirect back to the transaction details page
            return redirect()
                ->route('transaction.show', ['transactionId' => $transactionId])
                ->with('error', __('pages.transactions.cannotUndo'));

        // Redirect back to the bar
        return view('transaction.undo')
            ->with('transaction', $transaction);
    }

    /**
     * Do undo the given transaction.
     *
     * @return Response
     */
    public function doUndo($transactionId) {
        // Get the transaction
        $transaction = Transaction::findOrFail($transactionId);

        // Make sure we can undo
        if(!$transaction->canUndo())
            // Redirect back to the transaction details page
            return redirect()
                ->route('transaction.show', ['transactionId' => $transactionId])
                ->with('error', __('pages.transactions.cannotUndo'));

        // Undo the transaction
        DB::transaction(function() use($transaction) {
            $transaction->undo();
        });

        // Redirect back to the bar
        return redirect()
            // TODO: redirect to better page!
            ->route('last')
            ->with('success', __('pages.transactions.undone'));
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
            ->where('mutationable_type', MutationWallet::class)
            ->get();

        // If the user owns any of the wallets, allow
        foreach($mutations as $mutation)
            if($mutation->mutationable->wallet->user_id == $user->id)
                return true;

        // Allow permission if user is admin or community manager
        // TODO: give current request as second parameter as well
        // TODO: we must somehow give a reference to current community
        return perms(CommunityRoles::presetManager());
    }
}
