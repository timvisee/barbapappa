<?php

return [
    'paymentSettled' => [
        'kind' => 'Betaling',
        'message' => [
            'completed' => 'Je :amount opwaardering is voltooid',
            'revoked' => 'Je :amount opwaardering is ingetrokken',
            'rejected' => 'Je :amount opwaardering is afgekeurd',
            'failed' => 'Je :amount opwaardering is mislukt',
        ],
    ],
    'paymentRequiresUserAction' => [
        'kind' => 'Betaling',
        'message' => 'Je :amount opwaardering vereist actie',
    ],
    'paymentRequiresCommunityAction' => [
        'kind' => 'Betaling',
        'message' => 'Een :amount opwaardering vereist je beoordeling',
    ],
];
