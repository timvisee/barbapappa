<?php

namespace App\Utils;

/**
 * Class MoneyAmount.
 *
 * Some amount of money, having a currency and an amount.
 *
 * @package App\Utils
 */
class MathUtil {

    /**
     * Basic integer math expression.
     * Allows positive/negative numbers, [+,-,*,/], whitespaces around groups,
     * and a _ within numbers.
     */
    const EXPR_INT = "/^\s*(-?(\d|_)+)(\s*((\+|\-|\*|\/))\s*(-?(\d|_)+))*\s*$/";

    /**
     * Solve the given integer expression.
     *
     * The given expression must solve to an integer (whole number), otherwise
     * an error is returned. This is to prevent accidental wrong numbers.
     *
     * @param string $expr The expression.
     * @return int|null The solved value, or null on failure.
     */
    public static function solveInteger(string $expr) {
        // TODO: add some form of error reporting, with an enum possibly
        // TODO: ensure that devide uses float devide, so we can error if it
        //       doesn't result in an integer number

        // Input expression must match supported format
        if(preg_match(Self::EXPR_INT, $expr) != 1)
            return null;

        // Clean up expression from unused chars
        $expr = str_replace([" ", "\n", "\r", "\t", "\v", "\x00", '_'], '', $expr);

        // Solve expression
        // TODO: WARNING: do not use eval, it is insecure, implement a solver ourselves
        $value = eval('return ' . $expr . ';');

        // Output value must be an int
        if(!is_int($value))
            return null;

        return $value;
    }
}
