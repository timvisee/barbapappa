<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

use App\Mail\Password\Reset;
use App\Managers\PasswordResetManager;
use App\Utils\EmailRecipient;

/**
 * Economy model.
 *
 * @property int id
 * @property int community_id
 * @property string name
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Economy extends Model {

    protected $fillable = ['name'];

    /**
     * Get the community this economy is part of.
     *
     * @return The community.
     */
    public function community() {
        return $this->belongsTo('App\Models\Community');
    }

    /**
     * Get the bars that use this economy.
     *
     * @return The bars.
     */
    public function bars() {
        return $this->hasMany('App\Models\Bar');
    }

    /**
     * Get a list of economy currencies.
     *
     * @return List of supported currencies.
     */
    public function currencies() {
        // TODO: eager load by default, in the economy currency class itself
        return $this->hasMany('App\Models\EconomyCurrency')->with('currency');
    }

    /**
     * Get a relation to all the products that are part of this economy.
     *
     * @return The products.
     */
    public function products() {
        // TODO: `with()` names and prices
        return $this->hasMany(Product::class);
    }

    /**
     * Get the wallets created by users in this economy.
     *
     * @return The wallets.
     */
    public function wallets() {
        return $this->hasMany('App\Models\Wallet');
    }

    /**
     * Get the wallets created by the current logged in user in this economy.
     *
     * @return The wallets.
     */
    public function userWallets() {
        return $this->wallets()->where('user_id', barauth()->getUser()->id);
    }

    /**
     * Get all the transactions that took place in this economy.
     *
     * TODO: filter by user
     *
     * @return The transactions.
     */
    public function transactions() {
        // TODO: return proper selection
        // return $this->hasMany('App\Models\Transaction');
        return Transaction::where('id', '!=', -1);
    }

    /**
     * Get the last few transactions that took place in this economy.
     *
     * TODO: filter by user
     *
     * @param [$limit=5] The number of last transactions to return at max.
     *
     * @return The last transactions.
     */
    public function lastTransactions($limit = 5) {
        return $this
            ->transactions()
            ->orderBy('created_at', 'DESC')
            ->limit($limit);
    }

    /**
     * Go through all wallets of the current user in this economy, and calculate
     * the total balance.
     *
     * This method automatically selects the best currency to return in, and
     * notes whether the returned value is approximate or not. Balances in other
     * currencies are automatically converted using the latest known exchange
     * rates from the currencies table. The method also notes whether the
     * returned value is approximate, which is true when multiple currencies
     * ware summed.
     *
     * If no wallet is created, zero is returned in the default currency.
     *
     * Example return:
     * ```php
     * [1.23, 'EUR', true] // 1.23 euro, approximately
     * ```
     *
     * @return [$balance, $currency, $approximate] The balance, chosen currency
     *      code and whether the value is approximate.
     */
    public function calcBalance() {
        // Obtain the wallets, return zero with default currency if none
        $wallets = $this->userWallets()->with('currency')->get();
        if($wallets->isEmpty())
            return [0, config('currency.default'), false];

        // Build a map with per currency sums
        $sums = [];
        foreach($wallets as $wallet) {
            $currency = $wallet->currency->code;
            $sums[$wallet->currency->code] = ($sums[$wallet->currency->code] ?? 0) + $wallet->balance;
        }

        // Find the currency with the biggest difference from zero, is it approx
        $currency = null;
        $diff = 0;
        foreach($sums as $c => $b)
            if(abs($b) > $diff) {
                $currency = $c;
                $diff = abs($b);
            }
        $approximate = count($sums) > 1;

        // Sum the balance, convert other currencies
        $balance = collect($sums)
            ->map(function($b, $c) use($currency) {
                return $currency == $c ? $b : currency($b, $c, $currency, false);
            })
            ->sum();

        return [$balance, $currency, $approximate];
    }

    /**
     * Calcualte and format the total balance for all the wallets in this
     * economy for the current user. See `$this->calcBalance()`.
     *
     * @param boolean [$format=BALANCE_FORMAT_PLAIN] The balance formatting type.
     *
     * @return string Formatted balance
     */
    public function formatBalance($format = BALANCE_FORMAT_PLAIN) {
        // Obtain balance information
        $out = $this->calcBalance();
        $balance = $out[0];
        $currency = $out[1];
        $prefix = $out[2] ? '&asymp; ' : '';

        // Format the balance
        return balance($balance, $currency, $format, $prefix);
    }
}
