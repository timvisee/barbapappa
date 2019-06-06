<?php

/**
 * Mail related translations.
 */
return [
    'signature' => [
        'caption' => 'Groet,|Groeten,|Dank,',
        'signoff' => '~ A :app robot',
    ],

    /**
     * Email emails.
     */
    'email' => [
        /**
         * Email verification email.
         */
        'verify' => [
            'subject' => 'E-mail verificatie',
            'subjectRegistered' => 'Registratie & e-mail verificatie',
            'subtitle' => 'Je staat op het punt je e-mailadres te verifiëren.',
            'subtitleRegistered' => 'Je account is bijna klaar voor gebruik.',
            'registered' => 'Bedankt voor het registreren van een account.',
            'addNewEmail' => 'Je hebt zojuist een nieuw e-mailadres aan je account toegevoegd.',
            'verifyBeforeUseAccount' => 'Voordat je onze service kunt gebruiken, moet je je e-mailadres verifiëren.',
            'verifyBeforeUseEmail' => 'Voordat je het op onze service kunt gebruiken, moet je het verifiëren.',
            'soon' => 'Doe dit alsjeblieft zo snel mogelijk, de verificatie link verloopt **binnen :hours uur**.',
            'clickButtonToVerify' => 'Klik op de onderstaande knop om je e-mailadres te verifieren.',
            'verifyButton' => 'Verifiëer je e-mailadres',
            'manual' => 'Als de bovenstaande knop niet werkt kun je je e-mailadres handmatig verifiëren met behulp van de volgende link en token.',
        ],

        /**
         * Email verified email.
         */
        'verified' => [
            'subject' => 'Starten met :app',
            'subtitle' => 'Allereerst, welkom bij de club!',
            'accountReady' => 'Je e-mailadres is zojuist geverifiëerd en je account is klaar voor gebruik.',
            'startUsingSeeDashboard' => 'Neem een kijkje op je gepersonaliseerde dashboard om te starten met :app.',
            'configureEmailPreferences' => 'Bekijk het e-mailvoorkeuren paneel om in te stellen hoevaak je e-mailupdates ontvangt van :app',
        ]
    ],

    /**
     * Password emails.
     */
    'password' => [
        /**
         * Password request email.
         */
        'request' => [
            'subject' => 'Wachtwoord reset aanvraag',
            'subtitle' => 'We helpen je een nieuw wachtwoord in te stellen.',
            'requestedReset' => 'Je hebt zojuist een nieuw wachtwoord aangevraagd.',
            'visitResetPage' => 'Bezoek simpelweg de wachtwoord reset pagina en vul je nieuwe wachtwoord in.',
            'soon' => 'Do dit alsjeblieft zo snel mogelijk omdat de reset link **binnen :hours uur** verloopt.',
            'clickButtonToReset' => 'Klik op de onderstaande knop om je wachtwoord te resetten',
            'resetButton' => 'Je wachtwoord resetten',
            'manual' => 'Als de bovenstaande knop niet werkt, kun je de volgende link en token gebruiken om je wachtwoord handmatig te resetten.',
            'notRequested' => 'Als je geen wachtwoord reset hebt aangevraagd, kun je dit email bericht als niet verzonden beschouwen.',
        ],

        /**
         * Password reset email.
         */
        'reset' => [
            'subject' => 'Wachtwoord aangepast',
            'forSecurity' => 'We informeren je even wegens veiligheidsredenen.',
            'useNewPassword' => 'Vanaf nu kun je je nieuwe wachtwoord gebruiken om in te loggen.',
            'noChangeThenReset' => 'Als je je wachtwoord niet zelf hebt aangepast, verander het alsjeblieft zo snel mogelijk met behulp van de volgende link en token.',
            'orContact' => 'Of neem zo snel mogelijk [contact](:contact) op met het :app team wegens dit mogelijke veiligheidsincident.',
            'noChangeThenContact' => 'Als je dit bericht ontvangen hebt maar niet je wachtwoord hebt aangepast, neem zo snel mogelijk [contact](:contact) op met het :contact team wegens dit mogelijke veiligheidsincident.',
        ]
    ],

    'payment' => [
        'completed' => [
            'subject' => 'Betaling geaccepteerd',
            'subtitle' => 'Je hebt je portemonnee aangevuld.',
            'paymentReceived' => 'Je betaling is ontvangen. De betaling is verwerkt en geaccepteerd.',
            'amountReadyToUse' => 'Het bedrag is nu beschikbaar in je portemonnee en kan direct gebruikt worden.',
        ],
    ],
];
