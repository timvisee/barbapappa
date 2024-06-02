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
    'active_url'           => 'Het :attribute veld bevat een ongeldige URL.',
    'after'                => 'Het :attribute veld moet een datum bevatten na :date.',
    'after_or_equal'       => 'Het :attribute veld moet een datum bevatten gelijk aan of na :date.',
    'alpha'                => 'Het :attribute veld mag alleen letters bevatten.',
    'alpha_dash'           => 'Het :attribute veld mag alleen letters, nummers en streepjes bevatten.',
    'alpha_num'            => 'Het :attribute veld mag alleen letters en nummers bevatten.',
    'array'                => 'Het :attribute veld moet een lijst zijn.',
    'before'               => 'Het :attribute veld moet een datum bevatten voor :date.',
    'before_or_equal'      => 'Het :attribute veld moet een datum bevatten voor of gelijk aan :date.',
    'between'              => [
        'numeric' => 'Het :attribute moet tussen de :min en :max zijn.',
        'file'    => 'Het :attribute moet tussen de :min en :max kilobytes in grootte zijn.',
        'string'  => 'Het :attribute moet tussen de :min en :max karakters lang zijn.',
        'array'   => 'Het :attribute moet tussen de :min en :max elementen bevatten.',
    ],
    'boolean'              => 'Het :attribute veld moet waar of onwaar zijn.',
    'confirmed'            => 'De :attribute validatie komt niet overeen.',
    'date'                 => 'Het :attribute veld bevat een ongeldige datum.',
    'date_format'          => 'De :attribute komt niet overeen met het formaat :format.',
    'different'            => 'De :attribute en :other velden moeten verschillen.',
    'digits'               => 'Het :attribute veld moet :digits getallen bevatten.',
    'digits_between'       => 'Het :attribute veld moet tussen de :min en :max getallen bevatten.',
    'dimensions'           => 'De :attribute heeft een ongeldig afbeeldingsformaat.',
    'distinct'             => 'Het :attribute veld heeft een dubbele waarde.',
    'email'                => 'Het :attribute veld moet een geldig e-mailadres bevatten.',
    'exists'               => 'Het geselecteerde :attribute is ongeldig.',
    'file'                 => 'Het :attribute veld moet een bestand bevatten.',
    'filled'               => 'Het :attribute veld moet een waarde bevatten.',
    'image'                => 'Het :attribute veld moet een afbeelding bevatten.',
    'in'                   => 'De geselecteerde waarde in het :attribute veld is ongeldig.',
    'in_array'             => 'Het :attribute veld komt niet voor in :other.',
    'integer'              => 'Het :attribute veld moet een geheel getal bevatten.',
    'ip'                   => 'Het :attribute veld moet een geldig IP adres bevatten.',
    'ipv4'                 => 'Het :attribute veld moet een geldig IPv4 adres bevatten.',
    'ipv6'                 => 'Het :attribute veld moet een geldig IPv6 adres bevatten.',
    'json'                 => 'Het :attribute veld moet een geldig JSON string bevatten.',
    'max'                  => [
        'numeric' => 'Het :attribute veld mag niet groter dan :max bevatten.',
        'file'    => 'Het :attribute veld mag niet groter dan :max kilobytes in grootte bevatten.',
        'string'  => 'Het :attribute veld mag niet meer dan :max karakters bevatten.',
        'array'   => 'Het :attribute veld mag niet meer dan :max elementen bevatten.',
    ],
    'mimes'                => 'Het :attribute veld moet een bestand bevatten van het type: :values.',
    'mimetypes'            => 'Het :attribute veld moet een bestand bevatten van het type: :values.',
    'min'                  => [
        'numeric' => 'Het :attribute veld moet tenminste :min bevatten.',
        'file'    => 'Het :attribute veld moet tenminste :min kilobytes bevatten.',
        'string'  => 'Het :attribute veld moet tenminste :min karakters bevatten.',
        'array'   => 'Het :attribute veld moet tenminste :min elementen bevatten.',
    ],
    'not_in'               => 'De geselecteerde waarde in het :attribute veld is ongeldig.',
    'numeric'              => 'Het :attribute veld moet een nummer bevatten.',
    'present'              => 'Het :attribute veld moet beschikbaar zijn.',
    'regex'                => 'Het formaat in het :attribute veld is ongeldig.',
    'not_regex'            => 'Het formaat in het :attribute veld is niet toegestaan.',
    'required'             => 'Het :attribute veld is vereist.',
    'required_if'          => 'Het :attribute veld is vereist wanneer :other :value is.',
    'required_unless'      => 'Het :attribute veld is vereist tenzei :other in :values is.',
    'required_with'        => 'Het :attribute veld is vereist als :values beschikbaar is.',
    'required_with_all'    => 'Het :attribute veld is vereist als :values beschikbaar is',
    'required_without'     => 'Het :attribute veld is vereist als :values niet beschikbaar is.',
    'required_without_all' => 'Het :attribute veld is vereist als geen van de :values beschikbaar is.',
    'prohibited'           => 'Het :attribute veld is verboden.',
    'prohibited_if'        => 'Het :attribute veld is verboden wanneer :other :value is.',
    'prohibited_unless'    => 'Het :attribute veld is verboden behalve als :other :values is.',
    'prohibits'            => 'Het :attribute veld verbiedt :other.',
    'same'                 => 'De waarde in de :attribute en :other velden moet overeen komen.',
    'size'                 => [
        'numeric' => 'Het :attribute veld moet :size groot zijn.',
        'file'    => 'Het :attribute veld moet :size kilobytes bevatten.',
        'string'  => 'Het :attribute veld moet :size karakters bevatten.',
        'array'   => 'Het :attribute veld moet :size elementen bevatten.',
    ],
    'string'               => 'Het :attribute veld moet een tekstwaarde bevatten.',
    'timezone'             => 'Het :attribute veld moet een geldige tijdzone bevatten.',
    'unique'               => 'De waarde in het :attribute veld is al gebruikt.',
    'uploaded'             => 'Het uploaden van het bestand in het :attribute veld is mislukt.',
    'url'                  => 'Het formaat in het :attribute veld is ongeldig.',
    'iban'                 => 'Vul een geldige IBAN in.',
    'bic'                  => 'Vul een geldige BIC in.',
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

    'others_empty' => 'Meerdere waardes niet toegestaan.',

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
        'email' => 'e-mail',
        'password' => 'wachtwoord',
        'password_confirmation' => 'wachtwoord controle',
        'new_password' => 'nieuw wachtwoord',
        'new_password_confirmation' => 'nieuw wachtwoord controle',
        'name' => 'naam',
        'first_name' => 'voornaam',
        'last_name' => 'achternaam',
        'language' => 'taal',
        'currency' => 'valuta',
        'enabled' => 'ingeschakeld',
        'invalidate_other_sessions' => 'log uit op andere apparaten',
        'slug' => 'URL-pad',
        'amount' => 'bedrag',
        'amount_custom' => 'aangepast bedrag',
        'iban' => 'IBAN',
        'bic' => 'BIC',
        'account_holder' => 'rekeninghouder',
        'payment_service' => 'betaalservice',
        'choice' => 'keuze',
        'confirm' => 'bevestiging',
        'confirm_transfer' => 'bevestiging',
        'cost' => 'kosten',
        'balance' => 'saldo',
        'confirm_name' => 'naam bevestiging',
        'confirm_delete' => 'verwijder bevestiging',
        'wallet' => 'portemonnee',
        'to_wallet' => 'naar portemonnee',
        'symbol' => 'symbool',
        'format' => 'formaat',
        'method' => 'methode',
        'g-recaptcha-response' => 'reCAPTCHA',
    ],

];
