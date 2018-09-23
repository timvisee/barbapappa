<?php

/**
 * Pages and their names.
 */
return [
    'pages' => 'Pagina\'s',
    'dashboard' => 'Dashboard',
    'yourPersonalDashboard' => 'Je persoonlijke dashboard',
    'emailPreferences' => 'E-mailvoorkeuren',
    'account' => 'Account',
    'yourAccount' => 'Jouw account',
    'requestPasswordReset' => 'Wachtwoord reset aanvragen',
    'changePassword' => 'Wachtwoord veranderen',
    'changePasswordDescription' => 'Vul de onderstaande velden in om je wachtwoord te veranderen.',
    'about' => 'Over',
    'terms' => 'Voorwaarden',
    'privacy' => 'Privacy',
    'contact' => 'Contact',

    /**
     * Profile page.
     */
    'profile' => [
        'name' => 'Profiel'
    ],

    /**
     * Profile edit page.
     */
    'editProfile' => [
        'name' => 'Profiel bewerken',
        'updated' => 'Je profiel is aangepast.',
        'otherUpdated' => 'Het profiel is aangepast.',
    ],

    /**
     * Account page.
     */
    'accountPage' => [
        'description' => 'Deze pagina laat een overzicht van je account zien.',
        'email' => [
            'description' => 'Deze pagina laat een overzicht van je e-mailaddressen zien.',
            'yourEmails' => 'Jouw e-mailadressen',
            'verifySent' => 'Een nieuwe verificatie-e-mail zal binnenkort verzonden worden.',
            'alreadyVerified' => 'Dit e-mailadres is al geverifiëerd.',
            'cannotDeleteMustHaveVerified' => 'Je kunt dit e-mailadres niet verwijderen, je moet tenminste één geverifiëerd adres hebben.',
            'deleted' => 'Het e-mailadres is verwijderd.',
        ],
        'addEmail' => [
            'title' => 'E-mailadres toevoegen',
            'description' => 'Vul the e-mailadres in dat je wilt toevoegen.',
            'added' => 'E-mailadres toegevoegd. Er is een verificatie-e-mail gestuurd.',
        ],
    ],

    /**
     * Verify email address page.
     */
    'verifyEmail' => [
        'title' => 'E-mailadres verifiëren',
        'description' => 'Vul alsjeblieft de verificatie token in voor het e-mailadres dat je wilt verifiëren.<br>'
            . 'Deze token is onderaan de e-mail te vinden die je hebt ontvangen met verificatie instructies.',
        'invalid' => 'Token onbekend. Misschien is het e-mailadres al geverifiëerd, of de token is verlopen.',
        'expired' => 'De token is verlopen. Vraag alsjeblieft een nieuwe verificatie e-mail aan.',
        'alreadyVerified' => 'Dit e-mailadres is al geverifiëerd.',
        'verified' => 'Super! Je e-mailadres is geverifiëerd.',
    ],

    /**
     * Password request sent page.
     */
    'passwordRequestSent' => [
        'title' => 'Check je mailbox',
        'message' => 'Als het e-mailadres dat je hebt opgegeven bekend is in ons systeem, hebben we de instructies voor het resetten van je wachtwoord naar je mailbox gestuurd.<br><br>'
            . 'Let er op dat deze instructies maar geldig zullen zijn voor <b>:hours uur</b>.<br><br>'
            . 'Je kunt deze webpagina nu sluiten.',
    ],

    /**
     * Password reset page.
     */
    'passwordReset' => [
        'enterResetToken' => 'Vul alsjeblieft je wachtwoord reset token in.'
            . 'Deze token is te vinden in de e-mail die je ontvangen hebt met wachtwoord reset instructies.',
        'enterNewPassword' => 'Vul alsjeblieft het nieuwe wachtwoord in dat je vanaf nu wilt gebruiken.',
        'invalid' => 'Token onbekend. Misschien is de token reeds verlopen.',
        'expired' => 'De token is verlopen. Vraag alsjeblieft een nieuwe wachtwoord reset aan.',
        'used' => 'Je wachtwoord is al aangepast met deze token.',
        'changed' => 'Weer zo goed als nieuw! Je wachtwoord is aangepast.',
    ],
];
