<?php

/**
 * Pages and their names.
 */
return [
    'pages' => 'Parchments',
    'emailPreferences' => 'E-bottle preferences',
    // TODO: move to misc?
    'communities' => 'Crews',
    // TODO: move to misc?
    'bars' => 'Bars',
    'account' => 'Ye ship',
    'yourAccount' => 'Ye ship',
    'requestPasswordReset' => 'Request passcode reset',
    'changePassword' => 'Change passcode',
    'changePasswordDescription' => 'First enter yer ol\' n\' passcode. Den enter ye shiny n\' fresh passcode to take abroad.',

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
            'emails' => 'E-bottles',
            'yourEmails' => 'Ye e-bottles',
            'unverifiedEmails' => 'Unverified e-bottles',
            'verifyEmails' => 'Verify e-bottles',
            'unverifiedDescription' => 'This page lists yer e-bottle coordinates that still be unverified. Click th\' blue button below to start th\' verification process.',
            'resendVerify' => 'Sally forth verification',
            'unverified#' => '{0} No unverified emails|{1} Unverified email|[2,*] :count unverified emails',
            'verify#' => '{0} Verify no e-bottle coordinates|{1} Verify e-bottle coordinate|[2,*] Verify :count e-bottle coordinates',
            'verifiedDescription' => 'We sent a fresh verification message to yer e-bottle coordinates listed below. Tap th\' link in th\' message to complete verification. When ye completed th\' verification you may click th\' button om th\' bottom of th\' page to go forth.',
            'iVerifiedAll' => 'I verified all',
            'someStillUnverified' => 'Some of yer e-bottle coordinates still be unverified. Please see th\' list below. Check yer e-bottle inbox for a verification message.',
            'verifySent' => 'A fresh verification e-bottle be sally forth.',
            'alreadyVerified' => 'Th\' e-bottle coordinate be verified.',
            'allVerified' => 'All yer e-bottle coordinates be verified.',
            'cannotDeleteMustHaveOne' => 'Ye no delete \'his e-bottle coordinate, ye must be one coordinate.',
            'cannotDeleteMustHaveVerified' => 'Ye no delete \'his e-bottle coordinate, ye must be one verified coordinate.',
            'deleted' => 'Th\' e-bottle coordinate be deleted.',
            'deleteQuestion' => 'Ye be sure ye want to sunk dis e-bottle address?',
            'notifyOnLowBalance' => 'Receive notification when yer booty drops below zero',
            'mailReceipt' => 'Receive receipt of purchases after each bar visit',
            'backToEmails' => 'Back to e-bottles',
        ],
        'addEmail' => [
            'title' => 'Add e-bottle coordinate',
            'description' => 'Enter yer e-bottle coordinate to conquer.',
            'added' => 'E-bottle coordinate added. \'ll verification be sent.',
            'cannotAddMore' => 'Ye nay add more e-bottle coordinates to yer account. Sink an existing coordinate in order to add a fresh one.',
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
        'yesJoin' => 'Yay, sail ho!',
        'joined' => 'Joined',
        'youAreJoined' => 'You joined dis crew.',
        'leave' => 'Leave',
        'notJoined' => 'Strangers!',
        'hintJoin' => 'Ye be not part of th\' crew.',
        'joinQuestion' => 'Ye like be joined th\' crew?',
        'joinedThisCommunity' => 'Ye joined th\' crew.',
        'leaveQuestion' => 'Ye be sure to sink th\' crew?',
        'cannotSelfEnroll' => 'Ye cannot join dis crew yerself, it be disabled.',
        'leftThisCommunity' => 'Ye sunk th\' crew.',
        'cannotLeaveStillBarMember' => 'Ye nay leave dis crew, because yer still a member of a bar in dis crew',
        'protectedByCode' => 'Dis crew be protected by a secret. Request it at yer crew, or use yer binoculars to scan the crew Q-ARRRR code if available.',
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
        'deleteQuestion' => 'Yer \'bout to permanently sink dis crew. All mateies including yerself will lose access to it. All bars, booties, matey wallets, loot and related entities that be used within dis crew will be deleted as well. Ye be sure ye want to continue?',
        'deleteBlocked' => 'Yer \'bout to permanently sink this crew. ye must sink th\' entities listed below first before ye be continue with deleting dis crew.',
        'exactCommunityNameVerify' => 'Exact name of crew to sink (Verification)',
        'incorrectNameShouldBe' => 'Incorrect name, should be: \':name\'',
        'cannotDeleteDependents' => 'Dis community cannot be sunk, as entities be depending on it which nay just be deleted.',
        'generatePoster' => 'Create crew placard',
        'generatePosterDescription' => 'Create a placard for dis crew to hang on a wall. Fellow mateies will then be able to easily use :app and join dis crew by scanning a Q-ARRRR code with their handheld phoning device.',
        'showCodeOnPoster' => 'Show code to join dis crew on th\' placard',
        'posterBarPreferred' => 'It be usually preferred to generate a placard for a bar instead of a crew, as mateies joining a crew doesn\'t give them acces to purchasing loot without joining a bar as well. Visit the management hub of a specific bar to create a placard for it.',
        'poster' => [
            'thisCommunityUses' => 'Dis crew uses',
            'toDigitallyManage' => 'to digitally manage booty and stock for consumptions',
            'scanQr' => 'scan the Q-ARRRR code below to join and make a purchase',
            'orVisit' => 'Or see',
        ],
        'links' => [
            'title' => 'Useful links',
            'description' => 'Dis page lists various shareable links for dis crew. Ye may share these through e-bottle messages or print dem on a placard. Some of these links allow you to direct other pirates to specific otherwise hidden pages and intents.<br><br>Please be aware that some links change when modifying crew settings, and some links contain secret bits.',
            'linkCommunity' => 'Main crew page',
            'linkCommunityAction' => 'Visit :community',
            'linkJoinCommunity' => 'Invite fresh pirates to join crew',
            'linkJoinCommunityAction' => 'Join :community',
            'linkJoinCommunityCode' => 'Invite fresh pirates to join crew (with code)',
            'linkJoinCommunityCodeAction' => 'Join :community',
        ],
        'checklist' => 'Crew checklist',
    ],

    /**
     * Community membership page.
     */
    'communityMember' => [
        'title' => 'Membership',
    ],

    /**
     * Community member pages.
     */
    'communityMembers' => [
        'title' => 'Crew mateies',
        'description' => 'Dis page shows th\' overview o\' all crew mateies. Tapping a matey allows ye to remove the matey, or change be rank.',
        'search' => 'Search mateies',
        'noMembers' => 'Nay mateies...',
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
        'description' => 'Dis page shows th\' overview o\' all booties of dis crew.<br>Tapping a booty to manage it, or create shiny booty for a fresh bar.',
        'manage' => 'Manage booties',
        'noEconomies' => 'Dis crew be nay booties...',
        'createEconomy' => 'Create booty',
        'editEconomy' => 'Edit booty',
        'deleteEconomy' => 'Delete booty',
        'economyCreated' => 'Th\' booty be created. Please add \'n configure a currency now.',
        'deleteQuestion' => 'Yer \'bout to sink dis booty from our crew. This will permanently delete things inside it, such as balance imports, along with it. Ye be sure ye want to continue?',
        'deleteBlocked' => 'Yer \'bout to permanently sink this booty. ye must delete th\' entities listed below first before ye be continue with deleting dis booty.',
        'cannotDeleteDependents' => 'Dis booty cannot be sunk, as entities be depending on it which nay just be deleted.',
        'economyDeleted' => 'Th\' booty be sunk.',
        'economyUpdated' => 'Booty changes saved.',
        'namePlaceholder' => 'Our booty',
        'backToEconomy' => 'Back to booty',
        'backToEconomies' => 'Back to booties',
        'inEconomy' => 'in booty',
        'noWalletsInEconomy' => 'There be no booty in dis economy.',
        'walletOperations' => 'Booty operations',
        'zeroAllWallets' => 'Zero all booty',
        'zeroAllWalletsQuestion' => 'Ye about to set the balance of all booties in dis economy back to zero. Make sure to export all desired wallet data before doing dis. Ye be sure to continue?',
        'zeroAllWalletsDescription' => 'Balance reset by administrator',
        'zeroAllWalletsConfirmText' => 'zero all user wallets',
        'walletsZeroed' => 'All booty be zeroed.',
        'deleteAllWallets' => 'Delete all booty',
        'deleteAllWalletsQuestion' => 'Ye about to permanently destroy all booties in dis economy. Make sure to export all desired wallet data before doing dis. Ye be sure to continue?',
        'deleteAllWalletsConfirmText' => 'delete all user wallets',
        'cannotDeleteWalletsNonZero' => 'Nay delete all booty because some have a non-zero balance. Ye must zero all wallet balances first.',
        'confirmDeleteAllWallets' => 'Confirm to permanently delete all member wallets',
        'walletsDeleted' => 'All wallets have been deleted.',
    ],

    /**
     * Community economy payment pages.
     */
    'economyPayments' => [
        'title' => 'Payments',
        'description' => 'Dis page shows all payments initiated by crew members in dis booty.',
        'exportTitle' => 'Export payments',
        'exportDescription' => 'Dis page allows ye to export all payments initiated by crew members in dis booty to a file to view or import in an external program.',
    ],

    /**
     * Community economy currency pages.
     */
    'currencies' => [
        'title' => 'Currencies',
        'description' => 'Dis page shows \'n overview of currencies in th\' booty.<br>At least one currency must be enabled to use dis booty for a bar.<br>Add shiny currency, or tap one to manage.',
        'change' => 'Change currency',
        'noCurrencies' => 'Dis booty be nay currencies...',
        'createCurrency' => 'Add currency',
        'currencyCreated' => 'Th\' currency be added.',
        'deleteQuestion' => 'Yer \'bout to sink dis currency from dis booty. Ye be sure ye want to continue?',
        'deleteVoidNotice' => 'When ye remove dis booty, all configured loot prices for dis currency will be voided in bars dat use dis booty.<br>Ye may want to disable dis currency instead by changing it.',
        'currencyDeleted' => 'Th\' currency be sunk.',
        'currencyUpdated' => 'Currency changes saved.',
        'enabledDescription' => 'Specify whether dis currency be enabled in bars dat arrr using dis booty. If disabled, bar mateies won\'t be able to purchase loot with dis currency, \'n must use a different currency if available until it is enabled again.',
        'changeCurrencyTitle' => 'Change currency?',
        'changeCurrencyDescription' => 'Ye must only make very small changes to th\' currency properties, to prevent introducing issues where dis currency be used. Consider to add a fresh currency instead.',
        'allowWallets' => 'Allow wallet creation',
        'allowWalletsDescription' => 'With dis option ye specify whether bar mateies be create a fresh personal wallet for dis currency. Existing wallets always be kept afloat.',
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
        'title' => 'Loot',
        'all' => 'All loot',
        'select' => 'Select loot',
        'search' => 'Scavenge loot',
        'clickBuyOrSearch' => 'Tap loot to get or search',
        '#products' => '{0} Nay loot|{1} 1 loot|[2,*] :count loot',
        'noProducts' => 'Nay loot...',
        'searchingFor' => 'Scavenging for :term...',
        'noProductsFoundFor' => 'Nay loot for :term',
        'manageProduct' => 'Manage loot',
        'manageProducts' => 'Manage loot',
        'addProducts' => 'Add loot',
        'newProduct' => 'Fresh loot',
        'cloneProduct' => 'Clone loot',
        'editProduct' => 'Edit loot',
        'created' => 'The loot be added.',
        'changed' => 'The loot be changed.',
        'restoreQuestion' => 'Yer \'bout to afloat dis loot to make it available again. Ye be sure ye want to continue?',
        'restored' => 'The loot be afloated.',
        'deleteQuestion' => 'Yer \'bout to sink dis loot. Ye be sure ye want to continue?',
        'permanentDescription' => 'Tick th\' checkbox below to permanently delete dis loot. Ye nay be able to restore it.',
        'permanentlyDelete' => 'Permanently delete loot',
        'deleted' => 'The loot be trashed.',
        'deletedProduct' => 'Deleted loot',
        'permanentlyDeleted' => 'The loot be permanently deleted.',
        'namePlaceholder' => 'Bear Beer',
        'tagsPlaceholder' => 'cola soda',
        'enabledDescription' => 'Enabled, can be gotten',
        'prices' => 'Prices',
        'pricesDescription' => 'Configure prices for dis loot in th\' fields below for booties ye want to support.',
        'localizedNames' => 'Localized names',
        'localizedNamesDescription' => 'Configure localized names for dis loot in th\' fields below if be different from th\' main name.',
        'search' => 'Scavenge loot',
        'backToProducts' => 'Back to loot',
        'unknownProduct' => 'Unknown loot',
        'recentlyBoughtProducts#' => '{0} Recent loot|{1} Recently looted|[2,*] :count recently looted',
        'inventoryProductsDescription' => 'Normally, when dis product be bought, it be subtracted from th\' bar inventory. Here ye can specify an alternate list of products to subtract from th\' inventory when bought instead.<br><br>For example: for th\' product BaCo you may want to subtract Bacardi and Cola from the inventory rather than th\' BaCo product itself. Ye may set dis up by adding Bacardi and Cola to th\' list below.<br><br>If th\' list below contains any product, th\' current product won\'t be subtracted from th\' inventory. Explicitly add th\' current product to th\' list as well to subtract it from the inventory when bought. Remove all products from dis list to disable dis feature and revert back to normal.',
        'editInventoryProducts' => 'Edit inventory products',
        'addProduct' => 'Add product',
        'quantitiesUpdated' => 'Quantites updated.',
        'productRemoved' => 'Product removed',
        'type' => [
            'normal' => 'Normal',
            'custom' => 'Custom',
        ],
    ],

    /**
     * Inventory pages.
     */
    'inventories' => [
        'title' => 'Inventories',
        'inventory' => 'Inventory',
        'barInventory' => 'Bar inventory',
        'allInventories' => 'All inventories',
        'manage' => 'Manage inventories',
        'noInventories' => 'Nay inventories...',
        'createInventory' => 'Create inventory',
        'created' => 'Th\' inventory be created.',
        'namePlaceholder' => 'My inventory',
        'editInventory' => 'Edit inventory',
        'changed' => 'Th\' inventory be updated.',
        'deleteQuestion' => 'Yer about to sink dis inventory along with all its history. Ye be sure ye want to continue?',
        'moveBeforeDelete' => 'Before ye sink dis inventory, consider to move all yer products to another inventory.',
        'deleted' => 'Th\' inventory be sunk.',
        'exhaustedProducts' => 'Exhausted products',
        'addRemove' => 'Add/remove',
        'addRemoveProducts' => 'Add/remove products',
        'addRemoveDescription' => 'Add and/or remove product quantities, after shopping products for example. Leave fields empty to skip balancing a product.',
        'defaultAddRemoveComment' => 'Add/remove products',
        'rebalance' => 'Rebalance',
        'rebalanceProducts' => 'Rebalance products',
        'rebalanceDescription' => 'Count all products in yer inventory and enter th\' quantities in th\' list below. Enter either th\' quantity or change (delta). Enter th\' same quantity for products as currently known to confirm th\' inventory be in balance. Leave fields empty to skip balancing a product.',
        'defaultRebalanceComment' => 'Periodic rebalance',
        'move' => 'Move',
        'moveProducts' => 'Move products',
        'moveDescription' => 'Move products from this inventory to another. Leave fields empty to skip moving a product.',
        'defaultMoveComment' => 'Move products between inventories',
        'fromInventory' => 'From inventory',
        'toInventory' => 'To inventory',
        '#productsRebalanced' => '{0} Nay products rebalanced|{1} One product rebalanced|[2,*] :count products rebalanced',
        '#productsAddedRemoved' => '{0} Nay products added/removed|{1} One product added/removed|[2,*] :count products added/removed',
        '#productsMoved' => '{0} Nay products moved|{1} One product moved|[2,*] :count products moved',
        'confirmChangeQuantities' => 'I confirm I want to update product quantities for these products with the entered amounts',
        'mustBeInteger' => 'Must be an integer.',
        'changeType' => 'Change type',
        'type' => [
            1 => 'Balance',
            2 => 'Move',
            3 => 'Purchase',
            4 => 'Add/remove',
            5 => 'Set',
        ],
        'lastBalanced' => 'Last rebalanced',
        'inventoryQuantities' => 'Inventory quantities',
        'last#Changes' => '{0} Last changes|{1} Last change|[2,*] Last :count changes',
        'noChanges' => 'No changes',
        'allChanges' => 'All changes',
        'viewRelated' => 'View related',
        'hideType' => 'Hide type',
        'periodReport' => 'Period report',
        'periodOfFromTo' => 'Period of :period, from :from to :to',
        'period' => [
            'week' => 'Week',
            'month' => 'Month',
            'year' => 'Year',
        ],
        'warningNoBalanceChangesThisPeriod' => 'Products nay be rebalanced during th\' selected period. Please consider to rebalance products now to get an accurate report.',
        'volumeShort' => 'vol',
        'purchaseVolumeByProduct' => 'Purchase volume by product',
        'monthlyPurchases' => 'Monthly purchases',
        'drainEstimate' => 'Drain estimate',
        'drainEstimateOthers' => 'Drain all inventories',
        'stats' => [
            'period' => 'Period',
            'changeCount' => '# o\' changes',
            'manualChangeCount' => '# o\' manual changes',
            'balanceCount' => '# o\' rebalance changes',
            'purchaseCount' => '# o\' purchase changes',
            'quantityVolume' => 'Total quantity volume',
            'quantitySum' => 'Total quantity sum',
            'unbalanceVolume' => 'Unbalance volume',
            'unbalanceSum' => 'Unbalance sum',
            'unbalanceMoney' => 'Unbalance money',
            'purchaseVolume' => 'Purchase volume',
            'addSum' => 'Loot gained',
            'removeSum' => 'Loot sunk',
            'moveInSum' => 'Loot sailed-in',
            'moveOutSum' => 'Loot sailed-out',
        ],
        'unbalancedProducts' => 'Unbalanced products',
        'timeTravel' => 'Time travel',
        'travelToTime' => 'Travel to time (UTC)',
        'undoChangeQuestion' => 'Yer \'bout to undo dis change. Dis will revert th\' quantity change in th\' inventory. Ye be sure ye want to continue?',
        'alsoUndoRelated' => 'Also undo related change',
        'cannotUndoChange' => 'Dis change nay be undone.',
        'undoneChange' => 'Th\' change be undone.',
        'undoChange' => 'Undo change',
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
        'supportWithdrawDescription' => 'Enable withdrawals. Allow mateies to withdraw money from their wallets in this booty. (Currently nay supported)',
        'backToServices' => 'Back to payment services',
        'viewService' => 'View service',
        'unknownService' => 'Unknown payment service',
        'startedWillUseOldDetails' => 'Payments that already be initiated might still use old details, even after changing them here.',
        'startedWillComplete' => 'Nay fresh payments be accepted using dis service. However, payments that have already been initiated still be completed.',
        'amountInCurrency' => 'Amount in :currency',
        'amountToTopUpInCurrency' => 'Amount to top up wit\' in :currency',
        'youSpendAboutEachMonth' => 'Ye spend :amount each month.',
        'noteTimeAdvance' => '+:time advance',
        'redemption' => 'Fine',
        'topUpWithLargerAmount' => 'Top-up with larger amount',
        'pay' => 'Pay',
        'otherPay' => 'Other amount, pay',
        'selectPaymentServiceToUse' => 'Payment method',
    ],

    /**
     * Balance import system pages.
     */
    'balanceImport' => [
        'title' => 'Booty imports',
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
        'deleteQuestion' => 'Yer \'bout to sink dis booty import system. Dis will sink all related imports. Import changes that be committed to wallets already nay be reverted. Ye be sure ye want to continue?',
        'deleted' => 'The booty import system be sunk.',
        'backToSystems' => 'Back to systems',
        'viewSystem' => 'View system',
        'unknownSystem' => 'Unknown system',
        'exportUserList' => 'Export user list',
        'exportUserListDescription' => 'Dis lists all e-bottle coordinates for pirates of booty imports within dis system, that have at least one booty import change which has been committed to a pirates wallet. Dis means that only pirates are listed that be registered, boarded a bar in dis booty, and verified they e-bottle coordinate. These pirates automatically receive booty updates from :app.',
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
        'migrateAlias' => 'Migrate alias',
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
        'migrateDescription' => 'Migrate dis balance import alias to change th\' user details. When th\' e-bottle address be changed to a verified one of a pirate, all changes for dis alias be committed to their wallet. Balance import changes for this alias that have already been committed won\'t be transferred to a different user.',
        'migrated' => 'Th\' balance import alias be migrated.',
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
        'cannotUndoIfNewerApproved' => 'Ye nay undo dis booty import change, because there be a fresher balance change for dis user that still be accepted.',
    ],

    /**
     * Balance import alias pages.
     */
    'balanceImportAlias' => [
        'newAliasMustProvideName' => 'Th\' given e-bottle coordinate nay be known, ye must provide a name.',
        'newJsonAliasMustProvideName' => 'Th\' given e-bottle coordinate \':email\' nay be known, missing name field for dis user.',
        'jsonHasDuplicateAlias' => 'Th\' JSON data contains multiple items for \':email\'.',
        'aliasWithEmailAlreadyExists' => 'An alias with dis e-bottle coordinate already exists.',
        'aliasAlreadyInEvent' => 'Th\' user \':email\' already has a change in dis event',
        'allowAddingSameUserMultiple' => 'Allow adding th\' same user more than once in current event (Not recommended)',
    ],

    /**
     * Balance import balance update email.
     */
    'balanceImportMailBalance' => [
        'title' => 'Send balance e-bottle message',
        'description' => 'Send pirates in dis booty import event a balance update e-bottle message. Dis may be used to notify pirates when a shiny booty list is imported. A message will only be sent for approved changes in dis import event. The last known balance of pirates within this system will always be used. Pirates with zero booty will not receive a message.',
        'mailUnregisteredUsers' => 'Message unregistered pirates (no account)',
        'mailNotJoinedUsers' => 'Message not-joined pirates (with account, not a bar member)',
        'mailJoinedUsers' => 'Message joined pirates (with account, and a bar member)',
        'extraMessage' => 'Extra notes',
        'relatedBar' => 'Related bar',
        'noRelatedBar' => 'No related bar',
        'mustSelectBarToInvite' => 'Ye must select a bar to invite pirates',
        'inviteToJoinBar' => 'Invite unregistered pirates to enter the bar',
        'limitToLastEvent' => 'Limit to pirates in th\' last event',
        'replyToAddress' => '\'Reply-To\' email address',
        'confirmSendMessage' => 'Confirm to send an e-bottle message',
        'sentBalanceUpdateEmail' => 'A balance update message will now be sent to the selected balance import pirates. It may take a few minutes for it to arrive.',
    ],

    /**
     * Economy finance pages.
     */
    'finance' => [
        'title' => 'Financial report',
        'cumulativeBalance' => 'Cumulative balance',
        'outstandingBalance' => 'Outstanding balance',
        'paymentsInProgress' => 'In progress',
        'noAccountImport' => 'No account (import)',
        'membersWithNonZeroBalance' => 'Members with booty',
        'description' => 'Dis shows a simple financial report for th\' current booty state. Pirates from booty imports, that nay be registered and joined dis booty, are currently not listed.',
        'overview' => [
            'title' => 'Overview',
        ],
        'members' => [
            'title' => 'Pirates',
            'description' => 'Dis report lists all active/registered members that have a non-zero balance. These members automatically be notified, payments be processed through dis app.',
            'membersPositiveBalance' => 'Pirates wit\' positive balance',
            'membersNegativeBalance' => 'Pirates wit\' negative balance',
            'noNonZeroBalances' => 'There nay be pirates wit\' a non-zero balance.',
        ],
        'aliasWallets' => [
            'title' => 'Outstanding wallets',
            'description' => 'Dis report lists all users purchases are made for, that nay be registered. These purchases are made through th\' kiosk or by others, on aliases in th\' balance import system. Once these users register an account and verify their email address, their balance listed here will be assigned to their account.',
            'aliasesPositiveBalance' => 'Aliases wit\' positive balance',
            'aliasesNegativeBalance' => 'Aliases wit\' negative balance',
            'noNonZeroBalances' => 'There nay be users wit\' a non-zero balance.',
            'resolved' => 'Resolved?',
        ],
        'imports' => [
            'title' => 'Outstanding imports',
            'description' => 'Dis report lists all non-zero balances from balance imports dat nay be committed to a pirate yet. These balances be for pirates that haven\'t registered, or haven\'t verified their e-bottle coordinate. Once these users register an account and verify their email address, their balance listed here will be assigned to their account.',
            'aliasesPositiveBalance' => 'Aliases wit\' positive balance',
            'aliasesNegativeBalance' => 'Aliases wit\' negative balance',
            'aliases' => 'Aliases',
            'noAliases' => 'Nay aliases',
            'selectSystem' => 'Select a balance import system to view details.',
            'noSystems' => 'Dis economy nay have any balance import systems.',
            'resolved' => 'Resolved?',
        ],
    ],

    /**
     * bunq account pages.
     */
    'bunqAccounts' => [
        'title' => 'bunq accounts',
        'bunqAccount' => 'bunq account',
        'description' => 'Tap on one of yer bunq accounts to manage it, or add a fresh one.',
        'noAccounts' => 'Yer nay be any bunq accounts added yet...',
        'addAccount' => 'Add bunq account',
        'addAccountDescription' => 'This page allows you to add a bunq account for automatic payment processing.<br><br>Create an API token and an empty monetary account in the bunq app. Enter the token and the IBAN of the monetary account below.<br><br>Would you like to use a test account instead?',
        'createSandboxAccount' => 'Create bunq sandbox account',
        'descriptionPlaceholder' => 'bunq account for automating bar payments',
        'tokenDescription' => 'Create a fresh API key in the developer section of th\' bunq app on yer handheld phoning device, and enter the freshly created token in dis field. Th\' token must never be shared with anyone else.',
        'ibanDescription' => 'Enter the IBAN of a monetary account in your bunq profile. This monetary account will be dedicated to payment processing and cannot be used for anything else. It is recommended to create a fresh monetary account through the bunq application for this.',
        'invalidApiToken' => 'Invalid API token',
        'addConfirm' => 'By adding this bunq account, you give :app full control over the monitary account assigned to the specified IBAN. That account will be dedicated to automated payment processing, until this link between :app and bunq is dropped. Never drop this link through the mobile bunq app by deleting the API key, but drop it through :app to ensure any ongoing payments can be finished properly. The account must have a current balance of €0.00. You cannot use this monetary account for any other payments, applications or :app instances, and you might risk serious money-flow issues if you do so. :app is not responsible for any damage caused by linking your bunq account to this application.',
        'createSandboxConfirm' => 'This will create a bunq sandbox account for testing purposes. Please be aware that this will allow anyone to top-up their :app wallet without paying real money. :app is not responsible for any damage caused by linking your bunq account to this application.',
        'mustEnterBunqIban' => 'You must enter a bunq IBAN',
        'accountAlreadyUsed' => 'This monetary is already used',
        'noAccountWithIban' => 'No active monetary account with this IBAN',
        'onlyEuroSupported' => 'Only accounts using EURO currency are supported',
        'notZeroBalance' => 'Account does not have a balance of €0.00, create a fresh monitary account',
        'added' => 'The bunq account be added.',
        'changed' => 'The bunq account be changed.',
        'paymentsEnabled' => 'Payments enabled',
        'checksEnabled' => 'Checks enabled',
        'enablePayments' => 'Enable payments: allow usage for fresh payments',
        'enableChecks' => 'Enable checks: check periodically for fresh received payments',
        'confirm' => 'I agree with this and meet th\' requirements',
        'environment' => 'bunq API environment',
        'runHousekeeping' => 'Run housekeeping',
        'runHousekeepingSuccess' => 'Th\' monetary bunq account be reconfigured and any pending payments now be queued for processing.',
        'noHttpsNoCallbacks' => 'Dis site does nay be HTTPS, real time bunq payments nay be supported. Payment events be processed daily.',
        'manageCommunityAccounts' => 'Manage community bunq accounts',
        'manageAppAccounts' => 'Manage application global bunq accounts',
        'lastCheckedAt' => 'Last checked at',
        'lastRenewedAt' => 'API last renewed at',
        'notRenewedYet' => 'Not yet renewed',
    ],

    /**
     * Wallet pages.
     */
    'wallets' => [
        'title' => 'Wallets',
        'description' => 'Tap on one of yer wallets to manage it, or create a fresh one.',
        'walletEconomies' => 'Wallet booties',
        'myWallets' => 'Me wallets',
        '#wallets' => '{0} No wallets|{1} 1 wallet|[2,*] :count wallets',
        'economySelectDescription' => 'Wallets in dis community be divided by booty.<br>Select \'the booty to manage yer wallets.',
        'noWallets' => 'Nay wallets...',
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
        'topUp' => 'Top up wallet',
        'topUpNow' => 'Top up yarr!',
        'noWalletToTopUp' => 'Ye nay have booty here to top up.',
        'modifyBalance' => 'Modify balance',
        'modifyMethod' => 'Modify method',
        'modifyMethodDeposit' => 'Deposit (add)',
        'modifyMethodWithdraw' => 'Withdraw (subtract)',
        'modifyMethodSet' => 'Set to balance',
        'modifyBalanceWarning' => 'Dis changes the wallet balance, but no real money be exchanged through :app. Dis change be logged and will be visible to th\' user.',
        'confirmModifyBalance' => 'Confirm balance modification',
        'balanceModified' => 'Th\' wallet balance be modified.',
        'successfullyTransferredAmount' => 'Successfully transfered :amount to :wallet',
        'backToWallet' => 'Back to wallet',
        'walletTransactions' => 'Wallet transactions',
        'noServiceConfiguredCannotTopUp' => 'Ye nay top-up yer wallet through :app. Th\' bar \'o crew administrator nay configured any payment method. Please ask at yer bar for further info.',
    ],

    /**
     * Wallet stats pages.
     */
    'walletStats' => [
        'title' => 'Wallet statistics',
        'description' => 'Here are some wallet statistics for the selected period.',
        'transactions' => 'Transactions',
        'income' => 'Income',
        'expenses' => 'Expenses',
        'paymentIncome' => 'Payment income',
        'productExpenses' => 'Product spendings',
        'products' => 'Loot',
        'uniqueProducts' => 'Unique loot',
        'balanceHistory' => 'Balance history',
        'purchaseDistribution' => 'Product distribution',
        'purchasePerDay' => 'Product purchases per weekday (UTC)',
        'purchasePerHour' => 'Product purchases per day hour (UTC)',
        'purchaseHistogram' => 'Product purchase histogram',
        'noStatsNoTransactions' => 'No statistics to show. Wallet does not have any transactions.',
        'period' => [
            'week' => 'Past week',
            'month' => 'Past month',
            'year' => 'Past year',
        ],
        'typeProductDist' => [
            'title' => 'Purchased loot',
            'chartName' => 'Loot distribution chart',
        ],
        'smartText' => [
            'main' => 'In th\' :period ye be active on <b>:active-days</b>:best-day. During dis period ye got <b>:products</b>:products-unique.',
            'mainDays' => '{0} no days|{1} one day|[2,*] :count different days',
            'mainBestDay' => ' of which <b>:day</b> was yer best day',
            'mainUniqueProducts' => ', of which :unique unique',
            'productCount' => '{0} nay loot|{1} one loot|[2,*] :count loot',
            'productUniqueCount' => '{0} nay|{1} <b>one</b> was|[2,*] <b>:count</b> were',
            'partBestProduct' => 'Ye got <b>:product</b> the most:extra.',
            'partBestProductExtra' => ', followed by <b>:product</b>:extra',
        ],
    ],

    /**
     * Transaction pages.
     */
    'transactions' => [
        'title' => 'Transactions',
        'details' => 'Transaction details',
        'last#' => '{0} Last transactions|{1} Last transaction|[2,*] Last :count transactions',
        'noTransactions' => 'No transactions',
        'backToTransaction' => 'Back to transaction',
        'toTransaction' => 'to transaction',
        'fromTransaction' => 'from transaction',
        'referencedTo#' => '{0} Referenced to no transactions|{1} Referenced to transaction|[2,*] Referenced to :count transactions',
        'referencedBy#' => '{0} Referenced by no transactions|{1} Referenced by transaction|[2,*] Referenced by :count transactions',
        'cannotUndo' => 'This transaction cannot be undone.',
        'undone' => 'The transaction has been undone.',
        'undoTransaction' => 'Undo transaction',
        'selectProductsToUndo' => 'Select loot to undo',
        'noProductsSelected' => 'No loot selected',
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
            'balanceImport' => 'Import external balance',
            'fromWalletToProduct' => 'Purchased loot',
            'toProduct' => 'Purchased loot',
            'fromPaymentToWallet' => 'Deposit to wallet',
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
            'productTo' => 'Paid for loot(s)',
            'productFrom' => 'Received booty for loot',
            'productToDetail' => 'Paid for :products',
            'productFromDetail' => 'Received booty for :products',
            'paymentTo' => 'Withdrawal to external account',
            'paymentFrom' => 'Deposit from external account',
            'paymentToDetail' => 'Withdrawal via :payment',
            'paymentFromDetail' => 'Deposit via :payment',
            'balanceImport' => 'Import balance of external system',
            'balanceImportDetail' => 'Import balance of external system by :user',
        ],
    ],

    /**
     * Notification pages.
     */
    'notifications' => [
        'title' => 'Notifications',
        'notification' => 'Notification',
        'description' => 'Dis shows all yer notifications, both fresh and read.',
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
        '#payments' => '{0} No payments|{1} 1 payment|[2,*] :count payments',
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
        'paymentRequiresCommunityAction' => 'Dis payment awaits action by a crew manager. If ye nay have access to th\' receiving account, take no action and leave this check to rot for a different group manager who does have access.',
        'cancel' => 'Aboart payment',
        'cancelPaymentQuestion' => 'Yer about to aboart dis payment. Never aboart a payment for which yer already transfered money, or ye transfer might be lost. Ye be sure ye want to continue?',
        'paymentCancelled' => 'Payment aboarted',
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
        'searchByCommunity' => 'Scavenge by crew',
        'searchByCommunityDescription' => 'It\' usually be easier to find ye bar by it\'s crew.',

        // TODO: remove duplicates
        'createBar' => 'Create bar',
        'editBar' => 'Edit bar',
        'deleteBar' => 'Delete bar',
        'join' => 'Join',
        'yesJoin' => 'Yay, sail ho!',
        'joined' => 'Joined',
        'youAreJoined' => 'You joined dis bar.',
        'notJoined' => 'Strangers!',
        'leave' => 'Leave',

        'hintJoin' => 'Ye be not part of th\' bar.',
        'joinQuestion' => 'Ye like be joined th\' bar?',
        'alsoJoinCommunity' => 'Also be join their crew',
        'alreadyJoinedTheirCommunity' => 'Ye already be a pirate of their crew',
        'joinedThisBar' => 'Ye joined th\' bar.',
        'cannotSelfEnroll' => 'Ye cannot join dis bar yerself, it be disabled.',
        'leaveQuestion' => 'Ye be sure to sink th\' bar?',
        'leftThisBar' => 'Ye sunk th\' bar.',
        'cannotLeaveHasWallets' => 'Ye nay leave dis bar while ye have a wallet in it.',
        'protectedByCode' => 'Dis bar be protected by a secret. Request it at yer bar, or use yer spyglass to scan the bar Q-ARRRR code if available.',
        'incorrectCode' => 'Bar code be incorrect.',
        'namePlaceholder' => 'Queen Anne\'s ship',
        'descriptionPlaceholder' => 'Say ho to Queen Anne\'s ship!',
        'slugDescription' => 'Ye slug allows ye to create \'n easy to remember URL to access dis bar, by defining a short keyword.',
        'slugDescriptionExample' => 'Dis could simplify ye bar URL:',
        'slugPlaceholder' => 'anne',
        'slugFieldRegexError' => 'Dis slug must start with n alphabetical character.',
        'codeDescription' => 'With a bar secret, ye prevent random pirates from joining. To join the bar, users be required to enter th\' secret.',
        'economyDescription' => 'Th\' booty defines what loot, currencies and wallets be used in dis bar. Be very careful wit\' changing it after th\' bar be created, as this immediately affects th\' list of loot, currencies and wallets used in dis bar. Mateies probably don\'t expect dis, and might find it hard to understand.',
        'inventoryDescription' => 'Select an inventory to automatically keep track of inventory when products are sold.',
        'selectInventoryAfterCreate' => 'Ye may select th\' inventory after th\' bar be created.',
        'showExploreDescription' => 'List on public \'Explore bars\' page',
        'showCommunityDescription' => 'List on crew page for crew mateies',
        'selfEnrollDescription' => 'Allow self enrollment (wit\' code if specified)',
        'joinAfterCreate' => 'Join th\' bar after creating.',
        'created' => 'Th\' bar be created.',
        'updated' => 'Th\' bar be updated.',
        'mustCreateEconomyFirst' => 'To create a bar, ye must create booty first.',
        'backToBar' => 'Back to bar',
        'quickBuy' => 'Get now',
        'boughtProductForPrice' => 'Got :product for :price',
        'noDescription' => 'Dis bar be nay description',
        'manageBar' => 'Manage bar',
        'barInfo' => 'Bar info',
        'viewBar' => 'View bar',
        'deleted' => 'Th\' bar be sunk.',
        'deleteQuestion' => 'Yer \'bout to permanently sink dis bar. All mateies including yerself will lose access to it, and it nay be possible to link loot transactions to it anymore. Th\' loot and matey wallets will remain as part of th\' booty that be used in dis bar. Ye be sure ye want to continue?',
        'exactBarNameVerify' => 'Exact name of bar to sink (Verification)',
        'incorrectNameShouldBe' => 'Incorrect name, should be: \':name\'',
        'kioskManagement' => 'Kiosk management',
        'startKiosk' => 'Start kiosk',
        'startKioskDescription' => 'Here you can start kiosk mode. When you start kiosk mode, ye will be logged out here from your personal account, and a central terminal interface will be started for this bar which everybody can use to purchase loot. This mode will be active until you manually turn it off by logging out once more on this device.',
        'startKioskConfirm' => 'Confirm to enter kiosk mode',
        'startKioskConfirmDescription' => 'Entering kiosk mode will allow any pirate having access to dis machine to purchase loot on anybodies behalf.',
        'kioskSessions' => 'Kiosk sessions',
        'kioskSessionsDescription' => 'Dis page shows th\' active and sunk kiosk sessions for this bar. Click on an active session to see details or to sink it. Sunk sessions are automatically forgotten after a while.',
        'expireAllKioskSessionsQuestion' => 'Ye be sure ye want to sink all kiosk sessions? Dis will log out all kiosks for dis bar.',
        'generatePoster' => 'Bar placard',
        'generatePosterDescription' => 'Create a placard for dis bar to hang on a wall. Fellow mateies will then be able to easily use :app and join dis bar by scanning a Q-ARRRR code with their handheld phoning device.',
        'showCodeOnPoster' => 'Show code to join dis bar on th\' placard',
        'lowBalanceText' => 'Negative balance text',
        'lowBalanceTextPlaceholder' => 'Ye currently have a negative balance. Please top-up yer wallet now before getting fresh loot.',
        'allPurchases' => 'All purchases',
        'purchases' => 'Purchases',
        'purchasesDescription' => 'Dis page shows a history of all purchased loot in dis bar.',
        'exportPurchasesTitle' => 'Export purchases',
        'exportPurchasesDescription' => 'Dis page allows ye to export all purchases made in dis bar to a file.',
        'noPurchases' => 'No purchases',
        'poster' => [
            'thisBarUses' => 'Dis bar uses',
            'toDigitallyManage' => 'to digitally manage booty and stock for consumptions',
            'scanQr' => 'scan the Q-ARRRR code below to join and make a purchase',
            'orVisit' => 'Or see',
        ],
        'buy' => [
            'forMe' => 'Get yerself',
            'forOthers' => 'For crew/more',
        ],
        'advancedBuy' => [
            'tapProducts' => 'Select loot to get for any pirate.',
            'tapUsers' => 'Tap pirates to add the selected loot in cart for.',
            'tapBuy' => 'Tap th\' blue Get button to commit the purchase.',
            'addToCartFor' => 'Add selected to cart for',
            'searchUsers' => 'Scavenge pirates',
            'searchingFor' => 'Scavenging :term',
            'noUsersFoundFor' => 'No pirates found for :term',
            'inCart' => 'In cart',
            'buyProducts#' => '{0} Get no loot|{1} Get loot|[2,*] Get :count loot',
            'buyProductsUsers#' => '{0} Get no loot for :users pirates|{1} Get loot for :users pirates|[2,*] Get :count loot for :users pirates',
            'pressToConfirm' => 'Sail ho?',
            'boughtProducts#' => '{0} Not looted.|[1,*] :count looted.',
            'boughtProductsUsers#' => '{0} No loot for :users pirates.|[1,*] :count loot for :users pirates.',
            'pageCloseWarning' => 'Ye selected or has loot in cart that nay be gotten yet. Ye must add loot selection to at least one pirate and tap th\' Get button to commit th\' purchase, or the selection will be lost.',
        ],
        'links' => [
            'title' => 'Useful coordinates',
            'description' => 'Dis page lists various shareable coordinates for dis bar. Ye may share these through e-bottle messages or print dem on a placard. Some of these coordinates allow you to direct other pirates to specific otherwise hidden pages and intents.<br><br>Please be aware that some coordinates change when modifying bar settings, and some coordinates contain secret bits.',
            'linkBar' => 'Main bar page',
            'linkBarAction' => 'Visit :bar',
            'linkJoinBar' => 'Invite fresh pirates to join bar',
            'linkJoinBarAction' => 'Join :bar',
            'linkJoinBarCode' => 'Invite fresh pirates to join bar (with code)',
            'linkJoinBarCodeAction' => 'Join :bar',
            'linkQuickWallet' => 'Show main personal wallet',
            'linkQuickWalletAction' => 'View yer personal wallet',
            'linkQuickTopUp' => 'Top-up main personal wallet',
            'linkQuickTopUpAction' => 'Top-up yer wallet',
            'linkQuickTopUpRedemption' => 'Top-up main personal wallet (redemption)',
            'linkQuickTopUpRedemptionAction' => 'Top-up yer wallet (redemption)',
            'linkVerifyEmail' => 'Verify e-bottle coordinates',
            'linkVerifyEmailAction' => 'Verify yer e-bottle coordinate',
        ],
        'checklist' => 'Bar checklist',
    ],

    /**
     * Bar membership page.
     */
    'barMember' => [
        'title' => 'Membership',
        'memberSettings' => 'Membership settings',
        'showInBuy' => 'Visible in buy screens',
        'showInKiosk' => 'Visible in kiosk',
        'updated' => 'Ye settings be stowed.',
        'visibility' => 'Visibility',
        'visibilityDescription' => 'Below be may configure yer visibility as bar mateie. Disabling visibility be useful if ye want to prevent other pirates from getting loot on yer behalf. It be recommended to keep all toggles on.<br><br>Visibility in buy screens specifies whether ye be shown in th\' list of mateies when getting loot using their phone. Visibility in kiosk specifies whether ye be shown in th\' list of pirates on a central bar kiosk device.',
    ],

    /**
     * Kiosk page.
     */
    'kiosk' => [
        'loading' => 'Loading ship',
        'selectUser' => 'Select pirate',
        'addToUser' => 'Add to pirate',
        'searchUsers' => 'Scavenge pirates',
        'searchingFor' => 'Scavenging :term...',
        'noUsersFoundFor' => 'Nay for :term',
        'firstSelectUser' => 'Select a pirate on the left to make a purchase for',
        'firstSelectProduct' => 'Select loot on the left to purchase for a pirate',
        'selectProducts' => 'Select loot',
        'buyProducts#' => '{0} Get no loot|{1} Get loot|[2,*] Get :count loot',
        'buyProductsUsers#' => '{0} Get no loot for :users pirates|[1,*] Get :count× for :users pirates',
        'cartTimeoutDescription' => 'Forgot purchase?<br><br>Selected loot nay be purchased yet.',
        'viewCart' => 'Continue looting',
        'resetCart' => 'Reset loot',
        'swapColumns' => 'Swap columns',
        'backToKiosk' => 'Back to kiosk',
        'noConnectionBanner' => 'Connection errrror! Pull the page down to refresh.',
    ],

    /**
     * Kiosk join pages.
     */
    'kioskJoin' => [
        'title' => 'Add pirate / join',
        'joinBar' => 'Join :bar',
        'description' => 'Fresh pirates can add themselves to dis bar by registering an account.',
        'scanQr' => 'Scan th\' Q-ARRRR code below with yer phone to start:',
        'orUrl' => 'Or visit th\' following link in yer browser:',
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
        'activePastWeek' => 'Active past week',
        'activePastMonth' => 'Active past month',
        'productsPastHour' => 'Looted past hour',
        'productsPastDay' => 'Looted past day',
        'productsPastWeek' => 'Looted past week',
        'productsPastMonth' => 'Looted past month',
    ],

    /**
     * Bar member pages.
     */
    'barMembers' => [
        'title' => 'Bar mateies',
        'description' => 'Dis page shows th\' overview o\' all bar mateies. Tap a matey allows ye to remove the matey, or change be rank.',
        'search' => 'Search mateies',
        'nickname' => 'Display name',
        'nicknameDescription' => 'Ye may set a custom display name for yerself. With a display name set, yer name be hidden and yer custom name will be shown in buy and kiosk screens. Dis be intended for special pirates where showing their own name doesn\'t make sense. To prevent confusion set a clear and descriptive name or better yet, don\'t set a name at all.',
        'tagsDescription' => 'You may set search tags to help other pirates find ye when looting. Separate each tag by a space.',
        'noMembers' => 'Nay mateies...',
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
        'message' => 'If yer e-bottle coordinate ye entered be known by our captain, our jolly crew sent ye instructions for a shiny passcode to yer e-bottle-box.<br><br>'
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
        'questions' => 'Questions?',
        'questionsDescription' => 'If ye have any further questions about our Piracy Policy or yer piracy when using our seas, be sure to ship us a bottle message.',
    ],

    /**
     * Terms of Service page.
     */
    'terms' => [
        'title' => 'Terms',
        'description' => 'When ye use our seas, ye be agree with our Terms o\' Service as shown below.',
        'questions' => 'Questions?',
        'questionsDescription' => 'If ye have any further questions about our Terms o\' Service, be sure to ship us a bottle message.',
    ],

    /**
     * License page.
     */
    'license' => [
        'title' => 'License',
        'description' => 'Th\' Arrbapappa software project be open-source, and be released under th\' GNU AGPL-3.0 license. Dis license maps what ye are and nay be allowed to do with th\' public source code of dis software project. Dis license does not have any effect on the usage information processed within dis application.<br><br>Read th\' full license below, o\' check out th\' summary for dis license as quick summary.',
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
        'description' => 'Use th\' following coordinates to contact Arrbapappa:',
        'issuesDescription' => 'Dis ship is open-source, and its schematics be openly available. Ye may view the schematics and issue list at th\' links below.',
        'issueList' => 'Issue list',
        'newIssueMail' => 'Report issue',
        'thisAppIsOpenSource' => 'Dis ship has open schematics',
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
        'description' => ':app be a digital bar management application to facilitate a pirate-controlled sea for purchase processing, payment handling and treasure management.<br><br>:app be a fully automated solution for small self-managed bars and crews, to take away hassle of manually registering purcahses using tally marks on yer paper.<br><br>For any interest in using dis platform for yer own crew, sure to send us a message!',
        'developedBy' => 'Dis project be developed & maintained by',
        'sourceDescription' => 'I make th\' software that I develop open-source. Th\' complete project with its source code be available free of charge, for all pirates. I believe it be important to allow any pirate to inspect, modify, improve, contribute, and verify without restrictions.',
        'sourceAt' => 'Latest source code be available on GitLab',
        'withLicense' => 'Released with th\' following license',
        'usedTechnologies' => 'Some awesome technologies be used include',
        'noteLaravel' => 'backend framework',
        'noteSemanticUi' => 'frontend theming framework',
        'noteGlyphicons' => 'icons & symbols',
        'noteFlags' => 'pirateflags',
        'noteGetTerms' => 'terms & piracy policy template',
        'noteEDegen' => 'suggested \'Arrbapappa\'',
        'otherResources' => 'Other awesome resources include',
        'donate' => 'A lot of effort went into dis project.<br>Want to donate me a beer?',
        'thanks' => 'Thank ye for using dis product.<br>Thank ye for being awesome.',
        'copyright' => 'Copyright © :app :year.<br>All rights reserved.',
    ],

    /**
     * Error pages.
     */
    'errors' => [
        // TODO: move noPermission view into this
        '401' => [
            'title' => '401 Unauthorized',
            'description' => 'Ye compass be upside down, ye sailed th\' wrong seas.<br />Nay access to th\' sea.',
        ],
        '403' => [
            'title' => '403 Forbidden',
            'description' => 'Ye compass be upside down, ye sailed th\' wrong seas.<br />Nay access to th\' sea.',
        ],
        '404' => [
            'title' => '404 Not Found',
            'description' => 'Ye compass be upside down, ye sailed th\' wrong seas.<br />Ye sea ye lookin for nay exist.',
        ],
        '419' => [
            'title' => '419 Page Expired',
            'description' => 'Whoops! Dis sea be expired.',
        ],
        '429' => [
            'title' => '429 Too Many Requests',
            'description' => 'Whoops! Too many requests be made to dis page recently on dis network. Please wait some time before trying again.',
        ],
        '500' => [
            'title' => '500 Server Error',
            'description' => '<i>Houston, we have a problem!</i><br><br>An error occurred on our end. The administrators have been notified and are looking into it.',
        ],
        '503' => [
            'title' => '503 Service Unavailable',
            'description' => '<i>Houston, we have a problem!</i><br><br>An error occurred on our end, which results in us not being able to serve your request. The administrators have been notified and are looking into it.',
        ],
    ],
];
