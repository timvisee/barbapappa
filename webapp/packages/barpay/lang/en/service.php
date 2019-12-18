<?php

/**
 * Payment service translations.
 */
return [
    /**
     * An unknown payment.
     */
    'unknown' => [
        'name' => 'Payment',
    ],

    /**
     * IBAN transfer service with manual check.
     */
    'manualiban' => [
        'name' => 'Manual transfer (IBAN)',
        'duration' => 'takes up to 1 month',
    ],

    /**
     * Generic bunq related service translations.
     */
    'bunq' => [
        'unknownPaymentRefund' => 'unknown payment refund',
        'paid' => 'paid',
        'ibanCannotBeReceivingBunqAccount' => 'Cannot be an IBAN receiving payments on in this application.'
    ],

    /**
     * IBAN transfer service with automatic checking through bunq.
     */
    'bunqiban' => [
        'name' => 'Manual transfer (IBAN)',
        'duration' => 'instant / up to 3 days',
    ],

    /**
     * Transfer through a bunqme tab payment request.
     */
    'bunqmetab' => [
        'name' => 'iDeal transfer',
        'duration' => 'instant',
    ],
];
