<?php

/**
 * Pages and their names.
 */
return [
    'pages' => 'Pagina\'s',
    'index' => 'Hoofdpagina',
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
    'about' => 'Over',
    'contact' => 'Contact',
    'contactUs' => 'Neem contact op',

    /**
     * Dashboard page.
     */
    'dashboard' => [
        'title' => 'Dashboard',
        'yourPersonalDashboard' => 'Je persoonlijke dashboard',
        'noBarsOrCommunities' => 'Geen bars of groepen',
        'nothingHereNoMemberUseExploreButtons' => 'Er is hier niks om te zien omdat je nog geen lid bent van een bar of groep. Vind de jouwe met de onderstaande knoppen.',
    ],

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
            'verifySent' => 'Een nieuwe verificatie-e-mail zal zo spoedig mogelijk verzonden worden.',
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
     * Explore pages.
     */
    'explore' => [
        'title' => 'Ontdek',
        'exploreBars' => 'Ontdek bars',
        'exploreCommunities' => 'Ontdek groepen',
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
        'deleteCommunity' => 'Groep verwijderen',
        'join' => 'Inschrijven',
        'yesJoin' => 'Ja, inschrijven',
        'joined' => 'Ingeschreven',
        'notJoined' => 'Niet ingeschreven',
        'hintJoin' => 'Je maakt nog geen deel uit van deze groep.',
        'joinedClickToLeave' => 'Klik om uit te schrijven.',
        'joinQuestion' => 'Wil je je bij deze groep inschrijven?',
        'joinedThisCommunity' => 'Je bent ingeschreven bij deze groep.',
        'cannotSelfEnroll' => 'Je kunt jezelf niet inschrijven voor deze groep, de functie is uitgeschakeld.',
        'leaveQuestion' => 'Weet je zeker dat je je wilt uitschrijven bij deze groep?',
        'leftThisCommunity' => 'Je bent uitgeschreven bij deze groep.',
        'protectedByCode' => 'Deze groep is beveiligd met een code. Vraag er naar bij de groep, of scan de groep QR-code als deze beschikbaar is.',
        'protectedByCodeFilled' => 'Deze groep is beveiligd met een code. We hebben de code voor je ingevuld.',
        'incorrectCode' => 'Verkeerde groep code.',
        'namePlaceholder' => 'Viking groep',
        'descriptionPlaceholder' => 'Welkom bij dé Viking groep!',
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
            'toDigitallyManage' => 'om digitaal betalingen en inventaris te beheren voor consumpties',
            'scanQr' => 'scan de QR code met je telefoon, wordt lid en doe een aankoop',
            'orVisit' => 'Of bezoek',
        ],
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
        'manage' => 'Beheer valutas',
    ],

    /**
     * Product pages.
     */
    'products' => [
        'title' => 'Producten',
        'all' => 'Alle producten',
        'search' => 'Producten zoeken',
        'noProducts' => 'Geen producten...',
        'manageProduct' => 'Beheer product',
        'manageProducts' => 'Beheer producten',
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
        'permanentlyDeleted' => 'Het product is permanent verwijderd.',
        'namePlaceholder' => 'Luxe Sap',
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
        'supportWithdrawDescription' => 'Ondersteun opnames. Laat gebruikers geld opnemen van portemonnees binnen deze economie.',
        'backToServices' => 'Terug naar betaalservices',
        'viewService' => 'Service bekijken',
        'unknownService' => 'Onbekende betaalservice',
        'startedWillUseOldDetails' => 'Betalingen die al geïnitieerd zijn gebruiken mogelijk nog oude gegevens, ook nadat je ze hier aanpast.',
        'startedWillComplete' => 'Er zullen geen nieuwe betalingen geaccepteerd worden met deze service. Betaling die al geïnitieerd zijn zullen echter wel nog worden afgemaakt.',
        'amountToTopUpInCurrency' => 'Bedrag om te storten in :currency',
        'selectPaymentServiceToUse' => 'Betaalmethode',
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
        'economySelectDescription' => 'Portemonnees voor deze groep zijn onderverdeeld per economie.<br>Selecteer een economie om je portemonnees te beheren.',
        'noWallets' => 'Je hebt nog geen portemonnees...',
        'namePlaceholder' => 'Mijn persoonlijke portefeuille',
        'nameDefault' => 'Mijn nieuwe portefeuille',
        'createWallet' => 'Portemonnee aanmaken',
        'walletCreated' => 'De portemonnee is aangemaakt.',
        'walletUpdated' => 'Portemonnee aanpassingen opgeslagen.',
        'deleteQuestion' => 'Je staat op het punt deze portemonnee te verwijderen. Weet je zeker dat je door wilt gaan?',
        'cannotDeleteNonZeroBalance' => 'Om deze portemonnee te verwijderen moet het saldo precies :zero zijn.',
        'walletDeleted' => 'De portemonnee is verwijderd.',
        'cannotCreateNoCurrencies' => 'Je kunt nu geen portemonnee aanmaken. De groep administrator heeft geen valuta geconfigureerd waarbij dit is toegestaan.',
        'all' => 'Alle portemonnees',
        'view' => 'Portemonnee bekijken',
        'transfer' => 'Overboeken',
        'transferToSelf' => 'Overboeken naar portemonnee',
        'transferToUser' => 'Overboeken naar gebruiker',
        'toSelf' => 'Naar portemonnee',
        'toUser' => 'Naar gebruiker',
        'topUp' => 'Top-up portemonnee',
        'successfullyTransferredAmount' => ':amount succesvol overgeboekt naar :wallet',
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
        'viewTransaction' => 'Bekijk transactie',
        'linkedTransaction' => 'Gekoppelde transactie',
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
     * Payment pages.
     */
    'payments' => [
        'title' => 'Betalingen',
        'description' => 'Hier zie je alle betalingen in behandeling en afgehandeld die je gemaakt hebt in alle groepen.',
        'details' => 'Betaling details',
        'progress' => 'Voortgang van betaling',
        'last#' => '{0} Laatste betalingen|{1} Laatste betaling|[2,*] Laatste :count betalingen',
        'backToPayment' => 'Terug naar betaling',
        'backToPayments' => 'Terug naar betalingen',
        'requiringAction#' => '{0} Geen vereisen actie|{1} Betaling vereist actie|[2,*] :count vereisen actie',
        'inProgress' => 'Betaling in behandeling',
        'inProgress#' => '{0} Geen in behandeling|{1} Betaling in behandeling|[2,*] :count in behandeling',
        'inProgressDescription' => 'Deze betaling is nog in behandeling.',
        'settled#' => '{0} Geen afgehandeld|{1} Betaling afgehandeld|[2,*] :count afgehandeld',
        'noPayments' => 'Je hebt nog geen betalingen gedaan',
        'viewPayment' => 'Betaling bekijken',
        'unknownPayment' => 'Onbekende betaling',
        'handlePayments' => 'Behandel betalingen',
        'handleCommunityPayments' => 'Behandel groep betalingen',
        'paymentsToApprove' => 'Betalingen wachtend op actie',
        'paymentsWaitingForAction' => 'Een aantal betalingen wachten op actie van een groepsbeheerder. Behandel deze alsjeblieft zo snel mogelijk.',
        'paymentsToApproveDescription' => 'De volgende betalingen wachten op actie van een groepsbeheerder. Verwerk deze alsjeblieft zo snel mogelijk om betalingen vlot te laten verlopen.',
        'state' => [
            'init' => 'Geïnitieerd',
            'pendingManual' => 'In afwachting (handmatig)',
            'pendingAuto' => 'In afwachting (automatisch)',
            'processing' => 'Bezig met verwerken',
            'completed' => 'Voltooid',
            'revoked' => 'Ingetrokken',
            'rejected' => 'Afgekeurd',
            'failed' => 'Mislukt',
            'cancelled' => 'Geannuleerd',
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
        'notJoined' => 'Niet ingeschreven',

        'hintJoin' => 'Je maakt nog geen deel uit van deze bar.',
        'joinedClickToLeave' => 'Klik om uit te schrijven.',
        'joinQuestion' => 'Wil je je bij deze bar inschrijven?',
        'alsoJoinCommunity' => 'Ook inschrijven bij de bijbehorende groep',
        'alreadyJoinedTheirCommunity' => 'Je bent al lid van de bijbehorende groep',
        'joinedThisBar' => 'Je bent ingeschreven bij deze bar.',
        'cannotSelfEnroll' => 'Je kunt jezelf niet inschrijven voor deze bar, de functie is uitgeschakeld.',
        'leaveQuestion' => 'Weet je zeker dat je je wilt uitschrijven bij deze bar?',
        'leftThisBar' => 'Je bent uitgeschreven bij deze bar.',
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
        'quickBuy' => 'Direct kopen',
        'boughtProductForPrice' => ':product gekocht voor :price',
        'noDescription' => 'Deze bar heeft geen beschrijving',
        'barInfo' => 'Bar informatie',
        'viewBar' => 'Bar bekijken',
        'deleted' => 'De bar is verwijderd.',
        'deleteQuestion' => 'Je staat op het punt deze bar permanent te verwijderen. Alle leden waaronder jezelf zullen toegang tot de bar verliezen, en product transacties zullen niet meer gelinkt kunnen worden aan deze bar. De producten en portemonnees van leden blijven bestaan als onderdeel van de economie die gebruikt werd binnen deze bar. Weet je zeker dat je door wilt gaan?',
        'exactBarNameVerify' => 'Exacte naam van bar om te verwijderen (Verificatie)',
        'incorrectNameShouldBe' => 'Incorrecte naam, zou moeten zijn: \':name\'',
        'generatePoster' => 'Creëer bar poster',
        'generatePosterDescription' => 'Creëer een poster voor deze bar om aan de muur te hangen. Bezoekers kunnen dan gemakklijk gebruik kunnen maken van :app en kunnen lid worden van deze bar door een QR code te scannen met hun mobiele telefoon.',
        'showCodeOnPoster' => 'Toon code om lid te worden op de poster',
        'poster' => [
            'thisBarUses' => 'Deze bar gebruikt',
            'toDigitallyManage' => 'om digitaal betalingen en inventaris te beheren voor consumpties',
            'scanQr' => 'scan de QR code met je telefoon, wordt lid en doe een aankoop',
            'orVisit' => 'Of bezoek',
        ],
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
        'activePastMonth' => 'Actief afgelopen maand',
        'productsPastHour' => 'Producten afgelopen uur',
        'productsPastDay' => 'Producten afgelopen dag',
        'productsPastMonth' => 'Producten afgelopen maand',
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
