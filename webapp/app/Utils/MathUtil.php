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
    public static function solveInt(string $expr): ?int {
        // Solve + and -
        $a = strpos($expr, '+');
        $b = strpos($expr, '-');
        if($a !== false && ($b === false || $a < $b)) {
            $s1 = substr($expr, 0, $a);
            $s2 = substr($expr, $a + 1);
            $a = Self::solveInt($s1);
            $b = Self::solveInt($s2);
            return $a != null && $b != null ? $a + $b : null;
        }
        if($b !== false && ($a === false || $b < $a)) {
            $s1 = substr($expr, 0, $b);
            $s2 = substr($expr, $b + 1);
            $a = Self::solveInt($s1);
            $b = Self::solveInt($s2);
            return $a != null && $b != null ? $a - $b : null;
        }

        // Solve * and /
        $a = strpos($expr, '*');
        $b = strpos($expr, '/');
        if($a !== false && ($b === false || $a < $b)) {
            $s1 = substr($expr, 0, $a);
            $s2 = substr($expr, $a + 1);
            $a = Self::solveInt($s1);
            $b = Self::solveInt($s2);
            return $a != null && $b != null ? $a * $b : null;
        }
        if($b !== false && ($a === false || $b < $a)) {
            $s1 = substr($expr, 0, $b);
            $s2 = substr($expr, $b + 1);

            // Do not allow dividing with remainder
            $a = Self::solveInt($s1);
            $b = Self::solveInt($s2);
            return ($a != null && $b != null && $a % $b == 0) ? ($a / $b) : null;
        }

        // We assume this to just be a number now
        return Self::parseInt($expr);
    }

    /**
     * Parse an integer from the given string.
     *
     * Returns null if the given value is no integer.
     *
     * @param int $val String value representing the integer.
     * @return int|null Parsed value or null.
     */
    private static function parseInt($val): ?int {
        $int = (int) $val;
        return ((string) $int === (string) $val) ? $int : null;
    }
}
