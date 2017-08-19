<?php

namespace App\Utils;

class TokenGenerator {

    /**
     * The characters a token can be made of.
     */
    const TOKEN_CHARS = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

    /**
     * The characters a token can be made of when special characters are allowed.
     */
    const TOKEN_CHARS_SPECIAL = self::TOKEN_CHARS . '!@#$%^&*()_-+=[]{}\\|/?<>,.`~';

    /**
     * Generate a new random token.
     *
     * @param int $length Token length in characters.
     * @param bool $allowSpecial=true True to allow special characters in the token, false to just allow alpha numerical characters.
     *
     * @return string Generated token.
     */
    public static function generate($length, $allowSpecial = true) {
        // Get the characters a session key can consist of
        $chars = $allowSpecial ? self::TOKEN_CHARS_SPECIAL : self::TOKEN_CHARS;

        // Generate the token
        $token = '';
        for($i = 0; $i < $length; $i++)
            $token .= $chars[rand(0, strlen($chars) - 1)];

        return $token;
    }
}