<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TransformsRequest;

class NormalizeInput extends TransformsRequest {

    /**
     * Transform the given value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    public function transform($key, $value) {
        // Normalize the first and last names
        if($key == 'first_name' || $key == 'last_name')
            return name_case($value);

        // Normalize IBANs and BICs
        if($key == 'iban' || $key == 'bic')
            return is_string($value)
                ? preg_replace('/\s/', '', strtoupper($value))
                : $value;

        return $value;
    }
}
