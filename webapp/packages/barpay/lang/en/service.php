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
        'name' => 'Bank transfer (IBAN)',
        'nameAdmin' => 'Bank transfer (IBAN) (manual check)',
        'duration' => 'up to 1 month',
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
        'name' => 'Bank transfer (IBAN)',
        'nameAdmin' => 'Bank transfer (IBAN) (automatic through bunq)',
        'duration' => 'up to 3 days',
    ],

    /**
     * Transfer through a bunqme tab payment request.
     */
    'bunqmetab' => [
        'name' => 'iDeal/Wero payment',
        'nameAdmin' => 'iDeal/Wero payment (through bunq)',
        'duration' => 'instant',
    ],
];
