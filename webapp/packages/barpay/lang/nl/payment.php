<?php

/**
 * Payment service translations.
 */
return [
    'qr' => [
        'showPaymentQr' => 'Toon QR code voor betaling',
        'instruction' => 'Scan de QR code met de bank app op je mobiele telefoon om de betalingsgegevens automatisch in te vullen. Dit werkt met sommige moderne banken.',
    ],

    'manualiban' => [
        'pleaseTransferSameDescription' => 'Maak het bedrag over naar de rekening zoals hieronder beschreven staat. De beschrijving van de overboeking moet exact hetzelfde zijn en wordt gebruikt om je betaling te identificeren, anders gaat je betaling verloren.',
        'enterOwnIban' => 'Vul de IBAN in van de rekening waar vanaf je betaald, zodat we de betaling aan je account kunnen koppelen.',
        'confirmTransfer' => 'Ik bevestig dat ik het geld heb overgemaakt met de gegeven informatie.',
        'waitOnTransfer' => 'Aan het wachten op gebruikelijke vertragingen bij overboekingen tussen banken voordat een groepsbeheerder gevraagd zal worden om je betaling te controleren.',
        'waitOnReceipt' => 'Aan het wachten op een groepsbeheerder om je betaling handmatig te controleren. Dit kan een lange tijd duren.',
        'pleaseConfirmReceivedDescription' => 'Verifieer dat deze transactie ontvangen is op de bankrekening. Het bedrag, de IBAN en de beschrijving moeten overeen komen.',
        'approve' => [
            'approve' => 'Betaling goedkeuren, geld is ontvangen, alle gegevens komen overeen',
            'delay' => 'Betaling uitstellen, geld is nog niet ontvangen, vraag later opnieuw',
            'reject' => 'Betaling afwijzen, geld is niet ontvangen en zal ook in de toekomst niet ontvangen worden',
        ],
        'actionMessage' => [
            'approve' => 'Betaling goedgekeurd',
            'delay' => 'Betaling uitgesteld',
            'reject' => 'Betaling afgekeurd',
        ],
        'steps' => [
            'transfer' => 'Overboeking',
            'transferring' => 'Overboeken',
            'receipt' => 'Bevestiging',
        ],
        'stepDescriptions' => [
            'transfer' => 'Geld overmaken, IBAN invullen',
            'transferring' => 'Wacht op boeking',
            'receipt' => 'Wacht op bevestiging',
        ],
    ],

    'bunqiban' => [
        'pleaseTransferSameDescription' => 'Maak het bedrag over naar de rekening zoals hieronder beschreven staat. De beschrijving van de overboeking moet exact hetzelfde zijn en wordt gebruikt om je betaling te identificeren, anders gaat je betaling verloren.',
        'enterOwnIban' => 'Vul de IBAN in van de rekening waar vanaf je betaald, zodat we de betaling aan je account kunnen koppelen.',
        'confirmTransfer' => 'Ik bevestig dat ik het geld heb overgemaakt met de gegeven informatie.',
        'waitOnReceipt' => 'Aan het wachten tot de betaling ontvangen is. Dit kan tot een aantal dagen duren.',
        'steps' => [
            'transfer' => 'Overboeking',
            'receipt' => 'Bevestiging',
        ],
        'stepDescriptions' => [
            'transfer' => 'Geld overmaken, IBAN invullen',
            'receipt' => 'Wacht op bevestiging',
        ],
    ],

    'bunqmetab' => [
        'waitOnReceipt' => 'Aan het wachten tot de betaling ontvangen is. Dit kan tot een aantal dagen duren.',
        'waitOnCreate' => 'De betaling wordt aangemaakt in de achtergrond. Even geduld alsjeblieft.',
        'pleasePay' => 'Klik op de \'Betaal\' knop en voltooi de betaling via bunq.',
        'handledByBunq' => 'Je betaling zal behandeld worden door bunq, een bank die hiervoor een service verleent. Je kunt met iDeal betalen op de bunq-betaalpagina.',
        'paymentForWalletTopUp' => 'Betaling voor portemonnee storting',
        'processingDontPayTwice' => 'Verwerken. Betaal niet dubbel!',
        'processingDescription' => 'Je betaling wordt verwerkt op de achtergrond. Het kan tot een minuut duren voordat de betaling doorkomt.',
        'steps' => [
            'create' => 'Aanmaken',
            'pay' => 'Betalen',
            'receipt' => 'Bevestiging',
        ],
        'stepDescriptions' => [
            'create' => 'Betaling aanmaken',
            'pay' => 'Betalen via bunq',
            'receipt' => 'Wacht op bevestiging',
        ],
    ],
];
