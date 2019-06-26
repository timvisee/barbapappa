<?php

return [
    'paymentSettled' => [
        'kind' => 'Betaling',
        'message' => [
            'completed' => 'Je :amount top-up is voltooid',
            'revoked' => 'Je :amount top-up is ingetrokken',
            'rejected' => 'Je :amount top-up is afgekeurd',
            'failed' => 'Je :amount top-up is mislukt',
        ],
    ],
    'paymentRequiresUserAction' => [
        'kind' => 'Betaling',
        'message' => 'Je :amount top-up vereist actie',
    ],
    'paymentRequiresCommunityAction' => [
        'kind' => 'Betaling',
        'message' => 'Een :amount top-up vereist je beoordeling',
    ],
];
