<?php

/**
 * Pages and their names.
 */
return [
    'pages' => 'Pagina\'s',
    'emailPreferences' => 'E-mailvoorkeuren',
    // TODO: move to misc?
    'communities' => 'Groepen',
    // TODO: move to misc?
    'bars' => 'Bars',
    'account' => 'Account',
    'yourAccount' => 'Jouw account',
    'requestPasswordReset' => 'Wachtwoord reset aanvragen',
    'changePassword' => 'Wachtwoord veranderen',
    'changePasswordDescription' => 'Vul de onderstaande velden in om je wachtwoord te veranderen.',

    /**
     * App pages.
     */
    'app' => [
        'manageApp' => 'App beheren',
    ],

    /**
     * Index page.
     */
    'index' => [
        'title' => 'Hoofdpagina',
        'emailAndContinue' => 'Vul je e-mailadres in om in te loggen of te registreren.',
        'backToIndex' => 'Terug naar hoofdpagina',
    ],

    /**
     * Dashboard page.
     */
    'dashboard' => [
        'title' => 'Dashboard',
        'yourPersonalDashboard' => 'Je persoonlijke dashboard',
        'noBarsOrCommunities' => 'Geen bars of groepen',
        'nothingHereNoMemberUseExploreButtons' => 'Er is hier niks om te zien omdat je nog geen lid bent van een bar of groep. Vind die van jouw met de onderstaande knoppen.',
    ],

    /**
     * Last page.
     */
    'last' => [
        'title' => 'Terug naar bar',
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
            'emails' => 'E-mailadressen',
            'yourEmails' => 'Jouw e-mailadressen',
            'unverifiedEmails' => 'Ongeverifiëerde e-mails',
            'verifyEmails' => 'Verifiëer e-mails',
            'unverifiedDescription' => 'Deze pagina toont jouw e-mailadressen die nog niet geverifiëerd zijn. Druk op de onderstaande blauwe knop om het verificatieproces te starten.',
            'resendVerify' => 'Verificatie opnieuw versturen',
            'unverified#' => '{0} Geen ongeverifiëerde e-mails|{1} Ongeverifiëerde e-mail|[2,*] :count ongeverifiëerde e-mails',
            'verify#' => '{0} Verifiëer geen e-mailadres|{1} Verifiëer e-mailadres|[2,*] Verifiëer :count e-mailadressen',
            'verifiedDescription' => 'We hebben je een verificatie e-mail gestuurd naar de onderstaande e-mailadressen. Klik op de link in het bericht om het verificatieproces te voltooien. Zodra je al je e-mailadressen hebt geverifieerd kun je op de knop onder aan de pagina drukken om verder te gaan.',
            'iVerifiedAll' => 'Ik heb alles geverifiëerd',
            'someStillUnverified' => 'Nog niet al je e-mailadressen zijn geverifiëerd. Zie de onderstaande lijst. Controleer je e-mail inbox voor een verificatie bericht.',
            'verifySent' => 'Een nieuwe verificatie-e-mail zal zo spoedig mogelijk verzonden worden.',
            'alreadyVerified' => 'Dit e-mailadres is al geverifiëerd.',
            'allVerified' => 'Al je e-mailadressen zijn geverifiëerd.',
            'cannotDeleteMustHaveOne' => 'Je kunt dit e-mailadres niet verwijderen, je moet ten minste één adres hebben.',
            'cannotDeleteMustHaveVerified' => 'Je kunt dit e-mailadres niet verwijderen, je moet ten minste één geverifiëerd adres hebben.',
            'deleted' => 'Het e-mailadres is verwijderd.',
            'deleteQuestion' => 'Weet je zeker dat je dit e-mailadres wilt verwijderen?',
            'backToEmails' => 'Terug naar e-mails',
        ],
        'addEmail' => [
            'title' => 'E-mailadres toevoegen',
            'description' => 'Vul het e-mailadres in dat je wilt toevoegen.',
            'added' => 'E-mailadres toegevoegd. Er is een verificatie-e-mail gestuurd.',
            'cannotAddMore' => 'Je kunt niet nog een e-mailadres toevoegen aan je account. Verwijder eerst een adres om een nieuwe toe te voegen.',
        ],
        'backToAccount' => 'Terug naar account',
    ],

    /**
     * Explore pages.
     */
    'explore' => [
        'title' => 'Ontdek',
        'exploreBars' => 'Ontdek bars',
        'exploreCommunities' => 'Ontdek groepen',
        'exploreBoth' => 'Ontdek groepen & bars',
    ],

    /**
     * Community pages.
     */
    'community' => [
        'yourCommunities' => 'Jouw groepen',
        'noCommunities' => 'Geen groepen beschikbaar...',
        'viewCommunity' => 'Bekijk groep',
        'viewCommunities' => 'Bekijk groepen',
        'visitCommunity' => 'Bezoek groep',
        'createCommunity' => 'Groep aanmaken',
        'editCommunity' => 'Groep aanpassen',
        'deleteCommunity' => 'Groep verwijderen',
        'join' => 'Inschrijven',
        'yesJoin' => 'Ja, inschrijven',
        'joined' => 'Ingeschreven',
        'youAreJoined' => 'Je bent ingeschreven bij deze groep.',
        'leave' => 'Uitschrijven',
        'notJoined' => 'Niet ingeschreven',
        'hintJoin' => 'Je maakt nog geen deel uit van deze groep.',
        'joinQuestion' => 'Wil je je bij deze groep inschrijven?',
        'joinedThisCommunity' => 'Je bent ingeschreven bij deze groep.',
        'cannotSelfEnroll' => 'Je kunt jezelf niet inschrijven voor deze groep, de functie is uitgeschakeld.',
        'leaveQuestion' => 'Weet je zeker dat je je wilt uitschrijven bij deze groep?',
        'leftThisCommunity' => 'Je bent uitgeschreven bij deze groep.',
        'cannotLeaveStillBarMember' => 'Je kunt deze groep niet verlaten, omdat je nog lid bent van een bar in deze groep.',
        'protectedByCode' => 'Deze groep is beveiligd met een code. Vraag er naar bij de groep, of scan de groep QR-code als deze beschikbaar is.',
        'protectedByCodeFilled' => 'Deze groep is beveiligd met een code. We hebben de code voor je ingevuld.',
        'incorrectCode' => 'Verkeerde groep code.',
        'namePlaceholder' => 'Viking groep',
        'descriptionPlaceholder' => 'Welkom bij de Viking groep!',
        'slugDescription' => 'Een URL-pad laat je een makkelijk te onthouden link creëren om de groeps pagina te bezoeken, door een kort trefwoord te definieren.',
        'slugDescriptionExample' => 'Dit kan de groepslink versimpelen:',
        'slugPlaceholder' => 'vikings',
        'slugFieldRegexError' => 'Dit trefword moet met een alfabetisch karakter beginnen.',
        'codeDescription' => 'Met een groepscode voorkom je dat willekeurige gebruikers zich inschrijven bij deze groep. Om in te schrijven moeten gebruikers de gespecificeerde code invullen.',
        'showExploreDescription' => 'Toon op publieke \'Ontdek groepen\' pagina',
        'selfEnrollDescription' => 'Sta zelf inschrijven toe (met code als gespecificeerd)',
        'joinAfterCreate' => 'Schrijf je in bij de groep na het aanmaken',
        'created' => 'De groep is aangemaakt.',
        'updated' => 'De groep is aangepast.',
        'economy' => 'Economie',
        'goTo' => 'Ga naar groep',
        'backToCommunity' => 'Terug naar groep',
        'noDescription' => 'Deze groep heeft geen beschrijving',
        'communityInfo' => 'Groep informatie',
        'manageCommunity' => 'Groep beheren',
        'inCommunity' => 'in groep',
        'deleted' => 'De groep is verwijderd.',
        'deleteQuestion' => 'Je staat op het punt deze groep permanent te verwijderen. Alle leden waaronder jezelf zullen toegang tot de groep verliezen. Alle bars, economieën, lid portemonnees, producten en gerelateerde entiteiten die worden gebruikt in deze groep zullen ook verwijderd worden. Weet je zeker dat je door wilt gaan?',
        'deleteBlocked' => 'Je staat op het punt deze groep permanent te verwijderen. Verwijder eerst de entiteiten in de lijst hieronder voordat je verder kunt gaan met het verwijderen van deze groep.',
        'exactCommunityNameVerify' => 'Exacte naam van groep om te verwijderen (Verificatie)',
        'incorrectNameShouldBe' => 'Incorrecte naam, zou moeten zijn: \':name\'',
        'cannotDeleteDependents' => 'Deze groep kan niet verwijderd worden, omdat er nog entiteiten zijn die afhankelijk zijn en niet zomaar verwijderd kunnen worden.',
        'generatePoster' => 'Creëer groepsposter',
        'generatePosterDescription' => 'Creëer een poster voor deze groep om aan de muur te hangen. Bezoekers kunnen dan gemakklijk gebruik kunnen maken van :app en kunnen lid worden van deze groep door een QR code te scannen met hun mobiele telefoon.',
        'showCodeOnPoster' => 'Toon code om lid te worden op de poster',
        'posterBarPreferred' => 'Het is vaak de voorkeur om een poster voor een bar te maken in plaats van voor een groep, omdat gebruikers die lid worden van een groep geen toegang hebben tot het kopen van producten zonder eerst ook lid te worden van een bar. Bezoek de beheers hub van een specifieke bar om daar een poster voor te creëren.',
        'poster' => [
            'thisCommunityUses' => 'Deze groep gebruikt',
            'toDigitallyManage' => 'om digitaal betalingen en inventaris te beheren voor consumpties:',
            'scanQr' => 'scan de QR code met je telefoon, word lid en doe een aankoop',
            'orVisit' => 'Of bezoek',
        ],
        'links' => [
            'title' => 'Handige links',
            'description' => 'Deze pagina geeft een lijst handige deelbare links voor deze groep. Je kunt deze links bijvoorbeeld delen via e-mail of printen op een poster. Sommige van deze links maken het mogelijk om gebruikers naar specifieke normaal verborgen paginas of doelen te sturen.<br><br>Let er alsjeblieft op dat sommige links veranderen bij het aanpassen van groep instellingen, en sommige links bevatten geheime stukjes.',
            'linkCommunity' => 'Groep hoofdpagina',
            'linkCommunityAction' => 'Bezoek :community',
            'linkJoinCommunity' => 'Nodig nieuwe gebruiker uit',
            'linkJoinCommunityAction' => 'Wordt lid van :community',
            'linkJoinCommunityCode' => 'Nodig nieuwe gebruiker uit (met code)',
            'linkJoinCommunityCodeAction' => 'Wordt lid van :community',
        ],
        'checklist' => 'Groep checklist',
    ],

    /**
     * Community membership page.
     */
    'communityMember' => [
        'title' => 'Lidmaatschap',
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
        'ownRoleDowngradeWarning' => 'Bij het verlagen van je eigen rol verlies je waarschijnlijk beheerstoegang tot deze groep.',
        'confirmRoleChange' => 'Bevestig rol aanpassing voor groepslid',
        'confirmSelfDelete' => 'Bevestig om jezelf als groepslid uit te schrijven, waardoor je je rol verliest',
        'cannotDemoteLastManager' => 'Je kunt de rol voor het laatste groepslid met deze (of een meer permissieve) management rol niet degraderen.',
        'cannotEditMorePermissive' => 'Je kunt een groepslid met een meer permissieve rol dan jezelf niet aanpassen.',
        'cannotSetMorePermissive' => 'Je kunt geen meer permissieve rol voor een groepslid instellen dan je eigen rol.',
        'cannotDeleteLastManager' => 'Je kunt het laatste groepslid met deze (of een meer permissieve) management rol niet uitschrijven.',
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
        'editEconomy' => 'Economie aanpassen',
        'deleteEconomy' => 'Economie verwijderen',
        'economyCreated' => 'De economie is aangemaakt. Voeg nu een valuta toe.',
        'deleteQuestion' => 'Je staat op het punt deze economie te verwijderen van deze groep. Weet je zeker dat je door wilt gaan?',
        'deleteBlocked' => 'Je staat op het punt deze economie permanent te verwijderen. Verwijder eerst de entiteiten in de lijst hieronder voordat je verder kunt gaan met het verwijderen van deze economie.',
        'cannotDeleteDependents' => 'Deze economie kan niet verwijderd worden, omdat er nog entiteiten zijn die afhankelijk zijn en niet zomaar verwijderd kunnen worden.',
        'economyDeleted' => 'De economie is verwijderd.',
        'economyUpdated' => 'Economie aanpassingen opgeslagen.',
        'namePlaceholder' => 'Hoofdeconomie',
        'backToEconomy' => 'Terug naar economie',
        'backToEconomies' => 'Terug naar economieën',
        'inEconomy' => 'in economie',
        'noWalletsInEconomy' => 'Er zijn geen portemonnees in deze economie.',
        'walletOperations' => 'Portemonnee operaties',
        'zeroAllWallets' => 'Alle portemonnees naar nul',
        'zeroAllWalletsQuestion' => 'Je staat op het punt om het saldo in de portemonnees van alle gebruikers in deze economie naar nul te resetten. Zorg ervoor dat je alle gewenste data exporteert voordat je deze actie doorvoert. Weet je zeker dat je door wilt gaan?',
        'zeroAllWalletsDescription' => 'Saldo reset door administrator',
        'walletsZeroed' => 'Alle portemonnees zijn naar nul gereset',
        'deleteAllWallets' => 'Verwijder alle portemonnees',
        'deleteAllWalletsQuestion' => 'Je staat op het punt om de portemonnees van alle leden in deze economie permament te verwijderen. Zorg ervoor dat je alle gewenste data exporteert voordat je deze actie doorvoert. Weet je zeker dat je door wilt gaan?',
        'cannotDeleteWalletsNonZero' => 'Kan alle portemonnees niet verwijderen omdat sommige leden een saldo hebben dat niet nul is. Je moet alle portemonnees eerst naar nul resetten.',
        'confirmDeleteAllWallets' => 'Bevestig om alle portemonnees van leden permanent te verwijderen',
        'walletsDeleted' => 'Alle portemonnees zijn verwijderd.',
    ],

    /**
     * Community economy payment pages.
     */
    'economyPayments' => [
        'title' => 'Betalingen',
        'description' => 'Deze pagina toont alle betalingen die geïnitieerd zijn door groepsleden binnen deze economie.',
        'exportTitle' => 'Exporteer betalingen',
        'exportDescription' => 'Op deze pagina kun je alle betalingen die geïnitieerd zijn door groepsleden binnen deze economie exporteren naar een bestand om in een extern programma te bekijken of te importeren.',
    ],

    /**
     * Community economy currency pages.
     */
    'currencies' => [
        'title' => 'Valuta\'s',
        'description' => 'Op deze pagina zie je een overzicht van de ingestelde valuta\'s voor deze economie.<br>Ten minste één valuta moet ingeschakeld zijn om de economie te kunnen gebruiken voor een bar.<br>Voeg een nieuwe valuta toe, of klik op een valuta om deze te beheren.',
        'change' => 'Valuta aanpassen',
        'noCurrencies' => 'Deze economie heeft geen valuta\'s',
        'createCurrency' => 'Valuta toevoegen',
        'currencyCreated' => 'Valuta aangemaakt',
        'deleteQuestion' => 'Je staat op het punt deze valuta te verwijderen van deze economie. Weet je zeker dat je door wilt gaan?',
        'deleteVoidNotice' => 'Als je deze valuta verwijderd, zullen alle ingestelde prijzen in deze valuta verwijderd worden in bars die gebruik maken van deze economie.<br>Je kunt de valuta ook tijdelijk uitschakelen door deze aan te passen.',
        'currencyDeleted' => 'De valuta is verwijderd.',
        'currencyUpdated' => 'Valuta aanpassingen opgeslagen.',
        'enabledDescription' => 'Stel in of deze valuta in bars is ingeschakeld die gebruik maken van deze economie. Barleden kunnen geen producten kopen met deze valuta als uitgeschakeld, en moeten een ander valuta gebruiken, of wachten totdat de valuta weer is ingeschakeld.',
        'changeCurrencyTitle' => 'Valuta aanpassen?',
        'changeCurrencyDescription' => 'Je kunt alleen kleine valuta aanpassingen maken, om te voorkomen dat er fouten optreden op plekken waar deze valuta gebruikt wordt. Denk er anders over na een nieuwe valuta toe te voegen.',
        'allowWallets' => 'Portemonnee maken toestaan',
        'allowWalletsDescription' => 'Met deze optie stel je in of barleden een nieuwe persoonlijke portemonnee aan kunnen maken voor deze valuta. Huidige portemonnees blijven altijd bestaan.',
        'manage' => 'Beheer valutas',
        'namePlaceholder' => 'Euro',
        'detailDescription' => 'Hier volgen instellingen voor de valuta. Vul deze gegevens zo accuraat mogelijk in voor internationaal gebruikte valuta\'s, omdat betaalservices hiervan afhankelijk zijn. Sommige instellingen zijn niet meer aanpasbaar na het aanmaken.',
        'nameDescription' => 'Geef de valuta een naam, bijvoorbeeld \'Euro\' of \'Barmunten\'',
        'codeDescription' => 'Stel de internationale valuta code in volgens ISO 4217 als dit een internationaal gebruikt valuta is. Voor eigen valuta\'s zoals \'Barmunten\' laat je dit veld leeg.',
        'symbolDescription' => 'Stel het gewenste valuta symbool in. Zoals: \'€\' of \'B\'.',
        'formatDescription' => 'Specificeer het valuta formaat, om te definiëren hoe :app een geldbedrag laat zien. Zoals: \'€1.0,00\' of \'B1.0\'',
        'code' => 'Valuta code',
        'codePlaceholder' => 'EUR',
        'symbolPlaceholder' => '€',
        'format' => 'Valuta formaat',
        'formatPlaceholder' => '€1.0,00',
        'exampleNotation' => 'Voorbeeld notatie',
        'cannotDeleteHasWallet' => 'Je kunt deze valuta niet verwijderen, omdat een portemonnee gebruik maakt van deze valuta.',
        'cannotDeleteHasMutation' => 'Je kunt deze valuta niet verwijderen, omdat een transactie gebruik maakt van deze valuta.',
        'cannotDeleteHasPayment' => 'Je kunt deze valuta niet verwijderen, omdat een betaling gebruik maakt van deze valuta.',
        'cannotDeleteHasService' => 'Je kunt deze valuta niet verwijderen, omdat een betaalservice gebruik maakt van deze valuta.',
        'cannotDeleteHasChange' => 'Je kunt deze valuta niet verwijderen, omdat een balansimportverandering gebruik maakt van deze valuta.',
    ],

    /**
     * Product pages.
     */
    'products' => [
        'title' => 'Producten',
        'all' => 'Alle producten',
        'select' => 'Selecteer producten',
        'search' => 'Producten zoeken',
        'clickBuyOrSearch' => 'Klik op producten om te kopen of zoek',
        '#products' => '{0} Geen producten|{1} 1 product|[2,*] :count producten',
        'noProducts' => 'Geen producten...',
        'searchingFor' => 'Zoeken naar :term...',
        'noProductsFoundFor' => 'Geen producten gevonden voor :term',
        'manageProduct' => 'Beheer product',
        'manageProducts' => 'Beheer producten',
        'addProducts' => 'Producten toevoegen',
        'newProduct' => 'Nieuw product',
        'cloneProduct' => 'Dupliceer product',
        'editProduct' => 'Product aanpassen',
        'created' => 'Het product is aangemaakt.',
        'changed' => 'Product aanpassingen opgeslagen.',
        'restoreQuestion' => 'Je staat op het punt dit product te herstellen om het weer beschikbaar te maken. Weet je zeker dat je door wilt gaan?',
        'restored' => 'Het product is hersteld.',
        'deleteQuestion' => 'Je staat op het punt dit product te verwijderen. Weet je zeker dat je door wilt gaan?',
        'permanentDescription' => 'Vink het selectievakje aan om dit product permanent te verwijderen. Je kunt het product dan niet meer herstellen.',
        'permanentlyDelete' => 'Product permanent verwijderen',
        'deleted' => 'Het product is weggegooid.',
        'deletedProduct' => 'Verwijderd product',
        'permanentlyDeleted' => 'Het product is permanent verwijderd.',
        'namePlaceholder' => 'Luxe Sap',
        'tagsPlaceholder' => 'cola frisdrank',
        'enabledDescription' => 'Ingeschakeld, kan gekocht worden',
        'prices' => 'Prijzen',
        'pricesDescription' => 'Configureer de prijzen voor dit product in de velden hieronder voor de valutas die je wilt ondersteunen.',
        'localizedNames' => 'Gelokaliseerde namen',
        'localizedNamesDescription' => 'Configureer de gelokaliseerde namen voor dit product in de velden hieronder als het verschilt met de hoofdnaam.',
        'search' => 'Zoek producten',
        'backToProducts' => 'Terug naar producten',
        'viewProduct' => 'Product bekijken',
        'unknownProduct' => 'Onbekend product',
        'recentlyBoughtProducts#' => '{0} Recent gekochte producten|{1} Recent gekocht product|[2,*] :count recent gekochte producten',
        'type' => [
            'normal' => 'Normaal',
            'custom' => 'Aangepast',
        ],
    ],

    /**
     * Payment service pages.
     */
    'paymentService' => [
        'title' => 'Betaalservices',
        'service' => 'Betaalservice',
        'noServices' => 'Geen betaalservices...',
        'manageService' => 'Beheer betaalservice',
        'manageServices' => 'Beheer betaalservices',
        'serviceType' => 'Type betaalservice',
        'availableTypes#' => '{0} Geen betaalservice types beschikbaar|{1} Beschikbare betaalservice type|[2,*] :count betaalservice types beschikbaar',
        'newService' => 'Service toevoegen',
        'newChooseType' => 'Kies alsjeblieft het type betaalservice dat je wilt configureren en wilt toevoegen.',
        'editService' => 'Service aanpassen',
        'deleteService' => 'Service verwijderen',
        'created' => 'De betaalservice is aangemaakt.',
        'changed' => 'Betaalservice aanpassingen opgeslagen.',
        'restoreQuestion' => 'Je staat op het punt deze betaalservice te herstellen om het weer beschikbaar te maken. Weet je zeker dat je door wilt gaan?',
        'restored' => 'Deze betaalservice is hersteld.',
        'deleteQuestion' => 'Je staat op het punt deze betaalservice te verwijderen. Weet je zeker dat je door wilt gaan?',
        'permanentDescription' => 'Vink het selectievakje aan om deze betaalservice permanent te verwijderen. Je kunt de service dan niet meer herstellen.',
        'permanentlyDelete' => 'Betaalservice permanent verwijderen',
        'deleted' => 'De betaalservice is weggegooid.',
        'permanentlyDeleted' => 'De betaalservice is permanent verwijderd.',
        'enabledDescription' => 'Ingeschakeld, kan gebruikt worden',
        'enabledServices#' => '{0} Geen ingeschakelde services|{1} Ingeschakelde service|[2,*] :count ingeschakelde services',
        'disabledServices#' => '{0} Geen uitgeschakelde services|{1} Uitgeschakelde service|[2,*] :count uitgeschakelde services',
        'supportDeposit' => 'Ondersteun storingen',
        'supportWithdraw' => 'Ondersteun opnames',
        'supportDepositDescription' => 'Ondersteun stortingen. Laat gebruikers geld storten in portemonnees binnen deze economie.',
        'supportWithdrawDescription' => 'Ondersteun opnames. Laat gebruikers geld opnemen van portemonnees binnen deze economie. (Op dit moment niet ondersteund)',
        'backToServices' => 'Terug naar betaalservices',
        'viewService' => 'Service bekijken',
        'unknownService' => 'Onbekende betaalservice',
        'startedWillUseOldDetails' => 'Betalingen die al geïnitieerd zijn gebruiken mogelijk nog oude gegevens, ook nadat je ze hier aanpast.',
        'startedWillComplete' => 'Er zullen geen nieuwe betalingen geaccepteerd worden met deze service. Betaling die al geïnitieerd zijn zullen echter wel nog worden afgemaakt.',
        'amountInCurrency' => 'Bedrag in :currency',
        'amountToTopUpInCurrency' => 'Bedrag om mee op te waarderen in :currency',
        'pay' => 'Betaal',
        'otherPay' => 'Ander bedrag, betaal',
        'selectPaymentServiceToUse' => 'Betaalmethode',
    ],

    /**
     * Balance import system pages.
     */
    'balanceImport' => [
        'title' => 'Saldoimports',
        'system' => 'Systeem',
        'systems' => 'Systemen',
        'noSystems' => 'Geen systemen...',
        'systems#' => '{0} Geen systemen|{1} Systeem|[2,*] :count systemen',
        'manageSystem' => 'Beheer systeem',
        'manageSystems' => 'Beheer systemen',
        'namePlaceholder' => 'Ons systeem op papier',
        'newSystem' => 'Systeem toevoegen',
        'editSystem' => 'Systeem aanpassen',
        'deleteSystem' => 'Systeem verwijderen',
        'created' => 'Het saldoimportsysteem is aangemaakt.',
        'changed' => 'Het saldoimportsysteem is opgeslagen.',
        'deleteQuestion' => 'Je staat op het punt dit saldoimportsysteem te verwijderen. Hiermee verwijder je ook alle gerelateerde geïmporteerde data. Weet je zeker dat je door wilt gaan?',
        'deleted' => 'Het saldoimportsysteem is verwijderd.',
        'cannotDeleteHasEvents' => 'Kan dit systeme niet verwijderen, omdat het gebeurtenissen heeft',
        'backToSystems' => 'Terug naar systemen',
        'viewSystem' => 'Systeem bekijken',
        'unknownSystem' => 'Onbekende systeem',
        'exportUserList' => 'Exporteer gebruikerslijst',
        'exportUserListDescription' => 'Deze lijst bevat alle e-mailadressen voor gebruikers uit balansimports binnen dit balansimportsysteem, die tenminste één balansimportverandering hebben die is toegepast in een gebruikersportemonnee. Die betekent dat alleen gebruikers getoond worden die zich geregistreerd hebben, zich hebben ingeschreven bij een bar in deze economie, en die hun e-mailadres hebben geverifiëerd. Naar deze gebruikers wordt automatisch een balansupdate gestuurd vanuit :app.',
    ],

    /**
     * Balance import event pages.
     */
    'balanceImportEvent' => [
        'title' => 'Saldoimportgebeurtenissen',
        'event' => 'Gebeurtenis',
        'events' => 'Gebeurtenissen',
        'noEvents' => 'Geen gebeurtenissen...',
        'events#' => '{0} Geen gebeurtenissen|{1} Gebeurtenis|[2,*] :count gebeurtenissen',
        'manageEvent' => 'Beheer gebeurtenis',
        'manageEvents' => 'Beheer gebeurtenissen',
        'namePlaceholder' => '2019 Januari',
        'newEvent' => 'Gebeurtenis toevoegen',
        'editEvent' => 'Gebeurtenis aanpassen',
        'deleteEvent' => 'Gebeurtenis verwijderen',
        'created' => 'De saldoimportgebeurtenis is aangemaakt.',
        'changed' => 'De saldoimportgebeurtenis is opgeslagen.',
        'deleteQuestion' => 'Je staat op het punt deze saldoimportgebeurtenis te verwijderen. Hiermee verwijder je ook alle geïmporteerde data. Weet je zeker dat je door wilt gaan?',
        'deleted' => 'De saldoimportgebeurtenis is verwijderd.',
        'cannotDeleteHasChanges' => 'Kan deze gebeurtenis niet verwijderen, omdat het geïmporteerde aanpassingen',
        'backToEvents' => 'Terug naar gebeurtenissen',
        'viewEvent' => 'Gebeurtenis bekijken',
        'unknownEvent' => 'Onbekende gebeurtenis',
    ],

    /**
     * Balance import change pages.
     */
    'balanceImportChange' => [
        'title' => 'Saldoimportveranderingen',
        'change' => 'Verandering',
        'changes' => 'Veranderingen',
        'noChanges' => 'Geen veranderingen...',
        'approvedChanges' => 'Goedgekeurde veranderingen',
        'unapprovedChanges' => 'Niet gekeurde veranderingen',
        'noApprovedChanges' => 'Geen gekeurde veranderingen...',
        'noUnapprovedChanges' => 'Geen niet-gekeurde veranderingen...',
        'changes#' => '{0} Geen veranderingen|{1} Verandering|[2,*] :count veranderingen',
        'manageChange' => 'Beheer verandering',
        'manageChanges' => 'Beheer veranderingen',
        'newChange' => 'Verandering toevoegen',
        'importJsonChanges' => 'JSON verandering importeren',
        'editChange' => 'Verandering aanpassen',
        'approveChange' => 'Verandering goedkeuren',
        'approveAll' => 'Alles goedkeuren',
        'undoChange' => 'Verandering ongedaan maken',
        'deleteChange' => 'Verandering verwijderen',
        'created' => 'De saldoimportverandering is geïmporteerd.',
        'importedJson' => 'De JSON saldoimportveranderingen zijn geïmporteerd.',
        'changed' => 'De saldoimportverandering is opgeslagen.',
        'approveQuestion' => 'Je staat op het punt deze saldoimportverandering goed te keuren. Dit past de saldoverandering toe in de portemonnee van de gebruiker zodra de gebruiker zich inschrijft bij een gekoppelde bar. Weet je zeker dat je door wilt gaan?',
        'approved' => 'De saldoimportgebeurtenis is goedgekeurd en zal in de achtergrond toegepast worden bij de gebruiker.',
        'approveAllQuestion' => 'Je staat op het punt alle saldoimportverandering goed te keuren in de \':event\' gebeurtenis. Dit past de saldoveranderingen toe in de portemonnee van de gebruiker zodra de gebruiker zich inschrijft bij een gekoppelde bar. Weet je zeker dat je door wilt gaan?',
        'approvedAll' => 'De saldoimportgebeurtenissen zijn goedgekeurd en zullen in de achtergrond toegepast worden bij de gebruiker.',
        'undoQuestion' => 'Je staat op het punt om deze saldoimportverandering ongedaan te maken. Dit zal de status op niet-goedgekeurd zetten, en toegepaste saldo aanpassingen bij de gebruiker zullen teruggedraaid worden. Weet je zeker dat je door wilt gaan?',
        'undone' => 'The balance import change has been undone.',
        'deleteQuestion' => 'Je staat op het punt deze saldoimportverandering te verwijderen. Een mutatie in een portemonnee van een gebruiker die al toegewijd is als resultaat van deze verandering zal niet worden teruggedraaid, en de portemonnee mutatie zal dan ontkoppeld worden. Weet je zeker dat je door wilt gaan?',
        'deleted' => 'De saldoimportverandering is verwijderd.',
        'backToChanges' => 'Terug naar veranderingen',
        'viewChange' => 'Verandering bekijken',
        'unknownChange' => 'Onbekende verandering',
        'finalBalance' => 'Eindsaldo',
        'jsonData' => 'JSON data',
        'cost' => 'Kosten',
        'enterAliasNameEmail' => 'Vul de naam en het e-mailadres in voor de gebruiker voor wie je saldo importeert. Het e-mailadres zal gebruikt worden om automatisch het geïmporteerde saldo te koppelen aan de portemonnee van een geregistreerde gebruiker.',
        'selectCurrency' => 'Selecteer het valuta voor deze import.',
        'balanceOrCostDescription' => 'Vul het eindsaldo of de kosten voor de gebruiker in.<br><br>Voor periodieke saldoimports, vul het eindsaldo op het moment van de import gebeurtenis in het eindsaldo veld in. Bij de eerste import zal het eindsaldo volledig aan de gebruiker gegeven worden. Bij opvolgende imports zal het verschil tussen het laatste geïmporteerde saldo en het eindsaldo aan de gebruiker gegeven worden.<br><br>Voor een enkele kostenopgave, vul het kosten veld in om aan de gebruiker te crediteren. Gebruik een negatief getal om saldo aan de gebruiker te geven. Dit heeft geen effect op het bijgehouden saldo voor periodieke imports voor deze gebruiker.',
        'enterBalanceOrCost' => 'Vul alleen het eindsaldo of de kosten in.',
        'importJsonDescription' => 'Importeer periodieke saldo updates vanuit JSON data.<br><br>Voor periodieke saldoimports, vul het eindsaldo op het moment van de import gebeurtenis in het eindsaldo veld in. Bij de eerste import zal het eindsaldo volledig aan de gebruiker gegeven worden. Bij opvolgende imports zal het verschil tussen het laatste geïmporteerde saldo en het eindsaldo aan de gebruiker gegeven worden.',
        'importJsonFieldsDescription' => 'Configureer de veldnamen die voor elke gebruiker gebruikt worden in de JSON data.',
        'importJsonDataDescription' => 'Vul de JSON data in. Dit moet een JSON array zijn met objecten, waarbij elk object de hierboven geconfigureerde velden bevat.',
        'hasUnapprovedMustCommit' => 'Sommige veranderingen zijn nog niet goedgekeurd, en zullen niet toegepast worden bij gebruikers.',
        'mustApprovePreviousFirst' => 'Je moet eerst de vorige saldoimportverandering met een saldo update goedkeuren.',
        'mustApproveAllPreviousFirst' => 'Je moet eerst alle vorige saldoimportverandering voor deze saldoimportveranderingen die je wilt goedkeuren met een saldo update goedkeuren.',
        'cannotApproveWithFollowingApproved' => 'Je kunt een saldoimportverandering niet goedkeuren, als deze al een latere verandering heeft die goedgekeurd is.',
        'cannotDeleteMustUndo' => 'Je kunt een verandering die goedgekeurd is niet verwijderen. Je moet dit eerst ongedaan maken.',
        'cannotUndoIfNewerApproved' => 'Je kunt deze saldoimportverandering niet ongedaan maken, omdat er een nieuwe saldo verandering is die nog geaccepteerd is.',
    ],

    /**
     * Balance import alias pages.
     */
    'balanceImportAlias' => [
        'newAliasMustProvideName' => 'Het gegeven e-mailadres is nog niet bekend, dus je moet een naam specificeren.',
        'newJsonAliasMustProvideName' => 'Het gegeven e-mailadres \':email\' is nog niet bekend, het naam veld mist voor deze gebruiker.',
        'jsonHasDuplicateAlias' => 'De JSON data bevat meerdere items voor \':email\'.',
        'aliasAlreadyInEvent' => 'De gebruiker \':email\' heeft al een verandering in deze gebeurtenis',
        'allowAddingSameUserMultiple' => 'Sta toe dezelfde gebruiker meer dan eens toe te voegen in deze gebeurtenis (Niet aangeraden)',
    ],

    /**
     * Balance import balance update email.
     */
    'balanceImportMailBalance' => [
        'title' => 'Stuur saldo e-mail',
        'description' => 'Stuur gebruikers in deze balansimportgebeurtenis een e-mail update. Dit kun je gebruiken om de saldo update bij gebruikers te melden wanneer deze geïmporteerd wordt. Er wordt alleen een bericht gestuurd als de veranderingen zijn goedgekeurd. Het laatst bekende saldo binnen dit systeem wordt altijd gebruikt. Gebruikers met een saldo van nul zullen geen bericht ontvangen.',
        'mailUnregisteredUsers' => 'Bericht ongeregistreerde gebruikers (geen account)',
        'mailNotJoinedUsers' => 'Bericht niet-leden (met account, geen bar lid)',
        'mailJoinedUsers' => 'Bericht leden (met account, en bar lid)',
        'extraMessage' => 'Extra bericht',
        'relatedBar' => 'Gerelateerde bar',
        'noRelatedBar' => 'Geen gerelateerde bar',
        'mustSelectBarToInvite' => 'Je moet een bar selecteren om uit te nodigen',
        'inviteToJoinBar' => 'Nodig ongeregistreerde gebruikers uit deel te nemen aan de bar',
        'limitToLastEvent' => 'Limiteer tot gebruikers en leden in de laatste event',
        'replyToAddress' => '\'Reply-To\' e-mailadres',
        'confirmSendMessage' => 'Bevestig om e-mailberichten te versturen.',
        'sentBalanceUpdateEmail' => 'Een saldo update e-mail zal nu verzonden worden naar de geselecteerde gebruikers. Het kan enkele minuten duren voordat het bericht aankomt.',
    ],

    /**
     * Economy finance pages.
     */
    'finance' => [
        'title' => 'Financieel rapport',
        'walletSum' => 'Cumulatief saldo',
        'paymentsInProgress' => 'In verwerking',
        'noAccountImport' => 'Geen account (import)',
        'membersWithNonZeroBalance' => 'Leden met saldo',
        'description' => 'Hier zie je een simpel financieel overzicht van de huidige economie staat. Gebruikers van saldoimports, die nog niet geregistreerd zijn en nog geen deel uitmaken van deze economie worden hier nog niet getoond.',
    ],

    /**
     * bunq account pages.
     */
    'bunqAccounts' => [
        'title' => 'bunq accounts',
        'bunqAccount' => 'bunq account',
        'description' => 'Klik op één van je bunq accounts om deze te beheren, of maak een nieuwe aan.',
        'noAccounts' => 'Je hebt nog geen bunq accounts toegevoegd...',
        'addAccount' => 'bunq account toevoegen',
        'addAccountDescription' => 'Op deze pagina kunt je een bunq account toevoegen voor automatische betalingsverwerking.<br><br>Maak een API token aan en een leeg betaalaccount in de bunq app. Vul de token en de IBAN van het betaalaccount hieronder in.<br><br>Wil je liever een testaccount gebruiken?',
        'createSandboxAccount' => 'bunq sandbox account aanmaken',
        'descriptionPlaceholder' => 'bunq account voor automatisering van bar betalingen',
        'tokenDescription' => 'Maak een nieuwe API sleutel aan in de ontwikkelaars sectie van de bunq app op je mobiele telefoon, en vul de aangemaakte token in. Deze token mag nooit gedeeld worden met anderen.',
        'ibanDescription' => 'Vul de IBAN van een monitair account binnen je bunq profiel in. Dit account zal volledig opgedragen worden aan betaalverwerking, en kan niet gebruikt worden voor andere doeleinden. Het is aangeraden om hiervoor een nieuw monitair account aan te maken via de bunq app.',
        'invalidApiToken' => 'Invalid API token',
        'addConfirm' => 'Zodra je dit bunq account toevoegt, geef je :app volledige control over het monitaire account gekoppeld aan de gegeven IBAN. Dat account zal volledig gewijd worden aan geautomatiseerde betaalverwerking, totdat de koppeling tussen :app en bunq is opgeheven. Hef deze koppeling nooit op via de mobiele bunq applicatie door de API token te verwijderen, maar doe dit via :app zodat lopende betaling juist afgerond kunnen worden. Het account moet een huidig saldo van €0.00 hebben. Je kunt dit monitaire account niet voor andere betalingen, applicaties of andere :app platforms gebruiken, want dan riskeer je geldstroom problemen. :app is niet aansprakelijk voor schade aangericht door het koppelen van je bunq account aan deze applicatie.',
        'createSandboxConfirm' => 'Dit zal een bunq sandbox account aanmaken voor test doeleinden. Let er op dat dit het mogelijk zal maken dat gebruikers hun portemonnee opwaarderen zonder echt geld te betalen. :app is niet aansprakelijk voor schade aangericht door het koppelen van je bunq account aan deze applicatie.',
        'mustEnterBunqIban' => 'Je moet een bunq IBAN invullen',
        'accountAlreadyUsed' => 'Dit monitair account wordt al gebruikt',
        'noAccountWithIban' => 'Geen actief monetair account met deze IBAN',
        'onlyEuroSupported' => 'Alleen accounts die de EURO valuta gebruiken zijn ondersteund',
        'notZeroBalance' => 'Account heeft geen saldo van €0.00, maak een nieuwe monitair account',
        'added' => 'Het bunq account is toegevoegd.',
        'changed' => 'Het bunq account is aangepast.',
        'paymentsEnabled' => 'Betalingen ingeschakeld',
        'checksEnabled' => 'Checks ingeschakeld',
        'enablePayments' => 'Betalingen inschakelen: sta gebruik voor nieuwe betalingen toe',
        'enableChecks' => 'Checks inschakelen: voor een periodieke controle uit voor nieuw ontvangen betalingen',
        'confirm' => 'Ik ga hiermee akkoord en voldoe aan de eisen',
        'environment' => 'bunq API environment',
        'runHousekeeping' => 'Voer huishouden uit',
        'runHousekeepingSuccess' => 'Het monitaire bunq account is opnieuw ingesteld en eventuele hangende betalingen staan nu in de wachtrij voor verwerking.',
        'noHttpsNoCallbacks' => 'Deze site gebruikt geen HTTPS, real time bunq betalingen zijn daarom niet ondersteund. Betalingen zullen eens per dag verwerkt worden.',
        'manageCommunityAccounts' => 'Beheer bunq accounts voor groep',
        'manageAppAccounts' => 'Beheer bunq accounts voor gehele applicatie',
        'lastCheckedAt' => 'Laatst gechecked op',
        'lastRenewedAt' => 'API laatst vernieuwd op',
        'notRenewedYet' => 'Nog niet verieuwd',
    ],

    /**
     * Wallet pages.
     */
    'wallets' => [
        'title' => 'Portemonnees',
        'description' => 'Klik op één van je portemonnees om deze te beheren, of maak een nieuwe aan.',
        'walletEconomies' => 'Portemonnee economieën',
        'myWallets' => 'Mijn portemonnees',
        '#wallets' => '{0} Geen portemonnees|{1} 1 portemonnee|[2,*] :count portemonnees',
        'economySelectDescription' => 'Portemonnees voor deze groep zijn onderverdeeld per economie.<br>Selecteer een economie om je portemonnees te beheren.',
        'noWallets' => 'Geen portemonnees...',
        'namePlaceholder' => 'Mijn persoonlijke portefeuille',
        'nameDefault' => 'Mijn portefeuille',
        'createWallet' => 'Portemonnee aanmaken',
        'walletCreated' => 'De portemonnee is aangemaakt.',
        'walletUpdated' => 'Portemonnee aanpassingen opgeslagen.',
        'deleteQuestion' => 'Je staat op het punt deze portemonnee te verwijderen. Weet je zeker dat je door wilt gaan?',
        'cannotDeleteNonZeroBalance' => 'Om deze portemonnee te verwijderen moet het saldo precies :zero zijn.',
        'walletDeleted' => 'De portemonnee is verwijderd.',
        'cannotCreateNoCurrencies' => 'Je kunt nu geen portemonnee aanmaken. De groep administrator heeft geen valuta geconfigureerd waarbij dit is toegestaan.',
        'all' => 'Alle portemonnees',
        'view' => 'Bekijk portemonnee',
        'noWalletsToMerge' => 'Je hebt geen portemonnees die je samen kunt voegen.',
        'mergeWallets' => 'Portemonnees samenvoegen',
        'mergeDescription' => 'Selecteer voor elk valuta de portemonnees die je samen wilt voegen.',
        'mustSelectTwoToMerge' => 'Selecteer ten minste twee :currency portemonnees om samen te voegen.',
        'mergedWallets#' => '{0} Geen portemonnees samengevoegd|{1} Eén portemonnee samengevoegd|[2,*] :count portemonnees samengevoegd',
        'transfer' => 'Overboeken',
        'transferToSelf' => 'Overboeken naar portemonnee',
        'transferToUser' => 'Overboeken naar gebruiker',
        'toSelf' => 'Naar portemonnee',
        'toUser' => 'Naar gebruiker',
        'topUp' => 'Portemonnee opwaarderen',
        'topUpNow' => 'Nu betalen',
        'noWalletToTopUp' => 'Je hebt hier geen portemonnee om op te waarderen.',
        'modifyBalance' => 'Saldo aanpassen',
        'modifyMethod' => 'Aanpassing methode',
        'modifyMethodDeposit' => 'Storten (optellen)',
        'modifyMethodWithdraw' => 'Opnemen (aftrekken)',
        'modifyMethodSet' => 'Aanpassen naar saldo',
        'modifyBalanceWarning' => 'Hiermee wordt het portemonneesaldo aangepast, zonder dat er echt geld uitgewisseld wordt via :app. Deze aanpassing wordt opgeslagen en is zichtbaar voor de gebruiker.',
        'confirmModifyBalance' => 'Bevestig saldoaanpassing',
        'balanceModified' => 'Het portemonneesaldo is aangepast.',
        'successfullyTransferredAmount' => ':amount succesvol overgeboekt naar :wallet',
        'backToWallet' => 'Terug naar portemonnee',
        'walletTransactions' => 'Portemonnee transacties',
        'noServiceConfiguredCannotTopUp' => 'Je kunt je portemonnee niet via :app opwaarderen. De bar of groepsadministrator heeft geen betaalmethode geconfigureerd. Vraag er naar bij de bar voor meer informatie.',
    ],

    /**
     * Wallet stats pages.
     */
    'walletStats' => [
        'title' => 'Portemonnee statistieken',
        'description' => 'Hier zie je statistieken van je portemonnee in de geselecteerde periode.',
        'transactions' => 'Transacties',
        'income' => 'Inkomsten',
        'expenses' => 'Uitgaven',
        'paymentIncome' => 'Betalingsinkomsten',
        'productExpenses' => 'Productuitgaven',
        'products' => 'Producten',
        'uniqueProducts' => 'Unieke producten',
        'balanceHistory' => 'Saldogeschiedenis',
        'purchaseDistribution' => 'Productdistributie',
        'purchasePerDay' => 'Productaankopen per weekdag (UTC)',
        'purchasePerHour' => 'Productaankopen per dag uur (UTC)',
        'purchaseHistogram' => 'Productaankoop histogram',
        'noStatsNoTransactions' => 'Geen statistieken om te tonen. Portemonnee heeft nog geen transacties.',
        'period' => [
            'week' => 'Afgelopen week',
            'month' => 'Afgelopen maand',
            'year' => 'Afgelopen jaar',
        ],
        'typeProductDist' => [
            'title' => 'Gekochte producten',
            'chartName' => 'Productdistributiegrafiek',
        ],
        'smartText' => [
            'main' => 'In de :period was je actief op <b>:active-days</b>:best-day. Tijdens deze periode heb je <b>:products</b> gekocht:products-unique.',
            'mainDays' => '{0} geen dagen|{1} één dag|[2,*] :count verschillende dagen',
            'mainBestDay' => ' waarvan <b>:day</b> je beste dag was',
            'mainUniqueProducts' => ', waarvan :unique uniek',
            'productCount' => '{0} geen producten|{1} één product|[2,*] :count producten',
            'productUniqueCount' => '{0} none|{1} <b>één</b>|[2,*] <b>:count</b>',
            'partBestProduct' => 'Je hebt <b>:product</b> het meest gekocht:extra.',
            'partBestProductExtra' => ', gevolgd door <b>:product</b>:extra',
        ],
    ],

    /**
     * Transaction pages.
     */
    'transactions' => [
        'title' => 'Transacties',
        'details' => 'Transactie details',
        'last#' => '{0} Laatste transacties|{1} Laatste transactie|[2,*] Laatste :count transacties',
        'noTransactions' => 'Geen transacties',
        'backToTransaction' => 'Terug naar transactie',
        'toTransaction' => 'naar transactie',
        'fromTransaction' => 'van transactie',
        'referencedTo#' => '{0} Gerefereerd aan geen transacties|{1} Gerefereerd aan transactie|[2,*] Gerefereerd aan :count transacties',
        'referencedBy#' => '{0} Gerefereerd door geen transacties|{1} Gerefereerd door transactie|[2,*] Gerefereerd door :count transacties',
        'cannotUndo' => 'Deze transactie kan niet ongedaan gemaakt worden.',
        'undone' => 'De transactie is ongedaan gemaakt.',
        'undoTransaction' => 'Transactie ongedaan maken',
        'selectProductsToUndo' => 'Selecteer producten om ongedaan te maken',
        'noProductsSelected' => 'Geen producten geselecteerd',
        'undoQuestion' => 'Je staat op het punt deze transactie ongedaan te maken. Weet je zeker dat je door wilt gaan?',
        'viewTransaction' => 'Bekijk transactie',
        'linkedTransaction' => 'Gekoppelde transactie',
        'state' => [
            'pending' => 'In afwachting',
            'processing' => 'Bezig met verwerken',
            'success' => 'Voltooid',
            'failed' => 'Mislukt',
        ],
        'descriptions' => [
            'balanceImport' => 'Import extern saldo',
            'fromWalletToProduct' => 'Aankoop product(en)',
            'toProduct' => 'Aankoop product(en)',
            'fromPaymentToWallet' => 'Storting naar portemonnee',
            'fromWalletToWallet' => 'Overboeking tussen portemonnees',
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
        'viewMutation' => 'Bekijk mutatie',
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
            'paymentToDetail' => 'Opname via :payment',
            'paymentFromDetail' => 'Storting via :payment',
            'balanceImport' => 'Import saldo van extern systeem',
            'balanceImportDetail' => 'Import saldo van extern systeem door :user',
        ],
    ],

    /**
     * Notification pages.
     */
    'notifications' => [
        'title' => 'Notificaties',
        'notification' => 'Notificatie',
        'description' => 'Hier zie je al je notificaties, gelezen en ongelezen.',
        'unread#' => '{0} Geen ongelezen notificaties|{1} Ongelezen notificatie|[2,*] :count ongelezen notificaties',
        'persistent#' => '{0} Geen vaste notificaties|{1} Vaste notificatie|[2,*] :count vaste notificaties',
        'read#' => '{0} Geen gelezen notificaties|{1} Gelezen notificatie|[2,*] :count gelezen notificaties',
        'noNotifications' => 'Geen notificaties...',
        'all' => 'Alle notificaties',
        'markAsRead' => 'Markeren als gelezen',
        'markAllAsRead' => 'Markeer alles als gelezen',
        'markedAsRead#' => '{0} Geen notificaties als gemarkeerd als gelezen|{1} Notificatie gemarkeerd als gelezen|[2,*] :count notificaties gemarkeerd als gelezen',
        'unknownNotificationAction' => 'Onbekelde notificatie actie',
    ],

    /**
     * Payment pages.
     */
    'payments' => [
        'title' => 'Betalingen',
        'description' => 'Hier zie je alle betalingen in behandeling en afgehandeld die je gemaakt hebt in alle groepen.',
        'details' => 'Betaling details',
        'progress' => 'Voortgang van betaling',
        '#payments' => '{0} Geen betalingen|{1} 1 betaling|[2,*] :count betalingen',
        'last#' => '{0} Laatste betalingen|{1} Laatste betaling|[2,*] Laatste :count betalingen',
        'backToPayment' => 'Terug naar betaling',
        'backToPayments' => 'Terug naar betalingen',
        'requiringAction' => 'Betaling wacht op actie',
        'requiringAction#' => '{0} Geen actie vereist|{1} Betaling vereist actie|[2,*] :count vereisen actie',
        'inProgress' => 'Betaling in behandeling',
        'inProgress#' => '{0} Geen in behandeling|{1} Betaling in behandeling|[2,*] :count in behandeling',
        'inProgressDescription' => 'Deze betaling is nog in behandeling.',
        'settled#' => '{0} Geen afgehandeld|{1} Betaling afgehandeld|[2,*] :count afgehandeld',
        'noPayments' => 'Je hebt nog geen betalingen gedaan',
        'viewPayment' => 'Bekijk betaling',
        'unknownPayment' => 'Onbekende betaling',
        'handlePayments' => 'Behandel betalingen',
        'handleCommunityPayments' => 'Behandel groep betalingen',
        'paymentsToApprove' => 'Betalingen wachtend op actie',
        'paymentsWaitingForAction' => 'Een aantal betalingen wachten op actie van een groepsbeheerder. Behandel deze alsjeblieft zo snel mogelijk.',
        'paymentsToApproveDescription' => 'De volgende betalingen wachten op actie van een groepsbeheerder. Verwerk deze alsjeblieft zo snel mogelijk om betalingen vlot te laten verlopen.',
        'paymentRequiresCommunityAction' => 'Deze betaling wacht op actie van een groepsbeheerder. Als je geen toegang hebt tot de ontvangstrekening, onderneem dan geen actie en laat deze controle over aan een groepsbeheerder die hier wel toegang tot heeft.',
        'cancelPayment' => 'Betaling annuleren',
        'cancelPaymentQuestion' => 'Je staat op het punt deze betaling te annuleren. Annulleer nooit een betaling waarvoor je al geld hebt overgemaakt, of je overboeking gaat mogelijk verloren. Weet je zeker dat je door wilt gaan?',
        'paymentCancelled' => 'Betaling geannuleerd',
        'state' => [
            'init' => 'Geïnitieerd',
            'pendingUser' => 'Wachtend op gebruiker actie',
            'pendingCommunity' => 'Wachtend op beoordeling',
            'pendingAuto' => 'In afwachting (automatisch)',
            'processing' => 'Bezig met verwerken',
            'completed' => 'Voltooid',
            'revoked' => 'Ingetrokken',
            'rejected' => 'Afgekeurd',
            'failed' => 'Mislukt',
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
        'deleteBar' => 'Bar verwijderen',
        'join' => 'Inschrijven',
        'yesJoin' => 'Ja, inschrijven',
        'joined' => 'Ingeschreven',
        'youAreJoined' => 'Je bent ingeschreven bij deze bar.',
        'notJoined' => 'Niet ingeschreven',
        'leave' => 'Uitschrijven',

        'hintJoin' => 'Je maakt nog geen deel uit van deze bar.',
        'joinQuestion' => 'Wil je je bij deze bar inschrijven?',
        'alsoJoinCommunity' => 'Ook inschrijven bij de bijbehorende groep',
        'alreadyJoinedTheirCommunity' => 'Je bent al lid van de bijbehorende groep',
        'joinedThisBar' => 'Je bent ingeschreven bij deze bar.',
        'cannotSelfEnroll' => 'Je kunt jezelf niet inschrijven voor deze bar, de functie is uitgeschakeld.',
        'leaveQuestion' => 'Weet je zeker dat je je wilt uitschrijven bij deze bar?',
        'leftThisBar' => 'Je bent uitgeschreven bij deze bar.',
        'cannotLeaveHasWallets' => 'Je kunt deze bar niet verlaten, omdat je hier een portemonnee hebt.',
        'protectedByCode' => 'Deze bar is beveiligd met een code. Vraag er naar bij de bar, of scan de bar QR-code als deze beschikbaar is.',
        'protectedByCodeFilled' => 'Deze bar is beveiligd met een code. We hebben de code voor je ingevuld.',
        'incorrectCode' => 'Verkeerde bar code.',
        'namePlaceholder' => 'Viking bar',
        'descriptionPlaceholder' => 'Welkom bij dé Viking bar!',
        'slugDescription' => 'Een URL-pad laat je een makkelijk te onthouden link creëren om de bar pagina te bezoeken, door een kort trefwoord te definieren.',
        'slugDescriptionExample' => 'Dit kan de barlink versimpelen:',
        'slugPlaceholder' => 'viking',
        'slugFieldRegexError' => 'Dit trefword moet met een alfabetisch karakter beginnen.',
        'codeDescription' => 'Met een barcode voorkom je dat willekeurige gebruikers zich inschrijven bij deze bar. Om in te schrijven moeten gebruikers de gespecificeerde code invullen.',
        'economyDescription' => 'De economie bepaalt welke producten, valutas en portemonnees gebruikt worden in deze bar. Wees voorzichtig met het achteraf aanpassen hiervan omdat dit direct effect heeft op de lijst met producten, valutas en portemonnees gebruikt binnen deze bar. Gebruikers verwachten dit waarschijnlijk niet, en vinden dit mogelijk moeilijk te begrijpen.',
        'showExploreDescription' => 'Toon on publieke \'Ontdek bars\' pagina',
        'showCommunityDescription' => 'Toon op groepspagina voor groepsleden',
        'selfEnrollDescription' => 'Sta zelf inschrijven toe (met code als gespecificeerd)',
        'joinAfterCreate' => 'Schrijf je in bij de bar na het aanmaken',
        'created' => 'De bar is aangemaakt.',
        'updated' => 'De bar is aangepast.',
        'mustCreateEconomyFirst' => 'Voor een nieuwe bar moest je eerst een economie aanmaken.',
        'backToBar' => 'Terug naar bar',
        'quickBuy' => 'Nu kopen',
        'boughtProductForPrice' => ':product gekocht voor :price',
        'noDescription' => 'Deze bar heeft geen beschrijving',
        'manageBar' => 'Beheer bar',
        'barInfo' => 'Bar informatie',
        'viewBar' => 'Bar bekijken',
        'deleted' => 'De bar is verwijderd.',
        'deleteQuestion' => 'Je staat op het punt deze bar permanent te verwijderen. Alle leden waaronder jezelf zullen toegang tot de bar verliezen, en product transacties zullen niet meer gelinkt kunnen worden aan deze bar. De producten en portemonnees van leden blijven bestaan als onderdeel van de economie die gebruikt werd binnen deze bar. Weet je zeker dat je door wilt gaan?',
        'exactBarNameVerify' => 'Exacte naam van bar om te verwijderen (Verificatie)',
        'incorrectNameShouldBe' => 'Incorrecte naam, zou moeten zijn: \':name\'',
        'kioskManagement' => 'Kiosk management',
        'startKiosk' => 'Start kiosk',
        'startKioskDescription' => 'Hier kun je kiosk-modus starten. Als je kiosk-modus start, wordt je hier uitgelog van je persoonlijke account, en wordt een centrale terminal interface gestart waarmee iedereen producten kan kopen. Deze modus blijft actief totdat je het handmatig uitzet door normaals uit te loggen op dit apparaat.',
        'startKioskConfirm' => 'Bevestig om kiosk-modus te starten',
        'startKioskConfirmDescription' => 'Het starten van kiosk-modus maakt het voor iedereen die toegang heeft tot deze machine mogelijk om producten te kopen voor elke gebruiker.',
        'kioskSessions' => 'Kiosk sessies',
        'kioskSessionsDescription' => 'Deze pagina toont de actieve en beëindigde kiosksessies voor deze bar. Klik op een actieve sessies om details te zien of om de sessie te beëindigen. Beëindigde sessies worden na enige tijd automatisch vergeten.',
        'expireAllKioskSessionsQuestion' => 'Weet je zeker dat je alle sessies voor deze kiosk wilt beëindigen? Dit zal alle kiosks voor deze bar uitloggen.',
        'generatePoster' => 'Bar poster',
        'generatePosterDescription' => 'Creëer een poster voor deze bar om aan de muur te hangen. Bezoekers kunnen dan gemakklijk gebruik kunnen maken van :app en kunnen lid worden van deze bar door een QR code te scannen met hun mobiele telefoon.',
        'showCodeOnPoster' => 'Toon code om lid te worden op de poster',
        'lowBalanceText' => 'Negatief saldo tekst',
        'lowBalanceTextPlaceholder' => 'Je hebt op dit moment een negatief saldo. Waardeer je saldo alsjeblieft op voordat je nieuwe producten koopt.',
        'allPurchases' => 'Alle aankopen',
        'purchases' => 'Aankopen',
        'purchasesDescription' => 'Deze pagina toont een geschiedenis van alle productaankopen in deze bar.',
        'exportPurchasesTitle' => 'Exporteer aankopen',
        'exportPurchasesDescription' => 'Op deze pagina kun je alle aankopen die gemaakt zijn in deze bar naar een bestand exporteren.',
        'noPurchases' => 'Geen aankopen',
        'poster' => [
            'thisBarUses' => 'Deze bar gebruikt',
            'toDigitallyManage' => 'om digitaal betalingen en inventaris te beheren voor consumpties:',
            'scanQr' => 'scan de QR code met je telefoon, word lid en doe een aankoop',
            'orVisit' => 'Of bezoek',
        ],
        'buy' => [
            'forMe' => 'Koop zelf',
            'forOthers' => 'Voor anderen/meer',
        ],
        'advancedBuy' => [
            'tapProducts' => 'Selecteer producten die je voor iemand wilt kopen.',
            'tapUsers' => 'Druk op leden om de producten toe te voegen.',
            'tapBuy' => 'Druk op de koop knop om de aankoop te voltooien.',
            'addToCartFor' => 'Voeg geselecteerde aan winkelwagen toe voor',
            'searchUsers' => 'Zoek leden',
            'searchingFor' => 'Zoeken naar :term',
            'noUsersFoundFor' => 'Geen leden gevonden voor :term',
            'inCart' => 'In winkelwagen',
            'buyProducts#' => '{0} Koop geen producten|{1} Koop product|[2,*] Koop :count producten',
            'buyProductsUsers#' => '{0} Koop geen producten voor :users leden|{1} Koop product voor :users leden|[2,*] Koop :count producten voor :users leden',
            'pressToConfirm' => 'Tik om te bevestigen',
            'boughtProducts#' => '{0} Geen producten gekocht.|{1} Eén product gekocht.|[2,*] :count producten gekocht.',
            'boughtProductsUsers#' => '{0} Geen producten gekocht voor :users leden.|{1} Eén product gekocht voor :users leden.|[2,*] :count producten gekocht voor :users leden.',
            'pageCloseWarning' => 'Je hebt producten geselecteerd of hebt producten in de winkelwagen die nog niet gekocht zijn. Je moet geselecteerde producten eerst aan ten minste één gebruiker toevoegen en daarna op de Koop knop drukken om de bestelling te voltooien, of de selectie gaat verloren.',
        ],
        'links' => [
            'title' => 'Handige links',
            'description' => 'Deze pagina geeft een lijst handige deelbare links voor deze bar. Je kunt deze links bijvoorbeeld delen via e-mail of printen op een poster. Sommige van deze links maken het mogelijk om gebruikers naar specifieke normaal verborgen paginas of doelen te sturen.<br><br>Let er alsjeblieft op dat sommige links veranderen bij het aanpassen van bar instellingen, en sommige links bevatten geheime stukjes.',
            'linkBar' => 'Bar hoofdpagina',
            'linkBarAction' => 'Bezoek :bar',
            'linkJoinBar' => 'Nodig nieuwe gebruiker uit',
            'linkJoinBarAction' => 'Wordt lid van :bar',
            'linkJoinBarCode' => 'Nodig nieuwe gebruiker uit (met code)',
            'linkJoinBarCodeAction' => 'Wordt lid van :bar',
            'linkQuickWallet' => 'Toon primaire persoonlijke portemonnee',
            'linkQuickWalletAction' => 'Bekijk je persoonlijke portemonnee',
            'linkQuickTopUp' => 'Primaire persoonlijke portemonnee opwaarderen',
            'linkQuickTopUpAction' => 'Portemonnee opwaarderen',
            'linkVerifyEmail' => 'Verifieer e-mailadressen',
            'linkVerifyEmailAction' => 'Verifieer je e-mailadres',
        ],
        'checklist' => 'Bar checklist',
    ],

    /**
     * Bar membership page.
     */
    'barMember' => [
        'title' => 'Lidmaatschap',
        'memberSettings' => 'Lidmaatschap instellingen',
        'showInBuy' => 'Zichtbaar in koopscherm',
        'showInKiosk' => 'Zichtbaar in kiosk',
        'updated' => 'Je instellingen zijn opgeslagen.',
        'visibility' => 'Zichtbaarheid',
        'visibilityDescription' => 'Hieronder kun je jouw zichtbaarheid als barlid instellen. Zichtbaarheid uitschakelen is handig als je wilt voorkomen dat gebruikers producten namens jou kopen. Het is aanbevolen om alle schakelaars aan te laten.<br><br>Zichtbaarheid in het koopscherm specificeert of je getoont wordt in de lijst gebruikers wanneer je producten koopt met je telefoon. Zichtbaarheid in de kiosk specificeert of je getoont wordt in de lijst gebruikers op een centraal bar-kiosk apparaat.',
    ],

    /**
     * Kiosk page.
     */
    'kiosk' => [
        'loading' => 'Kiosk laden',
        'selectUser' => 'Selecteer lid',
        'searchUsers' => 'Zoek leden',
        'searchingFor' => 'Zoeken :term...',
        'noUsersFoundFor' => 'Niemand voor :term',
        'firstSelectUser' => 'Selecteer links een lid om een aankoop voor te doen',
        'selectProducts' => 'Selecteer producten',
        'buyProducts#' => '{0} Koop geen producten|{1} Koop product|[2,*] Koop :count producten',
        'buyProductsUsers#' => '{0} Koop geen producten voor :users leden|[1,*] Koop :count× voor :users leden',
        'deselect' => 'Deselect',
        'backToKiosk' => 'Terug naar kiosk',
    ],

    /**
     * Community/bar statistics pages.
     */
    'stats' => [
        'title' => 'Statistieken',
        'barStats' => 'Bar statistieken',
        'communityStats' => 'Groep statistieken',
        'activePastHour' => 'Actief afgelopen uur',
        'activePastDay' => 'Actief afgelopen dag',
        'activePastWeek' => 'Actief afgelopen week',
        'activePastMonth' => 'Actief afgelopen maand',
        'productsPastHour' => 'Producten afgelopen uur',
        'productsPastDay' => 'Producten afgelopen dag',
        'productsPastWeek' => 'Producten afgelopen week',
        'productsPastMonth' => 'Producten afgelopen maand',
    ],

    /**
     * Bar member pages.
     */
    'barMembers' => [
        'title' => 'Bar leden',
        'description' => 'Op deze pagina zie je een overzicht van alle bar leden.<br>Als je op een lid klikt kun je dit lid verwijderen, of zijn/haar rol aanpassen.',
        'nickname' => 'Weergavenaam',
        'nicknameDescription' => 'Je kunt een aangepaste weergavenaam voor jezelf instellen. Als je een weergavenaam instelt, wordt je volledige naam verborgen en zal je aangepaste naam getoont worden in koop- en schermen. Deze functie is bedoeld voor speciale gebruikers/accounts waar het tonen van hun eigen naam niet logisch is. Stel om verwarring te voorkomen een duidelijke naam in of beter, stel geen naam in.',
        'noMembers' => 'Deze bar heeft geen leden...',
        'memberSince' => 'Lid sinds',
        'lastVisit' => 'Laatste bezoek',
        'deleteQuestion' => 'Je staat op het punt dit lid te verwijderen van deze bar. Weet je zeker dat je door wilt gaan?',
        'memberRemoved' => 'Het lid is verwijderd.',
        'memberUpdated' => 'Lid aanpassingen opgeslagen.',
        'incorrectMemberRoleWarning' => 'Het toewijzen van de verkeerde rol aan een gebruiker kan voor serieuze beveiligingsproblemen zorgen.',
        'ownRoleDowngradeWarning' => 'Bij het verlagen van je eigen rol verlies je waarschijnlijk beheerstoegang tot deze bar.',
        'confirmRoleChange' => 'Bevestig rol aanpassing voor bar lid',
        'confirmSelfDelete' => 'Bevestig om jezelf als bar lid uit te schrijven, waardoor je je rol verliest',
        'cannotDemoteLastManager' => 'Je kunt de rol voor het laatste bar lid met deze (of een meer permissieve) management rol niet degraderen.',
        'cannotEditMorePermissive' => 'Je kunt een bar lid met een meer permissieve rol dan jezelf niet aanpassen.',
        'cannotSetMorePermissive' => 'Je kunt geen meer permissieve rol voor een bar lid instellen dan je eigen rol.',
        'cannotDeleteLastManager' => 'Je kunt het laatste bar lid met deze (of een meer permissieve) management rol niet uitschrijven.',
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
     * Password disable page.
     */
    'passwordDisable' => [
        'title' => 'Wachtwoord uitschakelen',
        'description' => 'Vul je huidige wachtwoord in in het veld hieronder om inloggen met een wachtwoord uit te schakelen. Je kunt daarna blijven inloggen met een link die gestuurd wordt naar je e-mail inbox.',
    ],

    /**
     * Password reset page.
     */
    'passwordReset' => [
        'enterResetToken' => 'Vul alsjeblieft je wachtwoord reset token in. '
            . 'Deze token is te vinden in de e-mail die je ontvangen hebt met wachtwoord reset instructies.',
        'enterNewPassword' => 'Vul alsjeblieft het nieuwe wachtwoord in dat je vanaf nu wilt gebruiken.',
        'invalid' => 'Token onbekend. Misschien is de token reeds verlopen.',
        'expired' => 'De token is verlopen. Vraag alsjeblieft een nieuwe wachtwoord reset aan.',
        'used' => 'Je wachtwoord is al aangepast met deze token.',
        'changed' => 'Weer zo goed als nieuw! Je wachtwoord is aangepast.',
    ],

    /**
     * Kiosk join pages.
     */
    'kioskJoin' => [
        'title' => 'Nieuwe gebruiker / lid worden',
        'joinBar' => 'Lid worden van :bar',
        'description' => 'Nieuwe gebruikers kunnen zichzelf toevoegen aan deze bar door een account te registreren.',
        'scanQr' => 'Scan de QR-code hieronder met je telefoon om te starten:',
        'orUrl' => 'Of ga naar de volgende link in je browser:',
    ],

    /**
     * Privacy policy page.
     */
    'privacy' => [
        'title' => 'Privacy',
        'description' => 'Wanneer je onze service gebruikt, vertrouw je ons met je gegevens. We begrijpen dat dit een grote verantwoordelijkheid is.<br><br>Het privacybeleid (Privacy Policy) hieronder is bedoeld om je te helpen begrijpen hoe we jouw gegevens beheren.',
        'questions' => 'Vragen?',
        'questionsDescription' => 'Als je verdere vragen hebt over ons privacybeleid (Privacy Policy) of over jouw privacy, neem gerust contact met ons op.',
    ],

    /**
     * Terms of Service page.
     */
    'terms' => [
        'title' => 'Voorwaarden',
        'description' => 'Wanneer je onze service gebruikt, ga je akkoord met onze servicevoowaarden (Terms of Service) zoals hieronder getoond.',
        'questions' => 'Vragen?',
        'questionsDescription' => 'Als je verdere vragen hebt over onze servicevoorwaarden (Terms of Service), neem gerust contact met ons op.',
    ],

    /**
     * License page.
     */
    'license' => [
        'title' => 'Licentie',
        'description' => 'Het Barbapappa software project is open-source, en is uitgebracht onder de GNU AGPL-3.0 licentie (License). Deze licentie beschrijft wat wel en niet is toegestaan met de publieke broncode voor dit softwareproject. Deze licentie betreft niet de gebruiksinformatie verwerkt binnen deze applicatie.<br><br>Lees de volledige licentie hieronder, of check de licentie samenvatting voor een snel overzicht.',
        'questions' => 'Vragen?',
        'questionsDescription' => 'Als je verdere vragen hebt over de gebruikte licentie (License), neem gerust contact met ons op. Je kunt de licentie ook bekijken in platte text leesbaar op elk willekeurig apparaat.',
        'plainTextLicense' => 'Licentie in platte text',
        'licenseSummary' => 'Licentie samenvatting (Engels)',
    ],

    /**
     * Contact page.
     */
    'contact' => [
        'title' => 'Contact',
        'contactUs' => 'Neem contact op',
        'description' => 'Gebruik de volgende gegevens om contact op te nemen met Barbapappa:',
        'issuesDescription' => 'Deze applicatie is open-source, het ontwikkelprocess is open. Bekijk de broncode of the issuelijst via de links hieronder.',
        'issueList' => 'Issue lijst',
        'newIssueMail' => 'Rapporteer issue',
        'thisAppIsOpenSource' => 'Deze applicatie is Open Source',
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

    /**
     * About page.
     */
    'about' => [
        'title' => 'Over',
        'aboutUs' => 'Over ons',
        'description' => ':app is een digitaal bar management applicatie om een gebruikergecontroleerd platform te faciliteren voor aankoopverwerking, betaalverwerking en inventarisbeheer.<br><br>:app is een volledig geautomatiseerd platform voor kleine zelfbeheerde bars en groepen, om gedoe met handmatig bijhouden van aankopen door middel van turven op papier weg te nemen.<br><br>Voor interesse in het gebruik van dit platform voor je eigen bar of groep, stuur ons een berichtje!',
        'developedBy' => 'Dit project wordt ontwikkeld & beheerd door',
        'sourceDescription' => 'Ik maak de software die ik ontwikkel open-source. Het complete project met alle broncode is gratis beschikbaar, voor iedereen. Ik geloof er in dat het belangrijk is om iedereen toegang te geven tot het inspecteren, aanpassen, verbeteren, bijdragen en verifiëren zonder restricties.',
        'sourceAt' => 'De laatste broncode is te vinden op GitLab',
        'withLicense' => 'Dit project is uitgebracht onder de volgende licentie',
        'usedTechnologies' => 'Een aantal awesome technologieën die gebruikt zijn',
        'noteLaravel' => 'backend framework',
        'noteSemanticUi' => 'frontend theming framework',
        'noteGlyphicons' => 'iconen & symbolen',
        'noteFlags' => 'vlaggetjes',
        'noteGetTerms' => 'voorwaarden & privacy policy template',
        'noteEDegen' => 'suggestie \'Barbapappa\'',
        'otherResources' => 'Andere awesome bronnen zijn',
        'donate' => 'Er is veel werk gestoken in dit project.<br>Wil je me een koffie doneren?',
        'thanks' => 'Bedankt voor het gebruik maken van dit project,<br>dat is geweldig!',
        'copyright' => 'Copyright © :app :year.<br>Alle rechten voorbehouden.',
    ],

    /**
     * Error pages.
     */
    'errors' => [
        // TODO: move noPermission view into this
        '401' => [
            'title' => '401 Unauthorized',
            'description' => 'Je hebt een verkeerde afslag genomen.<br />Je hebt geen rechten voor deze pagina.',
        ],
        '403' => [
            'title' => '403 Forbidden',
            'description' => 'Je hebt een verkeerde afslag genomen.<br />Je hebt geen rechten voor deze pagina.',
        ],
        '404' => [
            'title' => '404 Not Found',
            'description' => 'Je hebt een verkeerde afslag genomen.<br />De pagina die je probeert te bezoeken kan niet gevonden worden.',
        ],
        '419' => [
            'title' => '419 Page Expired',
            'description' => 'Oeps! Deze pagina is verlopen.',
        ],
        '429' => [
            'title' => '429 Too Many Requests',
            'description' => 'Oeps! Er zijn recent teveel verzoeken gestuurd voor deze pagina via dit netwerk. Wacht even voordat je het opnieuw probeert.',
        ],
        '500' => [
            'title' => '500 Server Error',
            'description' => '<i>Houston, we have a problem!</i><br><br>Er is iets fout gegaan aan onze kant. De administrators zijn op de hoogte gesteld en onderzoeken het probleem.',
        ],
        '503' => [
            'title' => '503 Service Unavailable',
            'description' => '<i>Houston, we have a problem!</i><br><br>Er is iets fout gegaan aan onze kant, daarom kunnen we je verzoek niet behandelen. De administrators zijn op de hoogte gesteld en onderzoeken het probleem.',
        ],
    ],
];
