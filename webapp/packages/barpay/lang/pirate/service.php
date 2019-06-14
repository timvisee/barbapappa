<?php

/**
 * Payment service translations.
 */
return [
    /**
     * IBAN transfer service with manual check.
     */
    'manualiban' => [
        'name' => 'IBAN transfer, manual check',
        'duration' => 'takes up to 1 month',
    ],

    /**
     * Generic bunq related service translations.
     */
    'bunq' => [
        'unknownPaymentRefund' => 'unknown payment refund',
        'payed' => 'payed',
        'ibanCannotBeReceivingBunqAccount' => 'Cannot be an IBAN receiving payments on in this application.'
    ],

    /**
     * IBAN transfer service with automatic checking through bunq.
     */
    'bunqiban' => [
        'name' => 'IBAN transfer, automatic',
        'duration' => 'instant / up to 3 days',
    ],

    /**
     * Transfer through payment request using bunq.
     */
    'bunqrequest' => [
        'name' => 'iDeal transfer',
        'duration' => 'instant',
    ],
];
