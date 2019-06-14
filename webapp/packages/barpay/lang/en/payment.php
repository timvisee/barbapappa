<?php

/**
 * Payment service translations.
 */
return [
    'qr' => [
        'showPaymentQr' => 'Show payment QR code',
        'instruction' => 'Scan this QR code with your mobile banking app to automatically fill in all payment details. This works with some modern banks.',
    ],

    'manualiban' => [
        'pleaseTransferSameDescription' => 'Please transfer the amount to the account as noted below. You must use the exact same description which is used to identify your payment, or your payment will be lost.',
        'enterOwnIban' => 'Enter the IBAN you\'re transferring the money from, so we can link the payment to your account.',
        'confirmTransfer' => 'I confirm I\'ve transferred the money with the given payment details',
        'waitOnTransfer' => 'Waiting for usual bank transfer delays before requesting a community manager to review and confirm your transfer.',
        'waitOnReceipt' => 'Waiting for a community manager to manually confirm your transaction has been received. This may take a long while.',
        'pleaseConfirmReceivedDescription' => 'Please confirm this transaction is received on your banking account. The amount, IBAN and description must match.',
        'approve' => [
            'approve' => 'Approve payment, money is received, all details match',
            'delay' => 'Delay payment, money is not yet received, ask again later',
            'reject' => 'Reject payment, money is not received and won\'t be received in the future',
        ],
        'actionMessage' => [
            'approve' => 'Payment approved',
            'delay' => 'Payment delayed',
            'reject' => 'Payment rejected',
        ],
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

    'bunqiban' => [
        'pleaseTransferSameDescription' => 'Please transfer the amount to the account as noted below. You must use the exact same description which is used to identify your payment, or your payment will be lost.',
        'enterOwnIban' => 'Enter the IBAN you\'re transferring the money from, so we can link the payment to your account.',
        'confirmTransfer' => 'I confirm I\'ve transferred the money with the given payment details',
        'waitOnReceipt' => 'Waiting for the payment to be received. This may take up to a few days.',
        'steps' => [
            'transfer' => 'Transfer',
            'receipt' => 'Receipt',
        ],
        'stepDescriptions' => [
            'transfer' => 'Transfer money, enter IBAN',
            'receipt' => 'Wait on receipt',
        ],
    ],
];
