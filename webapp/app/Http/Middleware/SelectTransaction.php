<?php

namespace App\Http\Middleware;

use App\Models\Transaction;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class SelectTransaction.
 *
 * Middleware that allows selecting a transaction through an URL parameter.
 *
 * @package App\Http\Middleware
 */
class SelectTransaction {

    /**
     * Handle an incoming request.
     *
     * @param Request $request Request.
     * @param \Closure $next Next callback.
     *
     * @return Response Response.
     */
    public function handle($request, Closure $next) {
        // Get the selected transaction
        $transaction = $request->route('transactionId');
        if($transaction != null)
            $transaction = Transaction::findOrFail($transaction);
        if($transaction == null)
            return response(view('noPermission'));

        // The user must have permission to view the transaction
        if(!$transaction->hasViewPermission())
            return response(view('noPermission'));

        // Make selected transaction available in the request and views
        $request->attributes->add(['transaction' => $transaction]);
        view()->share('transaction', $transaction);

        // Continue
        return $next($request);
    }
}
