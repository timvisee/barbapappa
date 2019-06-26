<?php

return [
    'paymentSettled' => [
        'kind' => 'Payment',
        'message' => [
            'completed' => 'Your :amount top-up completed',
            'revoked' => 'Your :amount top-up was revoked',
            'rejected' => 'Your :amount top-up was rejected',
            'failed' => 'Your :amount top-up failed',
        ],
    ],
    'paymentRequiresUserAction' => [
        'kind' => 'Payment',
        'message' => 'Your :amount top-up requires action',
    ],
];
