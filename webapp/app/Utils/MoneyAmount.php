<?php

namespace App\Utils;

use App\Models\Currency;

/**
 * Class MoneyAmount.
 *
 * Some amount of money, having a currency and an amount.
 *
 * @package App\Utils
 */
class MoneyAmount {

    /**
     * The currency.
     * @var Currency
     */
    public $currency;

    /**
     * The amount.
     * @var float
     */
    public $amount;

    /**
     * Whether the amount is approximate.
     * @var bool
     */
    public $approximate;

    /**
     * Constructor.
     *
     * @param Currency $currency The currency.
     * @param decimal $amount The amount.
     * @param bool [$approximate=false] Whether the amount is approximate.
     */
    public function __construct(Currency $currency, $amount, bool $approximate = false) {
        $this->currency = $currency;
        $this->amount = $amount;
        $this->approximate = $approximate;
    }

    /**
     * Add another money amount.
     *
     * Throws an excpetion if the currencies differ.
     *
     * @param MoneyAmount $amount Amount to add in same currency.
     * @return MoneyAmount This money amount.
     * @throws \Exception Throws if the currencies differ.
     */
    // TODO: do not mutate self here
    public function add(?MoneyAmount $amount) {
        if($amount == null)
            return $this;

        if($this->currency != $amount->currency)
            throw new \Exception("Cannot sum amounts, incompatible currencies");
        $this->amount += $amount->amount;
        $this->approximate = $this->approximate || $amount->approximate;
        return $this;
    }

    /**
     * Subtract another money amount.
     *
     * Throws an excpetion if the currencies differ.
     *
     * @param MoneyAmount $amount Amount to subtract in same currency.
     * @return MoneyAmount This money amount.
     * @throws \Exception Throws if the currencies differ.
     */
    // TODO: do not mutate self here
    public function sub(?MoneyAmount $amount) {
        if($amount == null)
            return $this;

        if($this->currency != $amount->currency)
            throw new \Exception("Cannot sum amounts, incompatible currencies");
        $this->amount -= $amount->amount;
        $this->approximate = $this->approximate || $amount->approximate;
        return $this;
    }

    /**
     * Multiply by an integer.
     *
     * @param int $factor Amount to multiply by.
     * @return MoneyAmount This money amount.
     * @throws \Exception Throws if the currencies differ.
     */
    // TODO: do not mutate self here
    public function mul(int $factor) {
        $this->amount *= $factor;
        return $this;
    }

    /**
     * Negate amount.
     *
     * @return MoneyAmount This money amount.
     * @throws \Exception Throws if the currencies differ.
     */
    // TODO: do not mutate self here
    public function neg() {
        $this->amount *= -1;
        return $this;
    }

    /**
     * Create a money amount instance being zero with the default currency.
     *
     * @param Currency $currency The currency.
     * @return MoneyAmount Zero amount.
     */
    public static function zero(Currency $currency) {
        return new MoneyAmount($currency, 0, false);
    }

    /**
     * Check whether the amount is zero.
     *
     * @return bool True if zero, false if not.
     */
    public function isZero() {
        return $this->amount == null || $this->amount == 0;
    }

    /**
     * Transform this into a MoneyAmountBag.
     *
     * @return MoneyAmountBag
     */
    public function toBag(): MoneyAmountBag {
        return new MoneyAmountBag([$this]);
    }

    /**
     * Clone this money amount.
     *
     * @return MoneyAmount The cloned amount.
     */
    public function clone(): MoneyAmount {
        return clone $this;
    }

    /**
     * Format the amount.
     *
     * @param boolean [$format=BALANCE_FORMAT_PLAIN] The balance formatting type.
     * @param array [$options=null] An array of options.
     *
     * @return string Formatted balance
     */
    public function formatAmount($format = BALANCE_FORMAT_PLAIN, $options = []) {
        $prefix = ($options['prefix'] ?? '') . ($this->approximate ? '&asymp; ' : '');
        $options['prefix'] = $prefix;
        return $this
            ->currency
            ->format($this->amount, $format, $options);
    }
}
