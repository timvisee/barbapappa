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
    private $currency;

    /**
     * The amount.
     * @var decimal
     */
    public $amount;

    /**
     * Whether the amount is approximate.
     * @var bool
     */
    private $approximate;

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
     * Create a money amount instance being zero with the default currency.
     *
     * @param Currency $currency The currency.
     * @return MoneyAmount Zero amount.
     */
    public static function zero(Currency $currency) {
        return new MoneyAmount($currency, 0, false);
    }

    /**
     * Format the amount.
     *
     * @param boolean [$format=BALANCE_FORMAT_PLAIN] The balance formatting type.
     *
     * @return string Formatted balance
     */
    public function formatAmount($format = BALANCE_FORMAT_PLAIN) {
        $prefix = $this->approximate ? '&asymp; ' : '';
        return $this
            ->currency
            ->format($this->amount, $format, ['prefix' => $prefix]);
    }
}
