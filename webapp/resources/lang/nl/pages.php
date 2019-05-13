<?php

/**
 * Pages and their names.
 */
return [
    'pages' => 'Pagina\'s',
    'index' => 'Hoofdpagina',
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
     * Last page.
     */
    'last' => [
        'title' => 'Bezoek laatste',
        'noLast' => 'Je hebt nog geen bar bezocht, bezoek er nu een!',
    ],

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
        'name' => 'Profiel aanpassen',
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
            'cannotDeleteMustHaveOne' => 'Je kunt dit e-mailadres niet verwijderen, je moet tenminste één adres hebben.',
            'cannotDeleteMustHaveVerified' => 'Je kunt dit e-mailadres niet verwijderen, je moet tenminste één geverifiëerd adres hebben.',
            'deleted' => 'Het e-mailadres is verwijderd.',
            'deleteQuestion' => 'Weet je zeker dat je dit e-mailadres wilt verwijderen?',
        ],
        'addEmail' => [
            'title' => 'E-mailadres toevoegen',
            'description' => 'Vul het e-mailadres in dat je wilt toevoegen.',
            'added' => 'E-mailadres toegevoegd. Er is een verificatie-e-mail gestuurd.',
        ],
        'backToAccount' => 'Terug naar account',
    ],

    /**
     * Community pages.
     */
    'community' => [
        'yourCommunities' => 'Jouw groepen',
        'noCommunities' => 'Geen groepen beschikbaar...',
        'viewCommunity' => 'Bekijk groep',
        'viewCommunities' => 'Bekijk groepen',
        'createCommunity' => 'Groep aanmaken',
        'editCommunity' => 'Groep aanpassen',
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
        'namePlaceholder' => 'Viking groep',
        'slugDescription' => 'Een URL-pad laat je een makkelijk te onthouden link creëren om de groeps pagina te bezoeken, door een kort trefwoord te definieren.',
        'slugDescriptionExample' => 'Dit kan de groepslink versimpelen:',
        'slugPlaceholder' => 'vikings',
        'slugFieldRegexError' => 'Dit trefword moet met een alfabetisch karakter beginnen.',
        'codeDescription' => 'Met een groepscode voorkom je dat willekeurige gebruikers zich inschrijven bij deze groep. Om in te schrijven moeten gebruikers de gespecificeerde code invullen.',
        'visibleDescription' => 'Zichtbaar in lijst met groepen.',
        'publicDescription' => 'Sta zelf inschrijven zonder wachtwoord toe.',
        'created' => 'De groep is aangemaakt.',
        'updated' => 'De groep is aangepast.',
        'economy' => 'Economie',
        'goTo' => 'Ga naar groep',
        'backToCommunity' => 'Terug naar groep',
    ],

    /**
     * Community member pages.
     */
    'communityMembers' => [
        'title' => 'Groepsleden',
        'description' => 'Op deze pagina zie je een overzicht van alle groepsleden.<br>Als je op een lid klikt kun je dit lid verwijderen, of zijn/haar rol aanpassen.',
        'noMembers' => 'Deze groep heeft geen leden...',
        'memberSince' => 'Lid sinds',
        'lastVisit' => 'Laatste bezoek',
        'deleteQuestion' => 'Je staat op het punt dit lid te verwijderen van deze groep. Weet je zeker dat je door wilt gaan?',
        'memberRemoved' => 'Het lid is verwijderd.',
        'memberUpdated' => 'Lid aanpassingen opgeslagen.',
        'incorrectMemberRoleWarning' => 'Het toewijzen van de verkeerde rol aan een gebruiker kan voor serieuze beveiligingsproblemen zorgen.',
    ],

    /**
     * Community economy pages.
     */
    'economies' => [
        'title' => 'Economieën',
        'description' => 'Op deze pagina zie je een overzicht van alle economieën binnen deze groep.<br>Klik op een economie om deze te beheren, of maak een nieuwe aan voor een nieuwe bar.',
        'manage' => 'Economieën beheren',
        'noEconomies' => 'Deze groep heeft geen economieën...',
        'createEconomy' => 'Economie aanmaken',
        'economyCreated' => 'De economie is aangemaakt. Voeg nu een valuta toe.',
        'deleteQuestion' => 'Je staat op het punt deze economie te verwijderen van deze groep. Weet je zeker dat je door wilt gaan?',
        'economyDeleted' => 'De economie is verwijderd.',
        'economyUpdated' => 'Economie aanpassingen opgeslagen.',
        'namePlaceholder' => 'Hoofdeconomie',
        'all' => 'Alle economieën',
        'backToEconomy' => 'Terug naar economie',
    ],

    /**
     * Community economy currency pages.
     */
    'currencies' => [
        'title' => 'Ingeschakelde valuta\'s',
        'description' => 'Op deze pagina zie je een overzicht van de ingeschakelde valuta\'s voor deze economie.<br>Tenminste één valuta moet ingeschakeld zijn om de economie te kunnen gebruiken voor een bar.<br>Voeg een nieuwe valuta toe, of klik op een valuta om deze te beheren.',
        'change' => 'Valuta aanpassen',
        'noCurrencies' => 'Deze economie heeft geen valuta\'s',
        'createCurrency' => 'Valuta toevoegen',
        'currencyCreated' => 'Valuta aangemaakt',
        'deleteQuestion' => 'Je staat op het punt deze valuta te verwijderen van deze economie. Weet je zeker dat je door wilt gaan?',
        'deleteVoidNotice' => 'Als je deze valuta verwijderd, zullen alle ingestelde prijzen in deze valuta verwijderd worden in bars die gebruik maken van deze economie.<br>Je kunt de valuta ook tijdelijk uitschakelen door deze aan te passen, zodat je de valuta later weer kunt inschakelen zonder alle prijzen opnieuw in te vullen.',
        'currencyDeleted' => 'De valuta is verwijderd.',
        'currencyUpdated' => 'Valuta aanpassingen opgeslagen.',
        'enabledTitle' => 'Valuta inschakelen',
        'enabledDescription' => 'Stel in of deze valuta in bars is ingeschakeld die gebruik maken van deze economie. Barleden kunnen geen producten kopen met deze valuta als uitgeschakeld, en moeten een ander valuta gebruiken, of wachten totdat de valuta weer is ingeschakeld.',
        'changeCurrencyTitle' => 'Valuta aanpassen?',
        'changeCurrencyDescription' => 'De valuta kan niet direct worden aangepast. Om de valuta aan te passen moet je deze configuratie verwijderen, en een nieuwe valuta toevoegen aan deze economie.',
        'allowWallets' => 'Portemonnee maken toestaan',
        'allowWalletsDescription' => 'Met deze optie stel je in of barleden een nieuwe persoonlijke portemonnee aan kunnen maken voor deze valuta. Huidige portemonnees blijven altijd bestaan.',
        'noCurrenciesToAdd' => 'Er zijn geen valutas die je kunt toevoegen. Vraag de website administrator om een valuta te configureren.',
        'noMoreCurrenciesToAdd' => 'Er zijn geen andere valutas om toe te voegen.',
        'manage' => 'Beheer economie valutas',
    ],

    /**
     * Product pages.
     */
    'products' => [
        'title' => 'Producten',
        'noProducts' => 'Geen producten...',
        'noProductsInEconomy' => 'Er zijn nog geen producten aan deze economie toegevoegd...',
        'createProduct' => 'Nieuw product',
        'editProduct' => 'Product aanpassen',
        'created' => 'Het product is aangemaakt.',
        'changed' => 'Product aanpassingen opgeslagen.',
        'deleteQuestion' => 'Je staat op het punt dit product te verwijderen. Weet je zeker dat je door wilt gaan?',
        'deleted' => 'Het product is verwijderd.',
        'namePlaceholder' => 'Luxe Sap',
        'enabledDescription' => 'Ingeschakeld, kan gekocht worden',
        'archivedDescription' => 'Gearchiveerd, verborgen van producten lijst',
        'prices' => 'Prijzen',
        'pricesDescription' => 'Configureer de prijzen voor dit product in de velden hieronder voor de valutas die je wilt ondersteunen.',
        'localizedNames' => 'Gelokaliseerde namen',
        'localizedNamesDescription' => 'Configureer de gelokaliseerde namen voor dit product in de velden hieronder als het verschilt met de hoofdnaam.',
        'search' => 'Zoek producten',
        'type' => [
            'normal' => 'Normaal',
            'custom' => 'Aangepast',
        ],
    ],

    /**
     * Wallet pages.
     */
    'wallets' => [
        'title' => 'Portemonnees',
        'description' => 'Klik op één van je portemonnees om deze te beheren, of maak een nieuwe aan.',
        'walletEconomies' => 'Portemonee economieën',
        'yourWallets' => 'Jouw portemonnees',
        '#wallets' => '{0} Geen portemonnees|{1} 1 portemonnee|[2,*] :count portemonnees',
        'economySelectDescription' => 'Portemonnees voor deze groep zijn gegroepeerd per economie.<br>Selecteer een economie om je portemonnees te beheren.',
        'noWallets' => 'Je hebt nog geen portemonnees...',
        'namePlaceholder' => 'Mijn persoonlijke portefeuille',
        'createWallet' => 'Portemonnee aanmaken',
        'walletCreated' => 'De portemonnee is aangemaakt.',
        'walletUpdated' => 'Portemonnee aanpassingen opgeslagen.',
        'deleteQuestion' => 'Je staat op het punt deze portemonnee te verwijderen. Weet je zeker dat je door wilt gaan?',
        'cannotDeleteNonZeroBalance' => 'Om deze portemonnee te verwijderen moet het saldo precies :zero zijn.',
        'walletDeleted' => 'De portemonnee is verwijderd.',
        'cannotCreateNoCurrencies' => 'Je kunt nu geen portemonnee aanmaken. De groep administrator heeft geen valuta geconfigureerd waarbij dit is toegestaan.',
        'all' => 'Alle portemonnees',
        'view' => 'Portemonnee bekijken',
        'backToWallet' => 'Terug naar portemonnee',
        'walletTransactions' => 'Portemonee transacties',
    ],

    /**
     * Transaction pages.
     */
    'transactions' => [
        'title' => 'Transacties',
        'details' => 'Transactie details',
        'last#' => '{0} Laatste transacties|{1} Laatste transactie|[2,*] Laatste :count transacties',
        'backToTransaction' => 'Terug naar transactie',
        'toTransaction' => 'naar transactie',
        'fromTransaction' => 'van transactie',
        'referencedTo#' => '{0} Gerefereerd aan geen transacties|{1} Gerefereerd aan transactie|[2,*] Gerefereerd aan :count transacties',
        'referencedBy#' => '{0} Gerefereerd door geen transacties|{1} Gerefereerd door transactie|[2,*] Gerefereerd door :count transacties',
        'cannotUndo' => 'Deze transactie kan niet ongedaan gemaakt worden.',
        'undone' => 'De transactie is ongedaan gemaakt.',
        'undoTransaction' => 'Transactie ongedaan maken',
        'undoQuestion' => 'Je staat op het punt deze transactie ongedaan te maken. Weet je zeker dat je door wilt gaan?',
        'state' => [
            'pending' => 'In afwachting',
            'processing' => 'Bezig met verwerken',
            'success' => 'Voltooid',
            'failed' => 'Mislukt',
        ],
        'descriptions' => [
            'fromWalletToProduct' => 'Betaling voor product(en) met portemonnee',
            'toProduct' => 'Betaling voor producten',
            'fromPaymentToWallet' => 'Storting naar portemonnee vanaf externe rekening',
            'fromWalletToWallet' => 'Overdracht tussen portemonnees',
            'toWallet' => 'Storting naar portemonnee',
            'fromWallet' => 'Opname vanaf portemonnee',
        ],
    ],

    /**
     * Mutation pages.
     */
    'mutations' => [
        'title' => 'Mutaties',
        'details' => 'Mutatie details',
        'from#' => '{0} Van geen mutaties|{1} Van 1 mutatie|[2,*] Van :count mutatie',
        'to#' => '{0} Naar geen mutaties|{1} Naar 1 mutatie|[2,*] Naar :count mutatie',
        'dependsOn#' => '{0} Afhankelijk van geen mutaties|{1} Afhankelijk van mutatie|[2,*] Afhankelijk van :count mutaties',
        'dependentBy#' => '{0} Afhankelijk door geen mutatiese |{1} Afhankelijk door mutatie|[2,*] Afhankelijk door :count mutaties',
        'state' => [
            'pending' => 'In afwachting',
            'processing' => 'Bezig met verwerken',
            'success' => 'Voltooid',
            'failed' => 'Mislukt',
        ],
        'types' => [
            'magic' => 'Speciale mutatie',
            'walletTo' => 'Storting naar portemonnee',
            'walletFrom' => 'Betaald met portemonnee',
            'walletToDetail' => 'Storting naar :wallet',
            'walletFromDetail' => 'Betaald met :wallet',
            'productTo' => 'Betaald voor product(en)',
            'productFrom' => 'Geld ontvangen voor product(en)',
            'productToDetail' => 'Betaald voor :products',
            'productFromDetail' => 'Geld ontvangen voor :products',
            'paymentTo' => 'Opname naar extern account',
            'paymentFrom' => 'Storting vanaf extern account',
        ],
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
        'createBar' => 'Bar aanmaken',
        'editBar' => 'Bar aanpassen',
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
        'namePlaceholder' => 'Viking bar',
        'slugDescription' => 'Een URL-pad laat je een makkelijk te onthouden link creëren om de bar pagina te bezoeken, door een kort trefwoord te definieren.',
        'slugDescriptionExample' => 'Dit kan de barlink versimpelen:',
        'slugPlaceholder' => 'viking',
        'slugFieldRegexError' => 'Dit trefword moet met een alfabetisch karakter beginnen.',
        'codeDescription' => 'Met een barcode voorkom je dat willekeurige gebruikers zich inschrijven bij deze bar. Om in te schrijven moeten gebruikers de gespecificeerde code invullen.',
        'visibleDescription' => 'Zichtbaar in lijst met bars.',
        'publicDescription' => 'Sta zelf inschrijven zonder wachtwoord toe.',
        'created' => 'De bar is aangemaakt.',
        'updated' => 'De bar is aangepast.',
        'mustCreateEconomyFirst' => 'Voor een nieuwe bar moest je eerst een economie aanmaken.',
        'backToBar' => 'Terug naar bar',
        'boughtProductForPrice' => ':product gekocht voor :price',
    ],

    /**
     * Bar member pages.
     */
    'barMembers' => [
        'title' => 'Bar leden',
        'description' => 'Op deze pagina zie je een overzicht van alle bar leden.<br>Als je op een lid klikt kun je dit lid verwijderen, of zijn/haar rol aanpassen.',
        'noMembers' => 'Deze bar heeft geen leden...',
        'memberSince' => 'Lid sinds',
        'lastVisit' => 'Laatste bezoek',
        'deleteQuestion' => 'Je staat op het punt dit lid te verwijderen van deze bar. Weet je zeker dat je door wilt gaan?',
        'memberRemoved' => 'Het lid is verwijderd.',
        'memberUpdated' => 'Lid aanpassingen opgeslagen.',
        'incorrectMemberRoleWarning' => 'Het toewijzen van de verkeerde rol aan een gebruiker kan voor serieuze beveiligingsproblemen zorgen.',
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
        'description' => 'Wanneer je onze service gebruikt, vertrouw je ons met je gegevens. We begrijpen dat dit een grote verantwoordelijkheid is.<br />Het privacybeleid (Privacy Policy) hieronder is bedoeld om je te helpen begrijpen hoe we jouw gegevens beheren.',
        'onlyEnglishNote' => 'Het privacybeleid (Privacy Policy) is alleen beschikbaar in het Engels, maar is actief voor alle gebruikstalen.',
        'questions' => 'Vragen?',
        'questionsDescription' => 'Als je verdere vragen hebt over ons privacybeleid (Privacy Policy) of over jouw privacy, neem gerust contact met ons op.',
    ],

    /**
     * Terms of Service page.
     */
    'terms' => [
        'title' => 'Voorwaarden',
        'description' => 'Wanneer je onze service gebruikt, ga je akkoord met onze servicevoowaarden (Terms of Service) zoals hieronder getoond.',
        'onlyEnglishNote' => 'De servicevoorwaarden (Terms of Service) zijn alleen beschikbaar in het Engels, maar zijn actief voor alle gebruikstalen.',
        'questions' => 'Vragen?',
        'questionsDescription' => 'Als je verdere vragen hebt over onze servicevoorwaarden (Terms of Service), neem gerust contact met ons op.',
    ],

    /**
     * License page.
     */
    'license' => [
        'title' => 'Licentie',
        'description' => 'Het BARbapAPPa software project is uitgebracht onder de GNU GPL-3.0 licentie (License). Deze licentie beschrijft wat wel en niet is toegestaan met de broncode van dit project.<br />Lees de volledige licentie hieronder, of check de licentie samenvatting voor een snel overzicht.',
        'onlyEnglishNote' => 'De licentie (License) is alleen beschikbaar in het Engels, maar is actief voor alle gebruikstalen.',
        'questions' => 'Vragen?',
        'questionsDescription' => 'Als je verdere vragen hebt over de gebruikte licentie (License), neem gerust contact met ons op. Je kunt de licentie ook bekijken in platte text leesbaar op elk willekeurig apparaat.',
        'plainTextLicense' => 'Licentie in platte text',
        'licenseSummary' => 'Licentie samenvatting (Engels)',
    ],

    /**
     * No permission page.
     */
    'noPermission' => [
        'title' => 'Je hoort hier niet te zijn...',
        'description' => 'Je hebt een verkeerde afslag genomen.<br />Je hebt niet genoeg rechten voor deze inhoud.',
        'notLoggedIn' => 'Niet ingelogd',
        'notLoggedInDescription' => 'Je bent op dit moment niet ingelogd. Log in om jouw juiste rechten te verkrijgen.',
    ],
];
