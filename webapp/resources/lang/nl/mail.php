<?php

/**
 * Mail related translations.
 */
return [
    'signature' => [
        'caption' => 'Groet,|Groeten,|Dank,',
        'signoff' => '~ :app robot',
    ],

    /**
     * Email emails.
     */
    'email' => [
        /**
         * Email verification email.
         */
        'verify' => [
            'subject' => 'E-mailverificatie',
            'subjectRegistered' => ':app registratie & e-mailverificatie',
            'subtitle' => 'Je staat op het punt je e-mailadres te verifiëren.',
            'subtitleRegistered' => 'Je account is bijna klaar voor gebruik.',
            'registered' => 'Bedankt voor het registreren van een account.',
            'addNewEmail' => 'Je hebt zojuist een nieuw e-mailadres aan je account toegevoegd.',
            'verifyBeforeUseAccount' => 'Voordat je onze service volledig kunt gebruiken, moet je je e-mailadres verifiëren.',
            'verifyBeforeUseEmail' => 'Voordat je het op onze service kunt gebruiken, moet je het verifiëren.',
            'soon' => 'Doe dit zo snel mogelijk, de verificatie link verloopt **binnen :expire**.',
            'clickButtonToVerify' => 'Klik op de onderstaande knop om je e-mailadres te verifieren.',
            'verifyButton' => 'Verifiëer je e-mailadres',
            'mayIgnore' => 'Als je dit niet hebt aangevraagd, kun je dit email bericht veilig als niet verzonden beschouwen.',
            'manual' => 'Als de bovenstaande knop niet werkt, open de volgende link in je web browser:',
        ],

        /**
         * Email verified email.
         */
        'verified' => [
            'subject' => 'Starten met :app',
            'subtitle' => 'Allereerst, welkom!',
            'accountReady' => 'Je e-mailadres is zojuist geverifiëerd en je account is klaar voor gebruik.',
            'visitExplore' => 'Als je nog geen lid bent van een groep of bar, bezoek de Ontdek pagina om je in te schrijven en deze toe te voegen aan je persoonlijke dashboard.',
            'startUsingSeeDashboard' => 'Neem een kijkje op je gepersonaliseerde dashboard om te starten met :app.',
            'configureEmailPreferences' => 'Bekijk het e-mailvoorkeuren paneel om in te stellen hoevaak je e-mailupdates ontvangt van :app',
        ]
    ],

    /**
     * Authentication emails.
     */
    // TODO: use :app variable here, instead of Barbapappa
    'auth' => [
        /**
         * Session link email.
         */
        'sessionLink' => [
            'subject' => 'Inloggen op Barbapappa',
            'subtitle' => 'Druk op de knop om in te loggen op je Barbapappa account.',
            'soon' => 'De link verloopt **binnen :expire**, en kan één keer gebruikt worden.',
            'button' => 'Inloggen op Barbapappa',
            'mayIgnore' => 'Als je dit niet hebt aangevraagd, kun je dit email bericht veilig als niet verzonden beschouwen.',
            'manual' => 'Als de bovenstaande knop niet werkt, open de volgende link in je web browser:',
        ],
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
            'soon' => 'Do dit alsjeblieft zo snel mogelijk omdat de reset link **binnen :expire** verloopt.',
            'clickButtonToReset' => 'Klik op de onderstaande knop om je wachtwoord te resetten',
            'resetButton' => 'Je wachtwoord resetten',
            'manual' => 'Als de bovenstaande knop niet werkt, open de volgende link in je web browser:',
            'mayIgnore' => 'Als je geen wachtwoord reset hebt aangevraagd, kun je dit email bericht veilig als niet verzonden beschouwen.',
        ],

        /**
         * Password reset email.
         */
        'reset' => [
            'subject' => 'Wachtwoord aangepast',
            'forSecurity' => 'We informeren je even wegens veiligheidsredenen.',
            'useNewPassword' => 'Vanaf nu kun je je nieuwe wachtwoord gebruiken om in te loggen.',
            'noChangeThenReset' => 'Als je je wachtwoord niet zelf hebt aangepast, verander het alsjeblieft zo snel mogelijk met behulp van de volgende link:',
            'orContact' => 'Of neem direct [contact](:contact) op met het :app team wegens dit mogelijke veiligheidsincident.',
            'noChangeThenContact' => 'Als je dit bericht ontvangen hebt maar niet je wachtwoord hebt aangepast, neem zo snel mogelijk [contact](:contact) op met het :contact team wegens dit mogelijke veiligheidsincident.',
        ],

        /**
         * Password disabled email.
         */
        'disabled' => [
            'subject' => 'Wachtwoord uitgeschakeld',
            'forSecurity' => 'We informeren je even wegens veiligheidsredenen.',
            'noDisabledThenReset' => 'Als je je wachtwoord niet zelf hebt uitgeschakeld, stel dan alsjeblieft zo snel mogelijk een nieuw wachtwoord in met behulp van de volgende link:',
            'orContact' => 'Of neem zo snel mogelijk [contact](:contact) op met het :app team wegens dit mogelijke veiligheidsincident.',
            'noDisabledThenContact' => 'Als je dit bericht ontvangen hebt maar niet je wachtwoord hebt uitgeschakeld, neem zo snel mogelijk [contact](:contact) op met het :contact team wegens dit mogelijke veiligheidsincident.',
        ],
    ],

    'payment' => [
        'completed' => [
            'subject' => 'Betaling geaccepteerd',
            'subtitle' => 'Je hebt je portemonnee aangevuld.',
            'paymentReceived' => 'Je betaling is ontvangen. De betaling is verwerkt en geaccepteerd.',
            'amountReadyToUse' => 'Het bedrag is nu beschikbaar in je portemonnee en kan direct gebruikt worden.',
        ],
        'failed' => [
            'subject' => 'Betaling gefaald',
            'subtitle' => 'Je portemonnee storting was niet succesvol.',
            'stateFailed' => 'Een betaling die je gestart hebt kon niet succesvol afgerond worden omdat de betaling is gefaald. Neem alsjeblieft contact met ons op als je denkt dat dit een fout is.',
            'stateRevoked' => 'Een betaling die je gestart hebt kon niet succesvol afgerond worden omdat de betaling is ingetrokken. Neem alsjeblieft contact met ons op als je denkt dat dit een fout is.',
            'stateRejected' => 'Een betaling die je gestart hebt kon niet succesvol afgerond worden omdat de betaling is afgekeurd. Neem alsjeblieft contact met ons op als je denkt dat dit een fout is.',
        ],
    ],

    /**
     * Update emails.
     */
    'update' => [
        /**
         * Balance update email.
         */
        'balance' => [
            'subject' => 'Saldo update van Barbapappa',
            'subtitle' => 'Hier volgt een overzicht van je Barbapappa portemonnees.',
            'pleaseTopUp' => 'Vul portemonnees met een negatief saldo nu aan, en zorg er altijd voor dat je genoeg beschikbaar hebt voor de komende periode.',
            'noUpdateZeroBalance' => 'Zodra het saldo van al je portemonnees nul is, zul je geen periodieke updates ontvangen.',
        ],
    ],

    /**
     * Balance import emails.
     */
    'balanceImport' => [
        'update' => [
            'subject' => 'Saldo update',
            'subtitle' => 'Hier is een saldo update voor je op :economy.',
            'subtitleWithBar' => 'Hier is een saldo update voor je bij :name op :economy.',
            'joinBarDescription' => 'Deze bar beheerd betalingen met Barbapappa. Klik op de knop hieronder om toe te treden tot :name in Barbapappa.',
            'joinBarButton' => ':name toetreden',
            'pleaseTopUp' => 'Vul een negatief saldo nu aan, en zorg er altijd voor dat je genoeg beschikbaar hebt voor de komende periode.',
            'noUpdateZeroBalance' => 'Zodra je saldo nul is, zul je geen updates ontvangen.',
        ],
    ],
];
