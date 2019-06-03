<?php

/**
 * Payment service translations.
 */
return [
    'qr' => [
        'showPaymentQr' => 'Toon QR code voor betaling',
        'instruction' => 'Scan de QR code met de bank app op je mobiele telefoon om de betalingsgegevens automatisch in te vullen. Dit werkt met de meeste moderne banken.',
    ],

    'manualiban' => [
        'pleaseTransferSameDescription' => 'Maak het bedrag over naar de rekening zoals hieronder beschreven. De beschrijving van de overboeking moet exact hetzelfde zijn en wordt gebruikt om je betaling te identificeren, anders gaat je betaling verloren.',
        'enterOwnIban' => 'Vul de IBAN in van de rekening waar vanaf je betaald, zodat we de betaling aan je account kunnen koppelen.',
        'confirmTransfer' => 'Ik bevestig dat ik het geld heb overgemaakt met de opgegeven gegevens.',
        'waitOnTransfer' => 'Aan het wachten op gebruikelijke vertragingen van overboekingen tussen banken voordat een groepsbeheerder gevraagd zal worden om je betaling te controleren.',
        'waitOnReceipt' => 'Aan het wachten op een groepsbeheerder om je betaling handmatig te controleren. Dit kan een lange tijd duren.',
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
];
