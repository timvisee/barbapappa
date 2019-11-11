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
     * The currency code.
     * @var Currency
     */
    private $currencyCode;

    /**
     * The amount.
     * @var decimal
     */
    private $amount;

    /**
     * Whether the amount is approximate.
     * @var bool
     */
    private $approximate;

    /**
     * Constructor.
     *
     * @param string $currencyCode The currency code.
     * @param decimal $amount The amount.
     * @param bool [$approximate=false] Whether the amount is approximate.
     */
    public function __construct(string $currencyCode, $amount, bool $approximate = false) {
        $this->currencyCode = $currencyCode;
        $this->amount = $amount;
        $this->approximate = $approximate;
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
        return balance($this->amount, $this->currencyCode, $format, ['prefix' => $prefix]);
    }
}
