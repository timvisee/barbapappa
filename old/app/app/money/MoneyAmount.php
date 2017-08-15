<?php

namespace app\money;

// Prevent direct requests to this file due to security reasons
use Exception;

defined('APP_INIT') or die('Access denied!');

class MoneyAmount {

    /** @var int Money amount in cents. */
    private $amount = 0;

    /**
     * Constructor.
     *
     * @param int $amount The money amount.
     */
    public function __construct($amount = 0) {
        // Set the money amount
        $this->setAmount($amount);
    }

    /**
     * Parse a money amount value.
     *
     * @param MoneyAmount|int $amount The money amount.
     *
     * @return int The money amount value.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function parseMoneyAmountValue($amount) {
        // Return the value if it's an integer
        if(is_int($amount))
            return $amount;

        // Return the money amount if it's a MoneyAmount instance
        if($amount instanceof MoneyAmount)
            return $amount->getAmount();

        // Return the value if it's numeric
        if(is_numeric($amount))
            return (int) $amount;

        // Throw an exception
        throw new Exception('Invalid money amount.');
    }

    /**
     * Get the money amount.
     *
     * @return int Money amount.
     */
    public function getAmount() {
        return $this->amount;
    }

    /**
     * Set the money amount.
     *
     * @param int $amount Money amount.
     *
     * @throws Exception Throws if an error occurred.
     */
    public function setAmount($amount) {
        // Validate the amount
        if(!static::isValidAmount($amount))
            throw new Exception('The money amount is invalid.');

        // Parse and set the amount
        $this->amount = static::parseMoneyAmountValue($amount);
    }

    /**
     * Add the specified money amount.
     *
     * @param MoneyAmount|int $amount The money amount to add.
     *
     * @throws Exception Throws if an error occurred.
     */
    public function addAmount($amount) {
        if(!static::isValidAmount($amount))
            throw new Exception('The money amount is invalid.');

        // Parse and add the amount
        $this->amount += static::parseMoneyAmountValue($amount);
    }

    /**
     * Subtract the specified money amount.
     *
     * @param MoneyAmount|int $amount The money amount to subtract.
     *
     * @throws Exception Throws if an error occurred.
     */
    public function subAmount($amount) {
        if(!static::isValidAmount($amount))
            throw new Exception('The money amount is invalid.');

        // Parse and subtract the amount
        $this->amount -= static::parseMoneyAmountValue($amount);
    }

    /**
     * Get the formatted money amount string.
     *
     * @param bool $currencySign [optional] True to include the currency sign, false if not.
     * @param bool $currencyColor [optional] True to color the formatted string, false if not.
     *
     * @return string The formatted money amount string.
     */
    public function getFormatted($currencySign = true, $currencyColor = true) {
        // TODO: Use the preferred separator of the language used.
        // TODO: Set the colors and color triggers using constants.
        // TODO: Only show colors for balances?

        // Get the amount
        $amount = $this->getAmount();

        // Generate the formatted base string
        $formatted = ($currencySign ? '&euro;' : '') . number_format($amount / 100, 2, ',', ' ');

        // Add color
        if($currencyColor) {
            if($amount < 0)
                $formatted = '<span style="color: red;">' . $formatted . '</span>';
            elseif($amount > 500)
                $formatted = '<span style="color: green;">' . $formatted . '</span>';
        }

        // Return the formatted string
        return $formatted;
    }

    /**
     * Get the money amount object as a string.
     *
     * @return string Money amount object as a string.
     */
    public function __toString() {
        return $this->getFormatted(true);
    }

    /**
     * Check weather an amount
     *
     * @param $amount
     *
     * @return bool
     */
    public static function isValidAmount($amount) {
        // Return true if  the amount is a MoneyAmount instance
        if($amount instanceof self)
            return true;

        // Make sure the value is an integer, return the result
        return is_numeric($amount);
    }
}
