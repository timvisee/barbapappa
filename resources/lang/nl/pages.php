<?php

/**
 * Pages and their names.
 */
return [
    'dashboard' => 'Dashboard',
    'yourPersonalDashboard' => 'Je persoonlijke dashboard',
    'emailPreferences' => 'E-mailvoorkeuren',
    'account' => 'Account',
    'yourAccount' => 'Jouw account',
    'profile' => 'Profiel',
    'editProfile' => 'Profiel bewerken',
    'requestPasswordReset' => 'Wachtwoord reset aanvragen',
    'changePassword' => 'Wachtwoord veranderen',
    'changePasswordDescription' => 'Vul de onderstaande velden in om je wachtwoord te veranderen.',
    'about' => 'Over',
    'terms' => 'Voorwaarden',
    'privacy' => 'Privacy',
    'contact' => 'Contact',

    /**
     * Account page.
     */
    'accountOverview' => [
        'description' => 'Deze pagina laat een overzicht van je account zien.',
    ],

    /**
     * Password request sent page.
     */
    'passwordRequestSent' => [
        'title' => 'Check je mailbox',
        'message' => 'Als het e-mailadres dat je hebt opgegeven wordt herkend door ons systeem, sturen we instructies om je wachtwoord te resetten.<br><br>'
            . 'Let er op dat als je instructies gestuurd hebben, ze geldig zijn voor maar <b>:hours uur</b>.<br><br>'
            . 'Je kunt deze webpagina nu sluiten.',
    ],

    /**
     * Password reset page.
     */
    'passwordReset' => [
        'enterResetToken' => 'Vul alsjeblieft je wachtwoord reset token in.'
            . 'Deze token is te vinden in de e-mail die je ontvangen hebt met wachtwoord reset instructies.',
        'enterNewPassword' => 'Vul alsjeblieft het nieuwe wachtwoord in wat je vanaf nu wilt gebruiken.',
    ],
];
