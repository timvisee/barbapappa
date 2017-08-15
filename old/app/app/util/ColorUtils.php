<?php

/**
 * ColorUtils.php
 * Utilities class for colors.
 *
 * @author Tim Visee
 * @website http://timvisee.com/
 * @copyright Copyright © Tim Visee 2013, All rights reserved.
 */

namespace app\util;

// Prevent direct requests to this set_file due to security reasons
defined('APP_INIT') or die('Access denied!');

/**
 * ColorUtils class.
 *
 * @package app\util
 * @author Tim Visee
 */
class ColorUtils {

    /**
     * Get the brightness of a HEX color, the brightness returned is a number from 0 to 255.
     *
     * @param string $hex The HEX color, the hash may be omitted.
     *
     * @return int The brightness of the HEX color.
     */
    public static function getHexBrightness($hex) {
        // Strip the leading hash
        $hex = str_replace('#', '', $hex);

        // Get the color values
        $colorRed = hexdec(substr($hex, 0, 2));
        $colorGreen = hexdec(substr($hex, 2, 2));
        $colorBlue = hexdec(substr($hex, 4, 2));

        // Calculate the brightness, return the result
        return (($colorRed * 299) + ($colorGreen * 587) + ($colorBlue * 114)) / 1000;
    }

    /**
     * Ajust the brightness of a HEX color.
     *
     * @param string $hex The HEX color the hash may be omitted.
     * @param int $steps The number of steps to change the color (-255 to 255).
     *
     * @return string The HEX color.
     */
    public static function adjustHexBrightness($hex, $steps) {
        // Steps should be between -255 and 255. Negative = darker, positive = lighter
        $steps = max(-255, min(255, $steps));

        // Normalize into a six character long hex string
        $hex = str_replace('#', '', $hex);
        if (strlen($hex) == 3) {
            $hex = str_repeat(substr($hex,0,1), 2).str_repeat(substr($hex,1,1), 2).str_repeat(substr($hex,2,1), 2);
        }

        // Split into three parts: R, G and B
        $color_parts = str_split($hex, 2);
        $return = '';

        foreach ($color_parts as $color) {
            $color   = hexdec($color); // Convert to decimal
            $color   = max(0,min(255,$color + $steps)); // Adjust color
            $return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT); // Make two char hex code
        }

        return $return;
    }
}