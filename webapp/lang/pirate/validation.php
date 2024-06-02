<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => 'Th\' :attribute \'d be accepted.',
    'active_url'           => 'Th\' :attribute be nay valid URL.',
    'after'                => 'Th\' :attribute \'d be a date after :date.',
    'after_or_equal'       => 'Th\' :attribute \'d be a date after or equal to :date.',
    'alpha'                => 'Th\' :attribute may only contain letters.',
    'alpha_dash'           => 'Th\' :attribute may only contain letters, numbers, n\' dashes.',
    'alpha_num'            => 'Th\' :attribute may only contain letters n\' numbers.',
    'array'                => 'Th\' :attribute \'d be an array.',
    'before'               => 'Th\' :attribute \'d be a date before :date.',
    'before_or_equal'      => 'Th\' :attribute \'d be a date before or equal to :date.',
    'between'              => [
        'numeric' => 'Th\' :attribute \'d be between :min n\' :max.',
        'file'    => 'Th\' :attribute \'d be between :min n\' :max kilobytes in size.',
        'string'  => 'Th\' :attribute \'d be between :min n\' :max characters long.',
        'array'   => 'Th\' :attribute \'d have between :min n\' :max items.',
    ],
    'boolean'              => 'Th\' :attribute field \'d be yay or nay.',
    'confirmed'            => 'Th\' :attribute confirmation does not match.',
    'date'                 => 'Th\' :attribute be not a valid date.',
    'date_format'          => 'Th\' :attribute does not match th\' format :format.',
    'different'            => 'Th\' :attribute n\' :other \'d be different.',
    'digits'               => 'Th\' :attribute \'d be :digits digits.',
    'digits_between'       => 'Th\' :attribute \'d be between :min n\' :max digits.',
    'dimensions'           => 'Th\' :attribute has wrong image dimensions.',
    'distinct'             => 'Th\' :attribute field has a duplicate value.',
    'email'                => 'Th\' :attribute \'d be a valid e-bottle coordinate.',
    'exists'               => 'Th\' selected :attribute be sunken.',
    'file'                 => 'Th\' :attribute \'d be a package.',
    'filled'               => 'Th\' :attribute field \'d have a value.',
    'image'                => 'Th\' :attribute \'d be an image.',
    'in'                   => 'Th\' selected :attribute be sunken.',
    'in_array'             => 'Th\' :attribute field does not sail in :other.',
    'integer'              => 'Th\' :attribute \'d be an integer.',
    'ip'                   => 'Th\' :attribute \'d be a valid IP coordinate.',
    'ipv4'                 => 'Th\' :attribute \'d be a valid IPv4 coordinate.',
    'ipv6'                 => 'Th\' :attribute \'d be a valid IPv6 coordinate.',
    'json'                 => 'Th\' :attribute \'d be a valid JSON package.',
    'max'                  => [
        'numeric' => 'Th\' :attribute may not be greater than :max.',
        'file'    => 'Th\' :attribute may not be greater than :max kilobytes in size.',
        'string'  => 'Th\' :attribute may not be greater than :max characters long.',
        'array'   => 'Th\' :attribute may not have more than :max items.',
    ],
    'mimes'                => 'Th\' :attribute \'d be a package of type: :values.',
    'mimetypes'            => 'Th\' :attribute \'d be a package of type: :values.',
    'min'                  => [
        'numeric' => 'Th\' :attribute \'d be at least :min.',
        'file'    => 'Th\' :attribute \'d be at least :min kilobytes in size.',
        'string'  => 'Th\' :attribute \'d be at least :min characters long.',
        'array'   => 'Th\' :attribute \'d have at least :min items.',
    ],
    'not_in'               => 'Th\' selected :attribute be sunken.',
    'numeric'              => 'Th\' :attribute \'d be a number.',
    'present'              => 'Th\' :attribute field \'d be present.',
    'regex'                => 'Th\' :attribute format be sunken.',
    'not_regex'            => 'Th\' :attribute format nay be allowed.',
    'required'             => 'Th\' :attribute field be required.',
    'required_if'          => 'Th\' :attribute field be required when :other be :value.',
    'required_unless'      => 'Th\' :attribute field be required unless :other be in :values.',
    'required_with'        => 'Th\' :attribute field be required when :values be sailing.',
    'required_with_all'    => 'Th\' :attribute field be required when :values be sailing.',
    'required_without'     => 'Th\' :attribute field be required when :values be sunken.',
    'required_without_all' => 'Th\' :attribute field be required when none of :values are sailing.',
    'prohibited'           => 'Th\' :attribute field be prohibited.',
    'prohibited_if'        => 'Th\' :attribute field be prohibited when :other be :value.',
    'prohibited_unless'    => 'Th\' :attribute field be prohibited unless :other be in :values.',
    'prohibits'            => 'Th\' :attribute field prohibits :other from being present.',
    'same'                 => 'Th\' :attribute and :other \'d match.',
    'size'                 => [
        'numeric' => 'Th\' :attribute \'d be :size.',
        'file'    => 'Th\' :attribute \'d be :size kilobytes in size.',
        'string'  => 'Th\' :attribute \'d be :size characters long.',
        'array'   => 'Th\' :attribute \'d contain :size items.',
    ],
    'string'               => 'Th\' :attribute \'d be a string.',
    'timezone'             => 'Th\' :attribute \'d be a valid sea zone.',
    'unique'               => 'Th\' :attribute has already been entered.',
    'uploaded'             => 'Th\' :attribute failed to upload.',
    'url'                  => 'Th\' :attribute format be sunken.',
    'iban'                 => 'Enter a valid IBAN.',
    'bic'                  => 'Enter a valid BIC.',
    'recaptchav3'          => 'The reCAPTCHA check failed, please try again later.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    'others_empty' => 'Cannot enter multiple values.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [
        'email' => 'e-bottle',
        'password' => 'passcode',
        'password_confirmation' => 'passcode check',
        'new_password' => 'shiny passcode',
        'new_password_confirmation' => 'shiny passcode check',
        'language' => 'speak',
        'invalidate_other_sessions' => 'sink other ships',
        'iban' => 'IBAN',
        'bic' => 'BIC',
        'g-recaptcha-response' => 'reCAPTCHA',
    ],

];
