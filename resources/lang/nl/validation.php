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

    'accepted'             => 'De :attribute moet geaccepteerd worden.',
    'active_url'           => 'De :attribute is een ongeldige URL.',
    'after'                => 'De :attribute moet een datum zijn na :date.',
    'after_or_equal'       => 'De :attribute moet een datum zijn gelijk aan of na :date.',
    'alpha'                => 'De :attribute mag alleen letters bevatten.',
    'alpha_dash'           => 'De :attribute mag alleen letters, nummers en streepjes bevatten.',
    'alpha_num'            => 'De :attribute mag alleen letters en nummers bevatten.',
    'array'                => 'De :attribute moet een lijst zijn.',
    'before'               => 'De :attribute moet een datum zijn voor :date.',
    'before_or_equal'      => 'De :attribute moet een datum zijn voor of gelijk aan :date.',
    'between'              => [
        'numeric' => 'De :attribute moet tussen de :min en :max zijn.',
        'file'    => 'De :attribute moet tussen de :min en :max kilobytes zijn.',
        'string'  => 'De :attribute moet tussen de :min en :max karakters zijn.',
        'array'   => 'De :attribute moet tussen de :min en :max elementen bevatten.',
    ],
    'boolean'              => 'De :attribute veld moet waar of onwaar zijn.',
    'confirmed'            => 'De :attribute validatie komt niet overeen.',
    'date'                 => 'De :attribute is een ongeldige datum.',
    'date_format'          => 'De :attribute komt niet overeen met het formaat :format.',
    'different'            => 'De :attribute en :other moeten verschillen.',
    'digits'               => 'De :attribute moet :digits getallen zijn.',
    'digits_between'       => 'De :attribute moet tussen de :min en :max getallen zijn.',
    'dimensions'           => 'De :attribute heeft een ongeldig afbeeldingsformaat.',
    'distinct'             => 'De :attribute heeft een dubbele waarde.',
    'email'                => 'De :attribute moet een geldig e-mailadres zijn.',
    'exists'               => 'De geselecteerde :attribute is ongeldig.',
    'file'                 => 'De :attribute moet een bestand zijn.',
    'filled'               => 'De :attribute moet een waarde bevatten.',
    'image'                => 'De :attribute moet een afbeelding zijn.',
    'in'                   => 'De geselecteerde :attribute is ongeldig.',
    'in_array'             => 'De :attribute veld komt niet voor in :other.',
    'integer'              => 'De :attribute moet een geheel getal zijn.',
    'ip'                   => 'De :attribute moet een geldig IP adres zijn.',
    'ipv4'                 => 'De :attribute moet een geldig IPv4 adres zijn.',
    'ipv6'                 => 'De :attribute moet een geldig IPv6 adres zijn.',
    'json'                 => 'De :attribute moet een geldig JSON string zijn.',
    'max'                  => [
        'numeric' => 'De :attribute mag niet groter zijn dan :max.',
        'file'    => 'De :attribute mag niet groter zijn dan :max kilobytes.',
        'string'  => 'De :attribute mag niet langer zijn dan :max karakters.',
        'array'   => 'De :attribute mag niet meer dan :max elementen bevatten.',
    ],
    'mimes'                => 'De :attribute moet een bestand zijn van het type: :values.',
    'mimetypes'                => 'De :attribute moet een bestand zijn van het type: :values.',
    'min'                  => [
        'numeric' => 'De :attribute moet tenminste :min zijn.',
        'file'    => 'De :attribute moet tenminste :min kilobytes zijn.',
        'string'  => 'De :attribute moet tenminste :min karakters lang zijn.',
        'array'   => 'De :attribute moet tenminste :min elementen bevatten.',
    ],
    'not_in'               => 'De geselecteerde :attribute is ongeldig.',
    'numeric'              => 'De :attribute moet een nummer zijn.',
    'present'              => 'De :attribute veld moet beschikbaar zijn.',
    'regex'                => 'De :attribute format is ongeldig.',
    'required'             => 'De :attribute veld is vereist.',
    'required_if'          => 'De :attribute veld is vereist wanneer :other :value is.',
    'required_unless'      => 'De :attribute veld is vereist tenzei :other in :values is.',
    'required_with'        => 'De :attribute veld is vereist als :values beschikbaar is.',
    'required_with_all'    => 'De :attribute veld is vereist als :values beschikbaar is',
    'required_without'     => 'De :attribute veld is vereist als :values niet beschikbaar is.',
    'required_without_all' => 'De :attribute veld is vereist als geen van de :values beschikbaar is.',
    'same'                 => 'De :attribute en :other moeten overeen komen.',
    'size'                 => [
        'numeric' => 'De :attribute moet :size groot zijn.',
        'file'    => 'De :attribute moet :size kilobytes groot zijn.',
        'string'  => 'De :attribute moet :size characters groot zijn.',
        'array'   => 'De :attribute moet :size elementen bevatten.',
    ],
    'string'               => 'De :attribute moet een tekstwaarde zijn.',
    'timezone'             => 'De :attribute moet een geldige tijdzone zijn.',
    'unique'               => 'De :attribute is al gebruikt.',
    'uploaded'             => 'De :attribute is mislukt met uploaden.',
    'url'                  => 'De :attribute formaat is ongeldig.',

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

    'attributes' => [],

];
