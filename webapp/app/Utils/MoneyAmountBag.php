<?php

namespace App\Utils;

use App\Models\Currency;

/**
 * Class MoneyAmountBag.
 *
 * A list of money amounts, to easily manage money in multiple currencies.
 *
 * @package App\Utils
 */
class MoneyAmountBag {

    /**
     * The amounts.
     * @var [MoneyAmount]
     */
    private $amounts;

    /**
     * Constructor.
     *
     * @param array[MoneyAmount] $amounts Money amounts.
     */
    public function __construct($amounts = []) {
        $this->amounts = collect();

        $amounts = collect($amounts ?? []);
        foreach($amounts as $amount)
            $this->add($amount);
    }

    /**
     * Add the given amount.
     *
     * @param MoneyAmount $amount
     */
    public function add(MoneyAmount $amount) {
        $this->set(
            $this->getOrZero($amount->currency)->add($amount),
        );
    }

    /**
     * Subtract the given amount.
     *
     * @param MoneyAmount $amount
     */
    public function sub(MoneyAmount $amount) {
        $this->set(
            $this->getOrZero($amount->currency)->add($amount),
        );
    }

    /**
     * Get the amount for a specific currency.
     *
     * @param Currency $currency
     * @return ?MoneyAmount The amount or null.
     */
    public function get(Currency $currency): ?MoneyAmount {
        // TODO: use first where instead?
        return $this->amounts->first(function($amount) use($currency) {
            return $amount->currency == $currency;
        });
    }

    /**
     * Get the amount for a specific currency, get a zero amount if it does not
     * exist.
     *
     * @param Currency $currency
     * @return MoneyAmount The amount.
     */
    public function getOrZero(Currency $currency): MoneyAmount {
        return $this->get($currency) ?? MoneyAmount::zero($currency);
    }

    /**
     * Get the amount for a specific currency.
     *
     * If the amount is zero, it will be removed from the list.
     *
     * @param Currency $currency
     * @return ?MoneyAmount The amount or null.
     */
    public function set(MoneyAmount $amount) {
        $this->remove($amount->currency);
        $this->amounts->push($amount);
    }

    /**
     * Remove a money amount with the given currency if it exists.
     *
     * @param Currency $currency
     */
    public function remove(Currency $currency) {
        $this->amounts = $this->amounts->reject(function($amount) use($currency) {
            return $amount->currency == $currency;
        });
    }

    /**
     * The money amounts.
     *
     * @return object Money amounts collection.
     */
    public function amounts() {
        return $this->amounts;
    }

    /**
     * Check whether the amount is zero.
     *
     * @return bool True if zero, false if not.
     */
    public function isZero(): bool {
        return $this->amounts->every(function($amount) {
            return $amount->isZero();
        });
    }

    /**
     * Whether the summed amount is considered approximate.
     *
     * Will always be approximate if we have amounts in multiple currencies.
     */
    public function isApproximate(): bool {
        return $this->amounts->count() > 1 || $this->amounts->contains(function($amount) {
            return $amount->approximate;
        });
    }

    /**
     * Go through the given list money amounts, and sum all money amounts in a
     * shared currency.
     *
     * This method automatically selects the best currency to return in, and
     * notes whether the returned value is approximate or not. Balances in other
     * currencies are automatically converted using the latest known exchange
     * rates from the currencies table. The method also notes whether the
     * returned value is approximate, which is true when multiple currencies
     * ware summed.
     *
     * @return MoneyAmount The summed amount.
     */
    public function sumAmounts() {
        // Return null if empty or first if zero or one item
        if($this->amounts->isEmpty())
            return null;
        if($this->amounts->count() == 1 || $this->isZero())
            return $this->amounts->first();

        // Build a map with per currency sums
        $sums = [];
        foreach($this->amounts as $amount) {
            $code = $amount->currency->code;
            $sums[$code] = ($sums[$code] ?? 0) + $amount->amount;
        }

        // Find the currency with the biggest difference from zero, is it approx
        $code = key($sums);
        $diff = 0;
        foreach($sums as $c => $b)
            if(abs($b) > $diff) {
                $code = $c;
                $diff = abs($b);
            }
        $approximate = count($sums) > 1;

        // Sum the balance, convert other currencies
        $balance = collect($sums)
            ->map(function($b, $c) use($code) {
                if($code == $c)
                    return $b;

                // Convert currencies in a different balance
                // TODO: convert currencies
                // throw new Exception('Unable to convert currency here, not yet implemented');
                // return currency($b, $c, $code, false);
                return $b;
            })
            ->sum();

        // TODO: throw exception if got null code

        // Find the currency that matches this code
        foreach($this->amounts as $amount)
            if($amount->currency->code == $code)
                $currency = $amount->currency;

        return new MoneyAmount($currency, $balance, $approximate);
    }

    // TODO: add format function! this is tricky with multiple currencies

    // /**
    //  * Format the amount.
    //  *
    //  * @param boolean [$format=BALANCE_FORMAT_PLAIN] The balance formatting type.
    //  * @param array [$options=null] An array of options.
    //  *
    //  * @return string Formatted balance
    //  */
    // public function formatAmount($format = BALANCE_FORMAT_PLAIN, $options = []) {
    //     $prefix = ($options['prefix'] ?? '') . ($this->approximate ? '&asymp; ' : '');
    //     $options['prefix'] = $prefix;
    //     return $this
    //         ->currency
    //         ->format($this->amount, $format, $options);
    // }
}
