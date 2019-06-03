<?php

/**
 * Payment service translations.
 */
return [
    'qr' => [
        'showPaymentQr' => 'Show payment QR code',
        'instruction' => 'Scan this QR code with your mobile banking app to automatically fill in all payment details. This works with most modern banks.',
    ],

    'manualiban' => [
        'pleaseTransferSameDescription' => 'Please transfer the amount to the account as noted below. You must use the exact same description which is used to identify your payment, or your payment will be lost.',
        'enterOwnIban' => 'Enter the IBAN you\'re transferring the money from, so we can link the payment to your account.',
        'confirmTransfer' => 'I confirm I\'ve transferred the money with the given payment details',
        'waitOnTransfer' => 'Waiting for usual bank transfer delays before requesting a community manager to review and confirm your transfer.',
        'waitOnReceipt' => 'Waiting for a community manager to manually confirm your transaction has been received. This may take a long while.',
        'steps' => [
            'transfer' => 'Transfer',
            'transferring' => 'Transferring',
            'receipt' => 'Receipt',
        ],
        'stepDescriptions' => [
            'transfer' => 'Transfer money, enter IBAN',
            'transferring' => 'Wait on transfer',
            'receipt' => 'Wait on receipt',
        ],
    ],
];
