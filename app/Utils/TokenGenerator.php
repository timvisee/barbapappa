<?php

namespace App\Utils;

class TokenGenerator {

    /**
     * The characters a token can be made of.
     */
    const TOKEN_CHARS = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-+=[]{}\\|/?<>,.`~';

    /**
     * Generate a new random token.
     *
     * @param int $length Token length in characters.
     *
     * @return string Generated token.
     */
    public static function generate($length) {
        // Get the characters a session key can consist of
        $chars = static::TOKEN_CHARS;

        // Generate the token
        $token = '';
        for($i = 0; $i < $length; $i++)
            $token .= $chars[rand(0, strlen($chars) - 1)];

        return $token;
    }
}