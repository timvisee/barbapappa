<?php

return [
    'paymentSettled' => [
        'kind' => 'Payment',
        'message' => [
            'completed' => 'Yer :amount top-up completed',
            'revoked' => 'Yer :amount top-up was revoked',
            'rejected' => 'Yer :amount top-up was rejected',
            'failed' => 'Yer :amount top-up sunk',
        ],
    ],
    'paymentRequiresUserAction' => [
        'kind' => 'Payment',
        'message' => 'Your :amount top-up requires action',
    ],
    'paymentRequiresCommunityAction' => [
        'kind' => 'Payment',
        'message' => 'Your :amount top-up requires your review',
    ],
];
