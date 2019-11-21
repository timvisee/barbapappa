<?php

/**
 * Pages and their names.
 */
return [
    'pages' => 'Parchments',
    'emailPreferences' => 'E-bottle preferences',
    'emailPreferencesNotYetImplemented' => 'Email preferences are not yet implemented, please check back later.',
    // TODO: move to misc?
    'communities' => 'Crews',
    // TODO: move to misc?
    'bars' => 'Bars',
    'account' => 'Ye ship',
    'yourAccount' => 'Ye ship',
    'requestPasswordReset' => 'Request passcode reset',
    'changePassword' => 'Change passcode',
    'changePasswordDescription' => 'First enter yer ol\' n\' passcode. Den enter ye shiny, fresh n\' new passcode to take abroad.',

    /**
     * App pages.
     */
    'app' => [
        'manageApp' => 'Manage app',
    ],

    /**
     * Index page.
     */
    'index' => [
        'title' => 'Home port',
        'emailAndContinue' => 'Enter yer email address to login or register.',
        'backToIndex' => 'Back to home page',
    ],

    /**
     * Dashboard page.
     */
    'dashboard' => [
        'title' => 'Home port',
        'yourPersonalDashboard' => 'Ye home port',
        'noBarsOrCommunities' => 'No bars \'o crews',
        'nothingHereNoMemberUseExploreButtons' => 'There be nothing to show here \'cause ye be no matey of any bar \'o crew. Find yer using th\' buttons.',
    ],

    /**
     * Last page.
     */
    'last' => [
        'title' => 'Back to bar',
        'noLast' => 'Ye visited no bar yet, enter one now!',
    ],

    /**
     * Profile page.
     */
    'profile' => [
        'name' => 'Manifest'
    ],

    /**
     * Profile edit page.
     */
    'editProfile' => [
        'name' => 'Edit manifest',
        'updated' => 'Yer manifest be updated.',
        'otherUpdated' => 'Th\' manifest be updated.',
    ],

    /**
     * Account page.
     */
    'accountPage' => [
        'description' => 'Th\' page shows an overview of ye ship.',
        'email' => [
            'description' => 'Th\' page shows an overview of ye e-bottle coordinates.',
            'yourEmails' => 'Ye e-bottles',
            'unverifiedEmails' => 'Unverified e-bottles',
            'verifyEmails' => 'Verify e-bottles',
            'unverifiedDescription' => 'This page lists yer e-bottle coordinates that still be unverified. Verify them now.',
            'resendVerify' => 'Sally forth verification',
            'unverified#' => '{0} No unverified emails|{1} Unverified email|[2,*] :count unverified emails',
            'verify#' => '{0} Verify no e-bottle coordinates|{1} Verify e-bottle coordinate|[2,*] Verify :count e-bottle coordinates',
            'verifiedDescription' => 'We sent a new verification message to yer unverified e-bottle coordinates. Tap th\' link in th\' message to complete verification.',
            'iVerifiedAll' => 'I verified all',
            'verifySent' => 'A fresh verification e-bottle be sally forth.',
            'alreadyVerified' => 'Th\' e-bottle coordinate be verified.',
            'allVerified' => 'All yer e-bottle coordinates be verified.',
            'cannotDeleteMustHaveOne' => 'Ye no delete \'his e-bottle coordinate, ye must be one coordinate.',
            'cannotDeleteMustHaveVerified' => 'Ye no delete \'his e-bottle coordinate, ye must be one verified coordinate.',
            'deleted' => 'Th\' e-bottle coordinate be deleted.',
            'deleteQuestion' => 'Ye be sure ye want to sunk dis e-bottle address?',
            'backToEmails' => 'Back to e-bottles',
        ],
        'addEmail' => [
            'title' => 'Add e-bottle coordinate',
            'description' => 'Enter yer e-bottle coordinate to conquer.',
            'added' => 'E-bottle coordinate added. \'ll verification be sent.',
        ],
        'backToAccount' => 'Back to account',
    ],

    /**
     * Explore pages.
     */
    'explore' => [
        'title' => 'Explore',
        'exploreBars' => 'Explore bars',
        'exploreCommunities' => 'Explore crews',
        'exploreBoth' => 'Explore communities & bars',
    ],

    /**
     * Community pages.
     */
    'community' => [
        'yourCommunities' => 'Ye crews',
        'noCommunities' => 'Nay crews asea...',
        'viewCommunity' => 'View crew',
        'viewCommunities' => 'View crews',
        'visitCommunity' => 'Visit crew',
        'createCommunity' => 'Create crew',
        'editCommunity' => 'Edit crew',
        'deleteCommunity' => 'Sink crew',
        'join' => 'Join',
        'yesJoin' => 'Yey, sail ho!',
        'joined' => 'Joined',
        'notJoined' => 'Strangers!',
        'hintJoin' => 'Ye be not part of th\' crew.',
        'joinedClickToLeave' => 'Click to be sunk.',
        'joinQuestion' => 'Ye like be joined th\' crew?',
        'joinedThisCommunity' => 'Ye joined th\' crew.',
        'leaveQuestion' => 'Ye be sure to sink th\' crew?',
        'cannotSelfEnroll' => 'Ye cannot join dis crew yerself, it be disabled.',
        'leftThisCommunity' => 'Ye sunk th\' crew.',
        'cannotLeaveStillBarMember' => 'Ye nay leave dis crew, because yer still a member of a bar in dis crew',
        'protectedByCode' => 'Dis crew be protected by a secret. Request it at yer crew, or use yer binoculars to scan the crew Q-ARRRR code if available.',
        'protectedByCodeFilled' => 'Dis crew be protected by a secret. We filled it for ye.',
        'incorrectCode' => 'Crew code be incorrect.',
        'namePlaceholder' => 'Seven Seas',
        'descriptionPlaceholder' => 'Say ho to yer Seven Seas crew!',
        'slugDescription' => 'Ye slug allows ye to create \'n easy to remember URL to access dis crew, by defining a short keyword.',
        'slugDescriptionExample' => 'Dis could simplify ye crew URL:',
        'slugPlaceholder' => 'seven-seas',
        'slugFieldRegexError' => 'Dis slug must start with n alphabetical character.',
        'codeDescription' => 'With a crew secret, ye prevent random pirates from joining. To join the crew, users be required to enter th\' secret.',
        'showExploreDescription' => 'List on public \'Explore crews\' page',
        'selfEnrollDescription' => 'Allow self enrollment (wit\' code if specified)',
        'joinAfterCreate' => 'Join th\' crew after creating.',
        'created' => 'Th\' crew be created.',
        'updated' => 'Th\' crew be updated.',
        'economy' => 'Booty',
        'goTo' => 'Go to crew',
        'backToCommunity' => 'Back to crew',
        'noDescription' => 'Dis crew be nay description',
        'communityInfo' => 'Crew info',
        'manageCommunity' => 'Manage crew',
        'inCommunity' => 'in crew',
        'deleted' => 'Th\' crew be sunk.',
        'deleteQuestion' => 'Yer \'bout to permanently sink dis crew. All mateies including yerself will lose access to it. All bars, booties, matey wallets, products and related entities that be used within dis crew will be deleted as well. Ye be sure ye want to continue?',
        'deleteBlocked' => 'Yer \'bout to permanently sink this crew. ye must sink th\' entities listed below first before ye be continue with deleting dis crew.',
        'exactCommunityNameVerify' => 'Exact name of crew to sink (Verification)',
        'incorrectNameShouldBe' => 'Incorrect name, should be: \':name\'',
        'cannotDeleteDependents' => 'Dis community cannot be sunk, as entities be depending on it which nay just be deleted.',
        'generatePoster' => 'Create crew poster',
        'generatePosterDescription' => 'Create a poster for dis crew to hang on a wall. Fellow mateies will then be able to easily use :app and join dis crew by scanning a QR code with their handheld phoning device.',
        'showCodeOnPoster' => 'Show code to join dis crew on th\' poster',
        'posterBarPreferred' => 'It be usually preferred to generate a poster for a bar instead of a crew, as mateies joining a crew doesn\'t give them acces to purchasing products without joining a bar as well. Visit the management hub of a specific bar to create a poster for it.',
        'poster' => [
            'thisCommunityUses' => 'Dis crew uses',
            'toDigitallyManage' => 'to digitally manage booty and stock for consumptions',
            'scanQr' => 'scan the QR code below to join and make a purchase',
            'orVisit' => 'Or see',
        ],
        'checklist' => 'Crew checklist',
    ],

    /**
     * Community member pages.
     */
    'communityMembers' => [
        'title' => 'Crew maties',
        'description' => 'Dis page shows th\' overview o\' all crew maties.<br>Clicking a matey allows ye to remove the matey, or change be rank.',
        'noMembers' => 'Dis crew be nay maties...',
        'memberSince' => 'Matey since',
        'lastVisit' => 'Last visit',
        'deleteQuestion' => 'Yer \'bout to remove dis matey from our crew. Ye be sure ye want to continue?',
        'memberRemoved' => 'Th\' matey be sunk.',
        'memberUpdated' => 'Matey changes saved.',
        'incorrectMemberRoleWarning' => 'Assigning n\' incorrect role that be too permissive to a matey may introduce significant security issues.',
        'ownRoleDowngradeWarning' => 'By downgrading yer own role ye might lose management access to dis crew. Be very careful.',
        'confirmRoleChange' => 'Confirm role change for crew matey',
        'confirmSelfDelete' => 'Confirm to kick yerself as crew matey, ye be lose yer role',
        'cannotDemoteLastManager' => 'Ye nay demote th\' last crew matey wit\' dis (or a moar permissive) management role.',
        'cannotEditMorePermissive' => 'You nay edit a crew matey with a moar permissive role than yerself.',
        'cannotSetMorePermissive' => 'You nay set a moar permissive role for a crew matey than yer current role.',
        'cannotDeleteLastManager' => 'Ye nay sink th\' last crew matey wit\' dis (or a moar permissive) management role.',
    ],

    /**
     * Community economy pages.
     */
    'economies' => [
        'title' => 'Booties',
        'description' => 'Dis page shows th\' overview o\' all booties of dis crew.<br>Click a booty to manage it, or create new booty for a new bar.',
        'manage' => 'Manage booties',
        'noEconomies' => 'Dis crew be nay booties...',
        'createEconomy' => 'Create booty',
        'economyCreated' => 'Th\' booty be created. Please add \'n configure a currency now.',
        'deleteQuestion' => 'Yer \'bout to sink dis booty from our crew. Ye be sure ye want to continue?',
        'deleteBlocked' => 'Yer \'bout to permanently sink this booty. ye must delete th\' entities listed below first before ye be continue with deleting dis booty.',
        'cannotDeleteDependents' => 'Dis booty cannot be sunk, as entities be depending on it which nay just be deleted.',
        'economyDeleted' => 'Th\' booty be sunk.',
        'economyUpdated' => 'Booty changes saved.',
        'namePlaceholder' => 'Our booty',
        'backToEconomy' => 'Back to booty',
        'backToEconomies' => 'Back to booties',
        'inEconomy' => 'in booty',
    ],

    /**
     * Community economy currency pages.
     */
    'currencies' => [
        'title' => 'Currencies',
        'description' => 'Dis page shows \'n overview of currencies in th\' booty.<br>At least one currency must be enabled to use dis booty for a bar.<br>Add new currency, or click one to manage.',
        'change' => 'Change currency',
        'noCurrencies' => 'Dis booty be nay currencies...',
        'createCurrency' => 'Add currency',
        'currencyCreated' => 'Th\' currency be added.',
        'deleteQuestion' => 'Yer \'bout to sink dis currency from dis booty. Ye be sure ye want to continue?',
        'deleteVoidNotice' => 'When ye remove dis booty, all configured product prices for dis currency will be voided in bars dat use dis booty.<br>Ye may want to disable dis currency instead by changing it.',
        'currencyDeleted' => 'Th\' currency be sunk.',
        'currencyUpdated' => 'Currency changes saved.',
        'enabledDescription' => 'Specify whether dis currency be enabled in bars dat arrr using dis booty. If disabled, bar maties won\'t be able to purchase products with dis currency, \'n must use a different currency if available until it is enabled again.',
        'changeCurrencyTitle' => 'Change currency?',
        'changeCurrencyDescription' => 'Ye must only make very small changes to th\' currency properties, to prevent introducing issues where dis currency be used. Consider to add a fresh currency instead.',
        'allowWallets' => 'Allow wallet creation',
        'allowWalletsDescription' => 'With dis option ye specify whether bar mateies be create a new personal wallet for dis currency. Existing wallets always be kept afloat.',
        'manage' => 'Manage currencies',
        'namePlaceholder' => 'Euro',
        'detailDescription' => 'Configure properties for th\' fresh currency. Make sure these be accurate when adding an internationally used currency, because payment services rely on these properties. Some properties cannot be changed anymore after adding.',
        'nameDescription' => 'Provide a currency name, e.g. \'Euro\' or \'Treasures\'',
        'codeDescription' => 'Provide th\' international currency code as defined by ISO 4217 if dis be an international currency. For custom currencies such as \'Treasures\' you should leave it empty.',
        'symbolDescription' => 'Provide a desired symbol for dis currency. E.g. \'€\' or \'T\'',
        'formatDescription' => 'Provide the currency format, to define how :app shows a money amount in dis currency other pirates. E.g. \'€1.0,00\' or \'T1.0\'',
        'code' => 'Currency code',
        'codePlaceholder' => 'EUR',
        'symbolPlaceholder' => '€',
        'format' => 'Currency format',
        'formatPlaceholder' => '€1.0,00',
        'exampleNotation' => 'Example notation',
        'cannotDeleteHasWallet' => 'Ye nay delete dis currency, because a wallet exists using dis currency.',
        'cannotDeleteHasMutation' => 'Ye nay delete dis currency, because a transaction exists using dis currency.',
        'cannotDeleteHasPayment' => 'Ye nay delete dis currency, because a payment exists using dis currency.',
        'cannotDeleteHasService' => 'Ye nay delete dis currency, because a payment service exists using dis currency.',
        'cannotDeleteHasChange' => 'Ye nay delete dis currency, because a booty import change exists using dis currency.',
    ],

    /**
     * Product pages.
     */
    'products' => [
        'title' => 'Products',
        'all' => 'All products',
        'select' => 'Select products',
        'search' => 'Search products',
        'noProducts' => 'Nay products...',
        'noProductsFoundFor' => 'Nay products for :term...',
        'manageProduct' => 'Manage product',
        'manageProducts' => 'Manage products',
        'addProducts' => 'Add products',
        'newProduct' => 'New product',
        'cloneProduct' => 'Clone product',
        'editProduct' => 'Edit product',
        'created' => 'The product be added.',
        'changed' => 'The product be changed.',
        'restoreQuestion' => 'Yer \'bout to afloat dis product to make it available again. Ye be sure ye want to continue?',
        'restored' => 'The product be afloated.',
        'deleteQuestion' => 'Yer \'bout to sink dis product. Ye be sure ye want to continue?',
        'permanentDescription' => 'Tick th\' checkbox below to permanently delete dis product. Ye nay be able to restore it.',
        'permanentlyDelete' => 'Permanently delete product',
        'deleted' => 'The product be trashed.',
        'permanentlyDeleted' => 'The product be permanently deleted.',
        'namePlaceholder' => 'Bear Beer',
        'enabledDescription' => 'Enabled, can be bought',
        'prices' => 'Prices',
        'pricesDescription' => 'Configure prices for dis product in th\' fields below for booties ye want to support.',
        'localizedNames' => 'Localized names',
        'localizedNamesDescription' => 'Configure localized names for dis product in th\' fields below if be different from th\' main name.',
        'search' => 'Search products',
        'backToProducts' => 'Back to products',
        'viewProduct' => 'View product',
        'unknownProduct' => 'Unknown product',
        'recentlyBoughtProducts#' => '{0} Recently bought products|{1} Product bought recently|[2,*] :count products bought recently',
        'type' => [
            'normal' => 'Normal',
            'custom' => 'Custom',
        ],
    ],

    /**
     * Payment service pages.
     */
    'paymentService' => [
        'title' => 'Payment services',
        'service' => 'Payment service',
        'noServices' => 'Nay payment services...',
        'manageService' => 'Manage payment service',
        'manageServices' => 'Manage payment services',
        'serviceType' => 'Payment service type',
        'availableTypes#' => '{0} Nay payment service types available|{1} Available payment service type|[2,*] :count payment service types available',
        'newService' => 'Add service',
        'newChooseType' => 'Please choose th\' type of payment service yer like to configure and add.',
        'editService' => 'Edit service',
        'deleteService' => 'Delete service',
        'created' => 'The payment service be added.',
        'changed' => 'The payment service be changed.',
        'restoreQuestion' => 'Yer \'bout to afloat dis payment service to make it available again. Ye be sure ye want to continue?',
        'restored' => 'The payment service be afloated.',
        'deleteQuestion' => 'Yer \'bout to sink dis payment service. Ye be sure ye want to continue?',
        'permanentDescription' => 'Tick th\' checkbox below to permanently delete dis payment service. Ye nay be able to restore it.',
        'permanentlyDelete' => 'Permanently delete payment service',
        'deleted' => 'The payment service be trashed.',
        'permanentlyDeleted' => 'The payment service be permanently deleted.',
        'enabledDescription' => 'Enabled, can be used',
        'enabledServices#' => '{0} No enabled services|{1} Enabled service|[2,*] :count enabled services',
        'disabledServices#' => '{0} No disabled services|{1} Disabled service|[2,*] :count disabled services',
        'supportDeposit' => 'Support deposits',
        'supportDepositDescription' => 'Enable deposits. Allow mateies to deposit money to their wallets in this booty.',
        'supportWithdraw' => 'Support withdrawals',
        'supportWithdrawDescription' => 'Enable withdrawals. Allow mateies to withdraw money from their wallets in this booty.',
        'backToServices' => 'Back to payment services',
        'viewService' => 'View service',
        'unknownService' => 'Unknown payment service',
        'startedWillUseOldDetails' => 'Payments that already be initiated might still use old details, even after changing them here.',
        'startedWillComplete' => 'Nay new payments be accepted using dis service. However, payments that have already been initiated still be completed.',
        'amountToTopUpInCurrency' => 'Amount to top-up wit\' in :currency',
        'selectPaymentServiceToUse' => 'Payment method',
    ],

    /**
     * Balance import system pages.
     */
    'balanceImport' => [
        'title' => 'Booty import systems',
        'system' => 'System',
        'systems' => 'Systems',
        'noSystems' => 'Nay systems...',
        'systems#' => '{0} Nay systems|{1} System|[2,*] :count systems',
        'manageSystem' => 'Manage system',
        'manageSystems' => 'Manage systems',
        'namePlaceholder' => 'Our paper system',
        'newSystem' => 'Add system',
        'editSystem' => 'Edit system',
        'deleteSystem' => 'Delete system',
        'created' => 'The booty import system be added.',
        'changed' => 'The booty import system be changed.',
        'deleteQuestion' => 'Yer \'bout to sink dis booty import system. Dis will sink all related imports. Ye be sure ye want to continue?',
        'deleted' => 'The booty import system be sunk.',
        'cannotDeleteHasEvents' => 'Nay delete dis system, because it has import events',
        'backToSystems' => 'Back to systems',
        'viewSystem' => 'View system',
        'unknownSystem' => 'Unknown system',
    ],

    /**
     * Balance import event pages.
     */
    'balanceImportEvent' => [
        'title' => 'Booty import events',
        'event' => 'Event',
        'events' => 'Events',
        'noEvents' => 'Nay events...',
        'events#' => '{0} Nay events|{1} Event|[2,*] :count events',
        'manageEvent' => 'Manage event',
        'manageEvents' => 'Manage events',
        'namePlaceholder' => '2019',
        'newEvent' => 'Add event',
        'editEvent' => 'Edit event',
        'deleteEvent' => 'Delete event',
        'created' => 'The booty import event be added.',
        'changed' => 'The booty import event be changed.',
        'deleteQuestion' => 'Yer \'bout to sink dis booty import event. Dis will sink all related imports. Ye be sure ye want to continue?',
        'deleted' => 'The booty import event be sunk.',
        'cannotDeleteHasChanges' => 'Nay delete dis event, because it has imported changes',
        'backToEvents' => 'Back to events',
        'viewEvent' => 'View event',
        'unknownEvent' => 'Unknown event',
    ],

    /**
     * Balance import change pages.
     */
    'balanceImportChange' => [
        'title' => 'Booty import changes',
        'change' => 'Change',
        'changes' => 'Changes',
        'noChanges' => 'Nay changes...',
        'approvedChanges' => 'Approved changes',
        'unapprovedChanges' => 'Unapproved changes',
        'noApprovedChanges' => 'No approved changes...',
        'noUnapprovedChanges' => 'No unapproved changes...',
        'changes#' => '{0} Nay changes|{1} Change|[2,*] :count changes',
        'manageChange' => 'Manage change',
        'manageChanges' => 'Manage changes',
        'newChange' => 'Add change',
        'importJsonChanges' => 'Import JSON changes',
        'editChange' => 'Edit change',
        'approveChange' => 'Approve change',
        'approveAll' => 'Approve all',
        'undoChange' => 'Undo change',
        'deleteChange' => 'Delete change',
        'created' => 'Th\' booty import change be imported.',
        'importedJson' => 'Th\' JSON booty import changes be imported.',
        'changed' => 'Th\' booty import change be changed.',
        'approveQuestion' => 'Yer \'bout to approve dis booty import change. Dis will commit th\' booty change to the wallet of the user when available. Ye be sure ye want to continue?',
        'approved' => 'Th\' booty import change be approved and be committed to th\' user wallet in th\' background.',
        'approveAllQuestion' => 'Yer \'bout to approve all booty import change in th\' \':event\' event. Dis will commit all booty change to the wallet of the user when available. Ye be sure ye want to continue?',
        'approvedAll' => 'Th\' booty import changes be approved and be committed to th\' user wallet in th\' background.',
        'undoQuestion' => 'Yer \'bout to undo dis booty import change. This will set its state to non-approved, and will revert any committed balance changes in th\' user\'s wallet. Ye be sure ye want to continue?',
        'undone' => 'Th\' booty import change be undone.',
        'deleteQuestion' => 'Yer \'bout to sink dis booty import change. Any mutation in a pirate\'s wallet as a result of dis change dat be committed already nay be reverted, and th\' wallet mutation is then unlinked. Dis will sink all related imports. Ye be sure ye want to continue?',
        'deleted' => 'Th\' booty import change be sunk.',
        'backToChanges' => 'Back to changes',
        'viewChange' => 'View change',
        'unknownChange' => 'Unknown change',
        'finalBalance' => 'Final balance',
        'jsonData' => 'JSON data',
        'cost' => 'Cost',
        'enterAliasNameEmail' => 'Enter th\' name \'nd e-bottle coordinate of th\' pirate yer importing booty for. The e-bottle coordinate be used to automatically link booty to th\' wallet of a registered pirate.',
        'selectCurrency' => 'Select th\' currency for dis import.',
        'balanceOrCostDescription' => 'Enter either th\' final balance or cost for th\' user.<br><br>For periodic balance imports, enter th\' final balance at time of the import event in th\' final balance field. On first import, th\' final balance be fully given to th\' user. On subsequent imports th\' difference between th\' last imported balance and th\' given final balance be given to th\' user.<br><br>For a one-time cost import, fill in th\' cost field to credit th\' user. Use a negative value to give the user balance. Dis has no effect on th\' tracked balance of periodic imports for dis user.',
        'enterBalanceOrCost' => 'Provide either th\' final balance or cost.',
        'importJsonDescription' => 'Import periodic balance updates from JSON data.<br><br>On first import, th\' final balance be fully given to th\' user. On subsequent imports th\' difference between th\' last imported balance and th\' given final balance be given to th\' user.',
        'importJsonFieldsDescription' => 'Configure th\' field names used in JSON data for each user.',
        'importJsonDataDescription' => 'Enter JSON data. Must be a JSON array with objects, each having fields as configured above.',
        'hasUnapprovedMustCommit' => 'Some changes nay be approved, and nay be applied to users until they are.',
        'mustApprovePreviousFirst' => 'Ye must approve th\' previous booty import change that be a balance update first.',
        'mustApproveAllPreviousFirst' => 'Ye must approve all previous booty import changes for the changes ye want to approve now that be a balance update first.',
        'cannotApproveWithFollowingApproved' => 'Ye nay approve a change, having a later change be approved already.',
        'cannotDeleteMustUndo' => 'Ye nay sink a change that be approved. Ye must undo it first.',
        'cannotUndoIfNewerApproved' => 'Ye nay undo dis booty import change, because there be a newer balance change for dis user that still be accepted.',
    ],

    /**
     * Balance import alias pages.
     */
    'balanceImportAlias' => [
        'newAliasMustProvideName' => 'Th\' given e-bottle coordinate nay be known, ye must provide a name.',
        'newJsonAliasMustProvideName' => 'Th\' given e-bottle coordinate \':email\' nay be known, missing name field for dis user.',
        'jsonHasDuplicateAlias' => 'Th\' JSON data contains multiple items for \':email\'.',
        'aliasAlreadyInEvent' => 'Th\' user \':email\' already has a change in dis event',
        'allowAddingSameUserMultiple' => 'Allow adding th\' same user more than once in current event (Not recommended)',
    ],

    /**
     * Economy finance pages.
     */
    'finance' => [
        'title' => 'Financial report',
        'walletSum' => 'Cumulative balance',
        'paymentsInProgress' => 'In progress',
        'fromBalanceImport' => 'from booty import',
        'membersWithNonZeroBalance' => 'Members with non-zero balance',
        'description' => 'Dis shows a simple financial report for th\' current booty state. Pirates from booty imports, that nay be registered and joined dis booty, are currently not listed.',
    ],

    /**
     * bunq account pages.
     */
    'bunqAccounts' => [
        'title' => 'bunq accounts',
        'bunqAccount' => 'bunq account',
        'description' => 'Click on one of yer bunq accounts to manage it, or add a new one.',
        'noAccounts' => 'Yer nay be any bunq accounts added yet...',
        'addAccount' => 'Add bunq account',
        'descriptionPlaceholder' => 'bunq account for automating bar payments',
        'tokenDescription' => 'Create a new API key in the developer section of th\' bunq app on yer handheld phoning device, and enter the freshly created token in dis field. Th\' token must never be shared with anyone else.',
        'ibanDescription' => 'Enter the IBAN of a monetary account in your bunq profile. This monetary account will be dedicated to payment processing and cannot be used for anything else. It is recommended to create a new monetary account through the bunq application for this.',
        'invalidApiToken' => 'Invalid API token',
        'addConfirm' => 'By adding this bunq account, you give :app full control over the monitary account assigned to the specified IBAN. That account will be dedicated to automated payment processing, until this link between :app and bunq is dropped. Never drop this link through the mobile bunq app by deleting the API key, but drop it through :app to ensure any ongoing payments can be finished properly. The account must have a current balance of €0.00. You cannot use this monetary account for any other payments, applications or :app instances, and you might risk serious money-flow issues if you do so. :app is not responsible for any damage caused by linking your bunq account to this application.',
        'mustEnterBunqIban' => 'You must enter a bunq IBAN',
        'accountAlreadyUsed' => 'This monetary is already used',
        'noAccountWithIban' => 'No active monetary account with this IBAN',
        'onlyEuroSupported' => 'Only accounts using EURO currency are supported',
        'notZeroBalance' => 'Account does not have a balance of €0.00, create a new monitary account',
        'added' => 'The bunq account be added.',
        'changed' => 'The bunq account be changed.',
        'enabled' => 'Enabled, allow usage for payments',
        'confirm' => 'I agree with this and meet th\' requirements',
        'environment' => 'bunq API environment',
        'runHousekeeping' => 'Run housekeeping',
        'runHousekeepingSuccess' => 'Th\' monetary bunq account be reconfigured and any pending payments now be queued for processing.',
        'noHttpsNoCallbacks' => 'Dis site does nay be HTTPS, real time bunq payments nay be supported. Payment events be processed daily.',
        'manageCommunityAccounts' => 'Manage community bunq accounts',
        'manageAppAccounts' => 'Manage application global bunq accounts',
    ],

    /**
     * Wallet pages.
     */
    'wallets' => [
        'title' => 'Wallets',
        'description' => 'Click on one of yer wallets to manage it, or create a fresh one.',
        'walletEconomies' => 'Wallet booties',
        'yourWallets' => 'Yer wallets',
        '#wallets' => '{0} No wallets|{1} 1 wallet|[2,*] :count wallets',
        'economySelectDescription' => 'Wallets in dis community be divided by booty.<br>Select \'the booty to manage yer wallets.',
        'noWallets' => 'Yer nay be any wallets yet...',
        'namePlaceholder' => 'My personal wallet',
        'nameDefault' => 'Fresh treasury',
        'createWallet' => 'Create wallet',
        'walletCreated' => 'Th\' wallet be created.',
        'walletUpdated' => 'Wallet changes saved.',
        'deleteQuestion' => 'Yer \'bout to sink dis wallet. Ye be sure ye want to continue?',
        'cannotDeleteNonZeroBalance' => 'To sink dis wallet, be have a balance of exactly :zero.',
        'walletDeleted' => 'Th\' wallet be sunk.',
        'cannotCreateNoCurrencies' => 'Ye nay create a wallet. Th\' crew admin did nay configure a currency which allows dis.',
        'all' => 'All wallets',
        'view' => 'View wallet',
        'noWalletsToMerge' => 'Ye nay have any wallets to merge.',
        'mergeWallets' => 'Merge wallets',
        'mergeDescription' => 'Select yer wallets to merge for each currency.',
        'mustSelectTwoToMerge' => 'Select at least two :currency wallets to merge.',
        'mergedWallets#' => '{0} Merged no wallets|{1} Merged one wallet|[2,*] Merged :count wallets',
        'transfer' => 'Transfer',
        'transferToSelf' => 'Transfer to wallet',
        'transferToUser' => 'Transfer to user',
        'toSelf' => 'To wallet',
        'toUser' => 'To user',
        'topUp' => 'Top-up wallet',
        'topUpNow' => 'Top-up yarr!',
        'successfullyTransferredAmount' => 'Successfully transfered :amount to :wallet',
        'backToWallet' => 'Back to wallet',
        'walletTransactions' => 'Wallet transactions',
    ],

    /**
     * Transaction pages.
     */
    'transactions' => [
        'title' => 'Transactions',
        'details' => 'Transaction details',
        'last#' => '{0} Last transactions|{1} Last transaction|[2,*] Last :count transactions',
        'backToTransaction' => 'Back to transaction',
        'toTransaction' => 'to transaction',
        'fromTransaction' => 'from transaction',
        'referencedTo#' => '{0} Referenced to no transactions|{1} Referenced to transaction|[2,*] Referenced to :count transactions',
        'referencedBy#' => '{0} Referenced by no transactions|{1} Referenced by transaction|[2,*] Referenced by :count transactions',
        'cannotUndo' => 'This transaction cannot be undone.',
        'undone' => 'The transaction has been undone.',
        'undoTransaction' => 'Undo transaction',
        'undoQuestion' => 'Yer \'bout to undo this transaction. Ye be sure ye want to continue?',
        'viewTransaction' => 'View transaction',
        'linkedTransaction' => 'Linked transaction',
        'state' => [
            'pending' => 'Pendin\'',
            'processing' => 'Processin\'',
            'success' => 'Hurray!',
            'failed' => 'Sunk',
        ],
        'descriptions' => [
            'fromWalletToProduct' => 'Payment for product(s) wit\' wallet',
            'toProduct' => 'Payment for product(s)',
            'fromPaymentToWallet' => 'Deposit to wallet from external account',
            'fromWalletToWallet' => 'Transfer between wallets',
            'toWallet' => 'Deposit to wallet',
            'fromWallet' => 'Withdrawal from wallet',
        ],
    ],

    /**
     * Mutation pages.
     */
    'mutations' => [
        'title' => 'Mutations',
        'details' => 'Mutation details',
        'from#' => '{0} From no mutations|{1} From 1 mutation|[2,*] From :count mutations',
        'to#' => '{0} To no mutations|{1} To 1 mutation|[2,*] To :count mutations',
        'dependsOn#' => '{0} Depends on no mutations|{1} Depends on mutation|[2,*] Depends on :count mutations',
        'dependentBy#' => '{0} Dependent by no mutations|{1} Dependent by mutation|[2,*] Dependent by :count mutations',
        'viewMutation' => 'View mutation',
        'state' => [
            'pending' => 'Pendin\'',
            'processing' => 'Processin\'',
            'success' => 'Hurray!',
            'failed' => 'Sunk',
        ],
        'types' => [
            'magic' => 'Special mutation',
            'walletTo' => 'Deposit to wallet',
            'walletFrom' => 'Paid wit\' wallet',
            'walletToDetail' => 'Deposit to :wallet',
            'walletFromDetail' => 'Paid wit\' :wallet',
            'productTo' => 'Paid for product(s)',
            'productFrom' => 'Received booty for product(s)',
            'productToDetail' => 'Paid for :products',
            'productFromDetail' => 'Received booty for :products',
            'paymentTo' => 'Withdrawal to external account',
            'paymentFrom' => 'Deposit from external account',
        ],
    ],

    /**
     * Notification pages.
     */
    'notifications' => [
        'title' => 'Notifications',
        'notification' => 'Notification',
        'description' => 'Dis shows all yer notifications, both new and read.',
        'unread#' => '{0} Nay unread notifications|{1} Unread notification|[2,*] :count unread notifications',
        'persistent#' => '{0} Nay persistent notifications|{1} Persistent notification|[2,*] :count persistent notifications',
        'read#' => '{0} Nay read notifications|{1} Read notification|[2,*] :count read notifications',
        'noNotifications' => 'Nay notifications...',
        'all' => 'All notifications',
        'markAsRead' => 'Mark as read',
        'markAllAsRead' => 'Mark all as read',
        'markedAsRead#' => '{0} Marked no notifications as read|{1} Marked a notification as read|[2,*] Marked :count notifications as read',
        'unknownNotificationAction' => 'Unknown notification action',
    ],

    /**
     * Payment pages.
     */
    'payments' => [
        'title' => 'Payments',
        'description' => 'This shows all in progress and settled payments ye made in any crew.',
        'details' => 'Payment details',
        'progress' => 'Payment progress',
        'last#' => '{0} Last payments|{1} Last payment|[2,*] Last :count payments',
        'backToPayment' => 'Back to payment',
        'backToPayments' => 'Back to payments',
        'requiringAction' => 'Payment awaits action',
        'requiringAction#' => '{0} None requiring action|{1} Payment requires action|[2,*] :count requiring action',
        'inProgress' => 'Payment in progress',
        'inProgress#' => '{0} None in progress|{1} Payment in progress|[2,*] :count in progress',
        'inProgressDescription' => 'This payment is still in progress.',
        'settled#' => '{0} None settled|{1} Payment settled|[2,*] :count settled',
        'noPayments' => 'Ye nay made any payments yet',
        'viewPayment' => 'View payment',
        'unknownPayment' => 'Unknown payment',
        'handlePayments' => 'Review payments',
        'handleCommunityPayments' => 'Review crew payments',
        'paymentsToApprove' => 'Payments awaiting action',
        'paymentsWaitingForAction' => 'Some payments are waiting for action by a crew manager, please review these as soon as possible.',
        'paymentsToApproveDescription' => 'Th\' following user payments be waiting for action by a crew manager. Please go through these as soon as possible to minimize payment times.',
        'paymentRequiresCommunityAction' => 'Dis payment awaits action by a crew manager.',
        'cancel' => 'Cancel payment',
        'cancelPaymentQuestion' => 'Yer about to cancel dis payment. Never cancel a payment for which yer already transfered money, or ye transfer might be lost. Ye be sure ye want to continue?',
        'paymentCancelled' => 'Payment cancelled',
        'state' => [
            'init' => 'Initiated',
            'pendingUser' => 'Pendin\' user action',
            'pendingCommunity' => 'Pendin\' review',
            'pendingAuto' => 'Pendin\' (automatic)',
            'processing' => 'Processin\'',
            'completed' => 'Hurray!',
            'revoked' => 'Revoked',
            'rejected' => 'Rejected',
            'failed' => 'Sunk',
        ],
    ],

    /**
     * Bar pages.
     */
    'bar' => [
        'yourBars' => 'Ye bars',
        'noBars' => 'Nay bars asea...',
        'searchByCommunity' => 'Search by crew',
        'searchByCommunityDescription' => 'It\' usually be easier to find ye bar by it\'s crew.',

        // TODO: remove duplicates
        'createBar' => 'Create bar',
        'editBar' => 'Edit bar',
        'deleteBar' => 'Delete bar',
        'join' => 'Join',
        'yesJoin' => 'Yey, sail ho!',
        'joined' => 'Joined',
        'notJoined' => 'Strangers!',

        'hintJoin' => 'Ye be not part of th\' bar.',
        'joinedClickToLeave' => 'Click to be sunk.',
        'joinQuestion' => 'Ye like be joined th\' bar?',
        'alsoJoinCommunity' => 'Also be join their crew',
        'alreadyJoinedTheirCommunity' => 'Ye already be a pirate of their crew',
        'joinedThisBar' => 'Ye joined th\' bar.',
        'cannotSelfEnroll' => 'Ye cannot join dis bar yerself, it be disabled.',
        'leaveQuestion' => 'Ye be sure to sink th\' bar?',
        'leftThisBar' => 'Ye sunk th\' bar.',
        'cannotLeaveHasWallets' => 'Ye nay leave dis bar while ye have a wallet in it.',
        'protectedByCode' => 'Dis bar be protected by a secret. Request it at yer bar, or use yer spyglass to scan the bar Q-ARRRR code if available.',
        'protectedByCodeFilled' => 'Dis bar be protected by a secret. We filled it for ye.',
        'incorrectCode' => 'Bar code be incorrect.',
        'namePlaceholder' => 'Queen Anne\'s ship',
        'descriptionPlaceholder' => 'Say ho to Queen Anne\'s ship!',
        'slugDescription' => 'Ye slug allows ye to create \'n easy to remember URL to access dis bar, by defining a short keyword.',
        'slugDescriptionExample' => 'Dis could simplify ye bar URL:',
        'slugPlaceholder' => 'anne',
        'slugFieldRegexError' => 'Dis slug must start with n alphabetical character.',
        'codeDescription' => 'With a bar secret, ye prevent random pirates from joining. To join the bar, users be required to enter th\' secret.',
        'economyDescription' => 'Th\' booty defines what products, currencies and wallets be used in dis bar. Be very careful wit\' changing it after th\' bar be created, as this immediately affects th\' list of products, currencies and wallets used in dis bar. Mateies probably don\'t expect dis, and might find it hard to understand.',
        'showExploreDescription' => 'List on public \'Explore bars\' page',
        'showCommunityDescription' => 'List on crew page for crew maties',
        'selfEnrollDescription' => 'Allow self enrollment (wit\' code if specified)',
        'joinAfterCreate' => 'Join th\' bar after creating.',
        'created' => 'Th\' bar be created.',
        'updated' => 'Th\' bar be updated.',
        'mustCreateEconomyFirst' => 'To create a bar, ye must create booty first.',
        'backToBar' => 'Back to bar',
        'quickBuy' => 'Quick buy',
        'boughtProductForPrice' => 'Bought :product for :price',
        'noDescription' => 'Dis bar be nay description',
        'barInfo' => 'Bar info',
        'viewBar' => 'View bar',
        'deleted' => 'Th\' bar be sunk.',
        'deleteQuestion' => 'Yer \'bout to permanently sink dis bar. All mateies including yerself will lose access to it, and it nay be possible to link product transactions to it anymore. Th\' products and matey wallets will remain as part of th\' booty that be used in dis bar. Ye be sure ye want to continue?',
        'exactBarNameVerify' => 'Exact name of bar to sink (Verification)',
        'incorrectNameShouldBe' => 'Incorrect name, should be: \':name\'',
        'generatePoster' => 'Create bar poster',
        'generatePosterDescription' => 'Create a poster for dis bar to hang on a wall. Fellow mateies will then be able to easily use :app and join dis bar by scanning a QR code with their handheld phoning device.',
        'showCodeOnPoster' => 'Show code to join dis bar on th\' poster',
        'poster' => [
            'thisBarUses' => 'Dis bar uses',
            'toDigitallyManage' => 'to digitally manage booty and stock for consumptions',
            'scanQr' => 'scan the QR code below to join and make a purchase',
            'orVisit' => 'Or see',
        ],
        'advancedBuy' => [
            'title' => 'Advanced buy',
            'tapProducts' => 'Tap products to buy for any pirate.',
            'tapUsers' => 'Tap pirates to add the selected products in cart for.',
            'tapBuy' => 'Tap th\' blue buy button to commit the purchase.',
            'addToCartFor' => 'Add selected to cart for',
            'searchUsers' => 'Search pirates',
            'noUsersFoundFor' => 'No pirates found for :term',
            'inCart' => 'In cart',
            'buyProducts#' => '{0} Buy no products|{1} Buy product|[2,*] Buy :count products',
            'buyProductsUsers#' => '{0} Buy no products for :users pirates|{1} Buy product for :users pirates|[2,*] Buy :count products for :users pirates',
            'pressToConfirm' => 'Tap again to confirm',
            'boughtProducts#' => '{0} Bought no products.|{1} Bought 1 product.|[2,*] Bought :count products.',
            'boughtProductsUsers#' => '{0} Bought no products for :users pirates.|{1} Bought 1 product for :users pirates.|[2,*] Bought :count products for :users pirates.',
            'pageCloseWarning' => 'Ye selected products or has products in cart that have not been bought yet. Ye must add a product selection to at least one pirate and tap th\' Buy button to commit th\' purchase, or the selection will be lost.',
        ],
        'checklist' => 'Bar checklist',
    ],

    /**
     * Community/bar statistics pages.
     */
    'stats' => [
        'title' => 'Statistics',
        'barStats' => 'Bar statistics',
        'communityStats' => 'Crew statistics',
        'activePastHour' => 'Active past hour',
        'activePastDay' => 'Active past day',
        'activePastMonth' => 'Active past month',
        'productsPastHour' => 'Products past hour',
        'productsPastDay' => 'Products past day',
        'productsPastMonth' => 'Products past month',
    ],

    /**
     * Bar member pages.
     */
    'barMembers' => [
        'title' => 'Bar mateies',
        'description' => 'Dis page shows th\' overview o\' all bar mateies.<br>Clicking a matey allows ye to remove the matey, or change be rank.',
        'noMembers' => 'Dis bar be nay mateies...',
        'memberSince' => 'Matey since',
        'lastVisit' => 'Last visit',
        'deleteQuestion' => 'Yer \'bout to remove dis matey from our bar. Ye be sure ye want to continue?',
        'memberRemoved' => 'Th\' matey be sunk.',
        'memberUpdated' => 'Matey changes saved.',
        'incorrectMemberRoleWarning' => 'Assigning n\' incorrect role that be too permissive to a matey may introduce significant security issues.',
        'ownRoleDowngradeWarning' => 'By downgrading yer own role ye might lose management access to dis bar. Be very careful.',
        'confirmRoleChange' => 'Confirm role change for bar matey',
        'confirmSelfDelete' => 'Confirm to kick yerself as bar matey, ye be lose yer role',
        'cannotDemoteLastManager' => 'Ye nay demote th\' last bar matey wit\' dis (or a moar permissive) management role.',
        'cannotEditMorePermissive' => 'You nay edit a bar matey with a moar permissive role than yerself.',
        'cannotSetMorePermissive' => 'You nay set a moar permissive role for a bar matey than yer current role.',
        'cannotDeleteLastManager' => 'Ye nay sink th\' last bar matey wit\' dis (or a moar permissive) management role.',
    ],

    /**
     * Verify email address page.
     */
    'verifyEmail' => [
        'title' => 'Verify e-bottle coordinate',
        'description' => 'Enter th\' verification token of the e-bottle coordinate ye like to verify.<br>'
            . 'Dis token be found at th\' bottom of ye verification e-bottle message ye\'ve received in yer e-bottle-box.',
        'invalid' => 'Unknown token. Maybe th\' e-bottle coordinate be already verified, or th\' token expired.',
        'expired' => 'Th\' token be sunk. Request a shiny e-bottle verification message.',
        'alreadyVerified' => 'Th\' e-bottle coordinate be already verified.',
        'verified' => 'Sail ho! Yer e-bottle coordinate be verified.',
    ],

    /**
     * Password request sent page.
     */
    'passwordRequestSent' => [
        'title' => 'Check for bottle messages',
        'message' => 'If yer e-bottle coordinate ye entered be known by our captain, our jolly crew sent ye instructions for a shiny new passcode to yer e-bottle-box.<br><br>'
            . 'Please note that them instructions would only be valid for <b>:hours turns o\'the hourglass</b>.<br><br>'
            . 'Ye may burn dis parchment now.',
    ],

    /**
     * Password disable page.
     */
    'passwordDisable' => [
        'title' => 'Disable passcode',
        'description' => 'Enter yer current passcode in th\' field bellow, to disable using a password for logging in in th\' future. Yer still be able to login using a link sent to yer e-bottle inbox.',
    ],

    /**
     * Password reset page.
     */
    'passwordReset' => [
        'enterResetToken' => 'Enter th\' passcode reset token. '
            . 'Th\' token be discovered in th\' e-bottle message yer received with th\' passcode reset map.',
        'enterNewPassword' => 'Enter th\' shiny passcode ye\'d like to take aboard.',
        'invalid' => 'Unknown token. Th\' token might be sunken.',
        'expired' => 'Th\' token be sunken. Request a shiny password reset token.',
        'used' => 'Yer passcode be already changed using dis token.',
        'changed' => 'Y\'all fresh! Yer password be changed.',
    ],

    /**
     * Privacy policy page.
     */
    'privacy' => [
        'title' => 'Piracy',
        'description' => 'When ye use our seas, yer trusting us with yer information. We understand this be a big responsibility. We be pirates but we must follow landlubber laws.<br><br>Th\' Piracy Policy below is meant to help ye understand how we manage yer information.',
        'onlyEnglishNote' => 'Note th\' Piracy Policy only be available in landlubber English, though it applies to our seas in any speak.',
        'questions' => 'Questions?',
        'questionsDescription' => 'If ye have any further questions about our Piracy Policy or yer piracy when using our seas, be sure to ship us a bottle message.',
    ],

    /**
     * Terms of Service page.
     */
    'terms' => [
        'title' => 'Terms',
        'description' => 'When ye use our seas, ye be agree with our Terms o\' Service as shown below.',
        'onlyEnglishNote' => 'Note th\' Terms \'o Service only be available in landlubber English, although it applies to our seas in any speak.',
        'questions' => 'Questions?',
        'questionsDescription' => 'If ye have any further questions about our Terms o\' Service, be sure to ship us a bottle message.',
    ],

    /**
     * License page.
     */
    'license' => [
        'title' => 'License',
        'description' => 'Th\' Arrbapappa software project be open-source, and be released under th\' GNU GPL-3.0 license. Dis license maps what ye are and nay be allowed to do with th\' public source code of dis software project. Dis license does not have any effect on the usage information processed within dis application.<br><br>Read th\' full license below, o\' check out th\' summary for dis license as quick summary.',
        'onlyEnglishNote' => 'Note th\' license only be available in landlubber English, although it applies to our seas in any speak.',
        'questions' => 'Questions?',
        'questionsDescription' => 'If ye have any further questions about our license, be sure to ship us a bottle message. Ye can also check th\' plain text license readable on any ship.',
        'plainTextLicense' => 'Plain text license',
        'licenseSummary' => 'License summary',
    ],

    /**
     * Contact page.
     */
    'contact' => [
        'title' => 'Contact',
        'contactUs' => 'Contact us',
        'description' => 'Ye can use any of th\' following channels to contact th\' administrator that is running dis ship. Please ask th\' administrators of yer crew for support first, and attempt to solve any problems with them.<br><br>Please describe yer question and/or problem carefully. Any GDRP inquiries can be requested through these channels as well.',
        'issuesDescription' => 'Dis ship is open-source, and its schematics are
        therefore openly available.<br>For bugs in dis ship, please check the \'Issues\' list to see if it has been registered already.<br>If it isn\'t yet, ye may create a new issue on the issues overview page. Alternatively, ye can send an e-bottle message describing the issue to the coordinate below, which will immediately add th\' issue to th\' list as well without having to create a GitLab account.',
    ],

    /**
     * No permission page.
     */
    'noPermission' => [
        'title' => 'Ye nay be here...',
        'description' => 'Ye compass be upside down, ye sailed th\' wrong seas.<br />Ye nay be access to this sea.',
        'notLoggedIn' => 'Nay entered',
        'notLoggedInDescription' => 'Ye nay be entered. Ye may enter yer ship to get proper access rights.',
    ],

    /**
     * About page.
     */
    'about' => [
        'title' => 'About',
        'aboutUs' => 'About us',
        'description' => ':app be a digital bar management application to facilitate a pirate-controlled sea for purchase processing, payment handling and treasure management.<br><br>:app be a fully automated solution for small self-managed bars and crews, to take away hassle of manually registering purcahses using telly marks on yer paper.<br><br>For any interest in using dis platform for yer own crew, sure to send us a message!',
        'developedBy' => 'Dis project be developed & maintained by',
        'sourceDescription' => 'I make th\' software that I develop open-source. Th\' complete project with its source code be available free of charge, for all pirates. I believe it be important to allow any pirate to inspect, modify, improve, contribute, and verify without restrictions.',
        'sourceAt' => 'Latest source code be available on GitLab',
        'withLicense' => 'Released with th\' following license',
        'usedTechnologies' => 'Some awesome technologies be used include',
        'noteLaravel' => 'backend framework',
        'noteSemanticUi' => 'frontend theming framework',
        'noteGlyphicons' => 'icons & symbols',
        'noteFlags' => 'pirateflags',
        'noteJQuery' => 'simplifies JavaScript',
        'noteGetTerms' => 'terms & piracy policy template',
        'noteEDegen' => 'suggested \'Arrbapappa\'',
        'otherResources' => 'Other awesome resources include',
        'donate' => 'A lot of effort went into dis project.<br>Want to donate me a beer?',
        'thanks' => 'Thank ye for using dis product.<br>Thank ye for being awesome.',
        'copyright' => 'Copyright © :app :year.<br>All rights reserved.',
    ],
];
