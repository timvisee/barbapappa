<?php

namespace App\Http\Controllers;

use App\Models\MutationProduct;
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

        // List products we can undo
        $products = self::fetchTransactionProducts($transaction);

        // Redirect back to the bar
        return view('transaction.undo')
            ->with('transaction', $transaction)
            ->with('products', $products)
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

        // Logic for undo with product selection
        // // List products we can undo
        // $products = self::fetchTransactionProducts($transaction);

        // // Prepare success response
        // $response_success = redirect()
        //     // TODO: redirect to better page!
        //     ->route('last')
        //     ->with('success', __('pages.transactions.undone'));

        // // If there are no products to select, undo full transaction
        // if($products->isEmpty()) {
        //     // Undo full transaction
        //     DB::transaction(function() use($transaction, $force) {
        //         $transaction->undo($force);
        //     });
        //     return $response_success;
        // }

        // // Build map of [product_id, quantity] to undo
        // $products_to_do = [];
        // $all_selected = true;
        // $none_selected = true;
        // foreach($products as $id => $item) {
        //     if(is_checked($request->input('product_' . $id))) {
        //         $products_to_do[$id] = ($products_to_do[$id] ?? 0) + $item['quantity'];
        //         $none_selected = false;
        //     } else
        //         $all_selected = false;
        // }

        // // Error if no products are selected
        // if($none_selected) {
        //     add_session_error('select_products', __('pages.transactions.noProductsSelected'));
        //     return redirect()->back()->with('success', null)->withInput();
        // }

        // // Undo full transaction if all products are selected
        // if($all_selected) {
        //     // Undo full transaction
        //     DB::transaction(function() use($transaction, $force) {
        //         $transaction->undo($force);
        //     });
        //     return $response_success;
        // }

        // // TODO: this logic is dirty, undo per single product instead
        // DB::transaction(function() use($transaction, &$products_to_do) {
        //     // List all product mutations
        //     $mutations = $transaction
        //         ->mutations()
        //         ->type(MutationProduct::class)
        //         ->get()
        //         ->map(function($mutation) {
        //             return [$mutation, $mutation->mutationable];
        //         });

        //     foreach($mutations as [$mutation_product, $mutationable_product]) {
        //         // Skip mutations we don't do anything with
        //         if(!isset($products_to_do[$mutationable_product->product_id]))
        //             continue;

        //         // TODO: this is causing trouble. Mutationable's are not
        //         // properly deleted along with their respective mutations.

        //         // Find related wallet mutation to update
        //         $mutation_wallet = $mutation_product->dependOn;
        //         $mutationable_wallet = $mutation_wallet->mutationable;
        //         if($mutation_wallet->mutationable_type != MutationWallet::class)
        //             throw new \Exception('Failed to undo part of transaction, could not find related wallet mutation');

        //         // Subtract from wallet mutation, delete if zero, update wallet balance
        //         $price = -$mutation_product->amount;
        //         $mutation_wallet->decrement('amount', $price);
        //         if($mutation_wallet->amount == 0)
        //             $mutation_wallet->delete();
        //         $mutationable_wallet->wallet->deposit($price);

        //         // Delete
        //         $mutation_product->undo(true, true);

        //         // Undo mutation and update to do quantity
        //         $products_to_do[$mutationable_product->product_id] -= $mutationable_product->quantity;

        //         // Remove product key from to to list if zero quantity left
        //         if($products_to_do[$mutationable_product->product_id] == 0)
        //             unset($products_to_do[$mutationable_product->product_id]);
        //     }

        //     // Assert we have no product to do left
        //     if(!empty($products_to_do))
        //         throw new \Exception('Failed to undo transaction, could not successfully undo all product transactoins');
        // });

        // return $response_success;
    }

    /**
     * Build list of products for transaction.
     *
     * [
     *     'product_id' => [
     *         'product' => Product,
     *         'quantity' => int,
     *     ],
     *     ...
     *  ]
     */
    private static function fetchTransactionProducts(Transaction $transaction) {
        $products = collect();
        $transaction
            ->mutations()
            ->type(MutationProduct::class)
            ->get()
            ->each(function($mutation) use(&$products) {
                $mut_product = $mutation->mutationable;
                $products[$mut_product->product_id] = [
                    'product' => $mut_product->product,
                    'quantity' => $mut_product->quantity + ($products[$mut_product->product_id]['quantity'] ?? 0),
                ];
            });

        return $products;
    }
}
