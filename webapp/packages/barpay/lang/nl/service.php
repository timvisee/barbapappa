<?php

/**
 * Payment service translations.
 */
return [
    /**
     * An unknown payment.
     */
    'unknown' => [
        'name' => 'Betaling',
    ],

    /**
     * IBAN transfer service with manual check.
     */
    'manualiban' => [
        'name' => 'Bankoverschrijving (IBAN)',
        'duration' => 'duurt tot 1 maand',
    ],

    /**
     * Generic bunq related service translations.
     */
    'bunq' => [
        'unknownPaymentRefund' => 'terugbetaling onbekende storting',
        'paid' => 'betaald',
        'ibanCannotBeReceivingBunqAccount' => 'Kan geen IBAN zijn waarop betalingen worden ontvangen binnen deze applicatie.'
    ],

    /**
     * IBAN transfer service with automatic checking through bunq.
     */
    'bunqiban' => [
        'name' => 'Bankoverschrijving (IBAN)',
        'duration' => 'tot 3 dagen',
    ],

    /**
     * Transfer through a bunqme tab payment request.
     */
    'bunqmetab' => [
        'name' => 'iDeal betaling',
        'duration' => 'direct',
    ],
];
