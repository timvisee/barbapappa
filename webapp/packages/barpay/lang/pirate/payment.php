<?php

/**
 * Payment service translations.
 */
return [
    'qr' => [
        'showPaymentQr' => 'Show payment QR code',
        'instruction' => 'Scan dis QR code wit\' yer banking app on yer handheld phoning device to automatically fill in all payment details. Dis works wit\' some modern banks.',
    ],

    'manualiban' => [
        'pleaseTransferSameDescription' => 'Please transfer th\' amount to th\' account as noted below. Ye must use th\' exact same description which be used to identify yer payment, or yer payment will be lost.',
        'enterOwnIban' => 'Enter yer IBAN yer transferring th\' money from, so we can link the payment to yer account.',
        'confirmTransfer' => 'I confirm I transferred th\' money wit\' given payment details',
        'waitOnTransfer' => 'Waiting for usual bank transfer delays before requesting a crew manager to review \'n confirm yer transfer.',
        'waitOnReceipt' => 'Waiting for a crew manager to manually confirm yer transaction be received. Dis may take a long while.',
        'pleaseConfirmReceivedDescription' => 'Please confirm dis transaction be received on yer banking account. Th\' amount, IBAN and description must match.',
        'approve' => [
            'approve' => 'Approve payment, money be received, all details match',
            'delay' => 'Delay payment, money nay be received, ask again later',
            'reject' => 'Reject payment, money nay be received and won\'t be received in th\' future',
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
        'pleaseTransferSameDescription' => 'Please transfer th\' amount to th\' account as noted below. Ye must use th\' exact same description which be used to identify yer payment, or yer payment will be lost.',
        'enterOwnIban' => 'Enter yer IBAN yer transferring th\' money from, so we can link the payment to yer account.',
        'confirmTransfer' => 'I confirm I transferred th\' money wit\' given payment details',
        'waitOnReceipt' => 'Waiting for th\' payment to be received. Dis may take up to a few days.',
        'steps' => [
            'transfer' => 'Transfer',
            'receipt' => 'Receipt',
        ],
        'stepDescriptions' => [
            'transfer' => 'Transfer money, enter IBAN',
            'receipt' => 'Wait on receipt',
        ],
    ],

    'bunqmetab' => [
        'waitOnReceipt' => 'Waiting for th\' payment to be received. Dis may take up to a few days.',
        'waitOnCreate' => 'Th\' payment be started in th\' background. Please wait.',
        'pleasePay' => 'Please click th\' \'Pay\' button and finish th\' payment through bunq.',
        'handledByBunq' => 'Yer payment be handled through bunq, a bank which provides a service for this. Ye be pay with iDeal on th\' bunq payment page.',
        'paymentForWalletTopUp' => 'Payment for wallet top-up',
        'processingDontPayTwice' => 'Processing. Do not pay twice!',
        'processingDescription' => 'Yer payment be processed in th\' backgroud. Please allow up to minute for it to come through.',
        'steps' => [
            'create' => 'Create',
            'pay' => 'Pay',
            'receipt' => 'Receipt',
        ],
        'stepDescriptions' => [
            'create' => 'Creating payment',
            'pay' => 'Pay via bunq',
            'receipt' => 'Wait on receipt',
        ],
        'continueToPayment' => 'Continue yer payment',
    ],
];
