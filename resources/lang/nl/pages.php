<?php

/**
 * Pages and their names.
 */
return [
    'pages' => 'Pagina\'s',
    'dashboard' => 'Dashboard',
    'yourPersonalDashboard' => 'Je persoonlijke dashboard',
    'emailPreferences' => 'E-mailvoorkeuren',
    'communities' => 'Groepen',
    'bars' => 'Bars',
    'account' => 'Account',
    'yourAccount' => 'Jouw account',
    'requestPasswordReset' => 'Wachtwoord reset aanvragen',
    'changePassword' => 'Wachtwoord veranderen',
    'changePasswordDescription' => 'Vul de onderstaande velden in om je wachtwoord te veranderen.',
    'about' => 'Over',
    'contact' => 'Contact',
    'contactUs' => 'Neem contact op',

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
            'resendVerify' => 'Verificatie opnieuw versturen',
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
     * Community pages.
     */
    'community' => [
        'yourCommunities' => 'Jouw groepen',
        'noCommunities' => 'Geen groepen beschikbaar...',
        'viewCommunity' => 'Bekijk groep',
        'viewCommunities' => 'Bekijk groepen',
        'join' => 'Inschrijven',
        'yesJoin' => 'Ja, inschrijven',
        'joined' => 'Ingeschreven',
        'notJoined' => 'Niet ingeschreven',
        'hintJoin' => 'Je maakt nog geen deel uit van deze groep.',
        'joinedClickToLeave' => 'Klik om uit te schrijven.',
        'joinQuestion' => 'Wil je je bij deze groep inschrijven?',
        'joinedThisCommunity' => 'Je bent ingeschreven bij deze groep.',
        'leaveQuestion' => 'Weet je zeker dat je je wilt uitschrijven bij deze groep?',
        'leftThisCommunity' => 'Je bent uitgeschreven bij deze groep.',
        'protectedByCode' => 'Deze groep is beveiligd met een code. Vraag er naar bij de groep, of scan de groep QR-code als deze beschikbaar is.',
        'protectedByCodeFilled' => 'Deze groep is beveiligd met een code. We hebben de code voor je ingevuld.',
        'incorrectCode' => 'Verkeerde groep code.',
    ],

    /**
     * Bar pages.
     */
    'bar' => [
        'yourBars' => 'Jouw bars',
        'noBars' => 'Geen bars beschikbaar...',
        'searchByCommunity' => 'Zoeken via groepen',
        'searchByCommunityDescription' => 'Vaak is het makkelijker om een bar te zoeken via de bijbehorende groep.',

        // TODO: remove duplicates
        'join' => 'Inschrijven',
        'yesJoin' => 'Ja, inschrijven',
        'joined' => 'Ingeschreven',
        'notJoined' => 'Niet ingeschreven',

        'hintJoin' => 'Je maakt nog geen deel uit van deze bar.',
        'joinedClickToLeave' => 'Klik om uit te schrijven.',
        'joinQuestion' => 'Wil je je bij deze bar inschrijven?',
        'alsoJoinCommunity' => 'Ook inschrijven bij de bijbehorende groep',
        'alreadyJoinedTheirCommunity' => 'Je bent al lid van de bijbehorende groep',
        'joinedThisBar' => 'Je bent ingeschreven bij deze bar.',
        'leaveQuestion' => 'Weet je zeker dat je je wilt uitschrijven bij deze bar?',
        'leftThisBar' => 'Je bent uitgeschreven bij deze bar.',
        'protectedByCode' => 'Deze bar is beveiligd met een code. Vraag er naar bij de bar, of scan de bar QR-code als deze beschikbaar is.',
        'protectedByCodeFilled' => 'Deze bar is beveiligd met een code. We hebben de code voor je ingevuld.',
        'incorrectCode' => 'Verkeerde bar code.',
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

    /**
     * Privacy policy page.
     */
    'privacy' => [
        'title' => 'Privacy',
        'description' => 'Wanneer je onze service gebruikt, vertrouw je ons met je gegevens. We begrijpen dat dit een grote verantwoordelijkheid is.<br />De Privacy Policy hieronder is bedoeld om je te helpen begrijpen hoe we jouw dat gegevens beheren.',
        'onlyEnglishNote' => 'De Privacy Policy is alleen beschikbaar in het Engels, al geld het voor onze service in alle talen.',
        'questions' => 'Vragen?',
        'questionsDescription' => 'Als je verdere vragen hebt over onze Privacy Policy of over je privacy, neem gerust contact met ons op.',
    ],

    /**
     * Terms of Service page.
     */
    'terms' => [
        'title' => 'Voorwaarden',
        'description' => 'Wanneer je onze service gebruikt, ga je akkoord met onze servicevoowaarden zoals hieronder getoond.',
        'onlyEnglishNote' => 'De servicevoorwaarden zijn alleen beschikbaar in het Engels, al gelden ze voor onze service in alle talen.',
        'questions' => 'Questions?',
        'questions' => 'Vragen?',
        'questionsDescription' => 'Als je verdere vragen hebt over onze servicevoorwaarden, neem gerust contact met ons op.',
    ],
];
