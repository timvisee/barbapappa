<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller {

    /**
     * Show an user transaction.
     *
     * @return Response
     */
    public function show($transactionId) {
        // Get the transaction
        $transaction = Transaction::findOrFail($transactionId);

        // Get the related mutations, partition into from/to
        $mutations = $transaction->mutations()->get();
        list($fromMutations, $toMutations) = $mutations->partition(function($m) {
            return $m->amount > 0;
        });

        // Fetch related objects
        $related = $transaction->getRelatedObjects()
            ->groupBy(function($item) {
                return get_class($item);
            });

        return view('transaction.show')
            ->with('transaction', $transaction)
            ->with('realtedObjects', $related)
            ->with('fromMutations', $fromMutations)
            ->with('toMutations', $toMutations);
    }

    /**
     * Undo a user transaction.
     *
     * @return Response
     */
    public function undo(Request $request, $transactionId) {
        // Get the transaction and force mode
        $transaction = Transaction::findOrFail($transactionId);
        $force = is_checked($request->query('force'));

        // Make sure we can undo
        if(!$transaction->canUndo($force))
            // Redirect back to the transaction details page
            return redirect()
                ->route('transaction.show', ['transactionId' => $transactionId])
                ->with('error', __('pages.transactions.cannotUndo'));

        // Redirect back to the bar
        return view('transaction.undo')
            ->with('transaction', $transaction)
            ->with('force', $force);
    }

    /**
     * Do undo the given transaction.
     *
     * @return Response
     */
    public function doUndo(Request $request, $transactionId) {
        // Get the transaction and force mode
        $transaction = Transaction::findOrFail($transactionId);
        $force = is_checked($request->query('force'));

        // Make sure we can undo
        if(!$transaction->canUndo($force))
            // Redirect back to the transaction details page
            return redirect()
                ->route('transaction.show', ['transactionId' => $transactionId])
                ->with('error', __('pages.transactions.cannotUndo'));

        // Undo the transaction
        DB::transaction(function() use($transaction, $force) {
            $transaction->undo($force);
        });

        // Redirect back to the bar
        return redirect()
            // TODO: redirect to better page!
            ->route('last')
            ->with('success', __('pages.transactions.undone'));
    }
}
