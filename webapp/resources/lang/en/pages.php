<?php

/**
 * Pages and their names.
 */
return [
    'pages' => 'Pages',
    'emailPreferences' => 'Email preferences',
    // TODO: move to misc?
    'communities' => 'Communities',
    // TODO: move to misc?
    'bars' => 'Bars',
    'account' => 'Account',
    'yourAccount' => 'Your account',
    'requestPasswordReset' => 'Request password reset',
    'changePassword' => 'Change password',
    'changePasswordDescription' => 'To change your password, fill in the fields below.',

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
        'title' => 'Home page',
        'emailAndContinue' => 'Enter your email address to login or register.',
        'backToIndex' => 'Back to home page',
    ],

    /**
     * Dashboard page.
     */
    'dashboard' => [
        'title' => 'Dashboard',
        'yourPersonalDashboard' => 'Your personal dashboard',
        'noBarsOrCommunities' => 'No bars or communities',
        'nothingHereNoMemberUseExploreButtons' => 'There\'s nothing to show here because you aren\'t a member of any bar or community yet. Find yours using the following buttons.',
    ],

    /**
     * Last page.
     */
    'last' => [
        'title' => 'Back to bar',
        'noLast' => 'You haven\'t visited a bar yet, visit one now!',
    ],

    /**
     * Profile page.
     */
    'profile' => [
        'name' => 'Profile'
    ],

    /**
     * Profile edit page.
     */
    'editProfile' => [
        'name' => 'Edit profile',
        'updated' => 'Your profile has been updated.',
        'otherUpdated' => 'The profile has been updated.',
    ],

    /**
     * Account page.
     */
    'accountPage' => [
        'description' => 'This page shows an overview of your account.',
        'email' => [
            'description' => 'This page shows your email addresses.',
            'emails' => 'Email addresses',
            'yourEmails' => 'Your email addresses',
            'unverifiedEmails' => 'Unverified email addresses',
            'verifyEmails' => 'Verify email addresses',
            'unverifiedDescription' => 'This page lists your email addresses that are still unverified. Clikc on the blue buttom below to start the verification process.',
            'resendVerify' => 'Resend verification',
            'unverified#' => '{0} No unverified emails|{1} Unverified email|[2,*] :count unverified emails',
            'verify#' => '{0} Verify no email addresses|{1} Verify email address|[2,*] Verify :count email addresses',
            'verifiedDescription' => 'We sent a new verification message to your email addresses listed below. Click the link in the message to complete verification. When you\'ve completed the verification you may click the button om the bottom of the page to continue.',
            'iVerifiedAll' => 'I verified all',
            'someStillUnverified' => 'Some of your email addresses are still unverified. Please see the list below. Check your email inbox for a verification message.',
            'verifySent' => 'A new verification email will be sent shortly.',
            'alreadyVerified' => 'This email address has already been verified.',
            'allVerified' => 'All your email addresses have been verified.',
            'cannotDeleteMustHaveOne' => 'You cannot delete this email address, you must have at least one address.',
            'cannotDeleteMustHaveVerified' => 'You cannot delete this email address, you must have at least one verified address.',
            'deleted' => 'The email address has been deleted.',
            'deleteQuestion' => 'Are you sure you want to delete this email address?',
            'notifyOnLowBalance' => 'Receive notification when your balance drops below zero',
            'backToEmails' => 'Back to e-mails',
        ],
        'addEmail' => [
            'title' => 'Add email address',
            'description' => 'Fill in the email address you\'d like to add.',
            'added' => 'Email address added. A verification email has been sent.',
            'cannotAddMore' => 'You can\'t add more email addresses to your account. Delete an existing address in order to add a new one.',
        ],
        'backToAccount' => 'Back to account',
    ],

    /**
     * Explore pages.
     */
    'explore' => [
        'title' => 'Explore',
        'exploreBars' => 'Explore bars',
        'exploreCommunities' => 'Explore communities',
        'exploreBoth' => 'Explore communities & bars',
    ],

    /**
     * Community pages.
     */
    'community' => [
        'yourCommunities' => 'Your communities',
        'noCommunities' => 'No communities available...',
        'viewCommunity' => 'View community',
        'viewCommunities' => 'View communities',
        'visitCommunity' => 'Visit community',
        'createCommunity' => 'Create community',
        'editCommunity' => 'Edit community',
        'deleteCommunity' => 'Delete community',
        'join' => 'Join',
        'yesJoin' => 'Yes, join',
        'joined' => 'Joined',
        'youAreJoined' => 'You joined this community.',
        'leave' => 'Leave',
        'notJoined' => 'Not joined',
        'hintJoin' => 'You aren\'t part of this community yet.',
        'joinQuestion' => 'Would you like to join this community?',
        'joinedThisCommunity' => 'You\'ve joined this community.',
        'cannotSelfEnroll' => 'You cannot join this community yourself, it is disabled.',
        'leaveQuestion' => 'Are you sure you want to leave this community?',
        'leftThisCommunity' => 'You left this community.',
        'cannotLeaveStillBarMember' => 'You cannot leave this community, because you\'re still a member of a bar in this community',
        'protectedByCode' => 'This community is protected by a passcode. Request it at the community, or scan the community QR code if available.',
        'incorrectCode' => 'Incorrect community code.',
        'namePlaceholder' => 'The Vikings',
        'descriptionPlaceholder' => 'Welcome to thé Viking community.',
        'slugDescription' => 'A slug allows you to create an easy to remember URL to access this community, by defining a short keyword.',
        'slugDescriptionExample' => 'This could simplify your community URL:',
        'slugPlaceholder' => 'vikings',
        'slugFieldRegexError' => 'The slug must start with an alphabetical character.',
        'codeDescription' => 'With a community code, you prevent random users from joining. To join the community, users are required to enter the specified code.',
        'showExploreDescription' => 'List on public \'Explore communities\' page',
        'selfEnrollDescription' => 'Allow self enrollment (with code if specified)',
        'joinAfterCreate' => 'Join the community after creating.',
        'created' => 'The community has been created.',
        'updated' => 'The community has been updated.',
        'economy' => 'Economy',
        'goTo' => 'Go to community',
        'backToCommunity' => 'Back to community',
        'noDescription' => 'This community has no description',
        'communityInfo' => 'Community info',
        'manageCommunity' => 'Manage community',
        'inCommunity' => 'in community',
        'deleted' => 'The community has been deleted.',
        'deleteQuestion' => 'You\'re about to permanently delete this community. All members including yourself will lose access to it. All bars, economies, user wallets, products and related entities that are used within this community will be deleted as well. Are you sure you want to continue?',
        'deleteBlocked' => 'You\'re about to permanently delete this community. You must delete the entities listed below first before you can continue with deleting this community.',
        'exactCommunityNameVerify' => 'Exact name of community to delete (Verification)',
        'incorrectNameShouldBe' => 'Incorrect name, should be: \':name\'',
        'cannotDeleteDependents' => 'This community cannot be deleted, as entities are depending on it which cannot just be deleted.',
        'generatePoster' => 'Create community poster',
        'generatePosterDescription' => 'Create a poster for this community to hang on a wall. Visitors will then be able to easily use :app and join this community by scanning a QR code with their mobile phone.',
        'showCodeOnPoster' => 'Show code to join this community on the poster',
        'posterBarPreferred' => 'It is usually preferred to generate a poster for a bar instead of a community, as users joining a community doesn\'t give them acces to purchasing products without joining a bar as well. Visit the management hub of a specific bar to create a poster for it.',
        'poster' => [
            'thisCommunityUses' => 'This community uses',
            'toDigitallyManage' => 'to digitally manage payments and inventory for consumptions',
            'scanQr' => 'scan the QR code with your phone to join and make a purchase',
            'orVisit' => 'Or visit',
        ],
        'links' => [
            'title' => 'Useful links',
            'description' => 'This page lists various shareable links for this community. You may share these through email or print them on a poster. Some of these links allow you to direct users to specific otherwise hidden pages and intents.<br><br>Please be aware that some links change when modifying community settings, and some links contain secret bits.',
            'linkCommunity' => 'Main community page',
            'linkCommunityAction' => 'Visit :community',
            'linkJoinCommunity' => 'Invite new user to join community',
            'linkJoinCommunityAction' => 'Join :community',
            'linkJoinCommunityCode' => 'Invite new user to join community (with code)',
            'linkJoinCommunityCodeAction' => 'Join :community',
        ],
        'checklist' => 'Community checklist',
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
        'title' => 'Community members',
        'description' => 'This page shows an overview of all community members.<br>Clicking on a member allows you to remove the member, or change it\'s role.',
        'noMembers' => 'This community has no members...',
        'memberSince' => 'Member since',
        'lastVisit' => 'Last visit',
        'deleteQuestion' => 'You\'re about to remove this member from this community. Are you sure you want to continue?',
        'memberRemoved' => 'The member has been removed.',
        'memberUpdated' => 'Member changes saved.',
        'incorrectMemberRoleWarning' => 'Assigning an incorrect role that is too permissive to a member may introduce significant security issues.',
        'ownRoleDowngradeWarning' => 'By downgrading your own role you might lose management access to this community. Be very careful.',
        'confirmRoleChange' => 'Confirm role change for community member',
        'confirmSelfDelete' => 'Confirm to kick yourself as community member, you will lose your role',
        'cannotDemoteLastManager' => 'You cannot demote the last community member with this (or a more permissive) management role.',
        'cannotEditMorePermissive' => 'You cannot edit a community member with a more permissive role than yourself.',
        'cannotSetMorePermissive' => 'You cannot set a more permissive role for a community member than your current role.',
        'cannotDeleteLastManager' => 'You cannot kick the last community member with this (or a more permissive) management role.',
    ],

    /**
     * Community economy pages.
     */
    'economies' => [
        'title' => 'Economies',
        'description' => 'This page shows an overview of the economies available in this community.<br>Click on an economy to manage it, or create a new one for a new bar.',
        'manage' => 'Manage economies',
        'noEconomies' => 'This community has no economies...',
        'createEconomy' => 'Create economy',
        'editEconomy' => 'Edit economy',
        'deleteEconomy' => 'Delete economy',
        'economyCreated' => 'The economy has been created. Please add and configure a currency now.',
        'deleteQuestion' => 'You\'re about to delete this economy from this community. This will permanently delete things inside it, such as balance imports, along with it. Are you sure you want to continue?',
        'deleteBlocked' => 'You\'re about to permanently delete this economy. You must delete the entities listed below first before you can continue with deleting this economy.',
        'cannotDeleteDependents' => 'This economy cannot be deleted, as entities are depending on it which cannot just be deleted.',
        'economyDeleted' => 'The economy has been removed.',
        'economyUpdated' => 'Economy changes saved.',
        'namePlaceholder' => 'Main economy',
        'backToEconomy' => 'Back to economy',
        'backToEconomies' => 'Back to economies',
        'inEconomy' => 'in economy',
        'noWalletsInEconomy' => 'There are no wallets in this economy.',
        'walletOperations' => 'Wallet operations',
        'zeroAllWallets' => 'Zero all wallets',
        'zeroAllWalletsQuestion' => 'You are about to set the balance of all member wallets in this economy back to zero. Make sure to export all desired wallet data before doing this. Are you sure you want to continue?',
        'zeroAllWalletsDescription' => 'Balance reset by administrator',
        'zeroAllWalletsConfirmText' => 'zero all user wallets',
        'walletsZeroed' => 'All member wallets have been zeroed.',
        'deleteAllWallets' => 'Delete all wallets',
        'deleteAllWalletsQuestion' => 'You are about to permanently delete all member wallets in this economy. Make sure to export all desired wallet data before doing this. Are you sure you want to continue?',
        'deleteAllWalletsConfirmText' => 'delete all user wallets',
        'cannotDeleteWalletsNonZero' => 'Cannot delete all wallets because some memer wallets have a non-zero balance. You must zero all wallet balances first.',
        'confirmDeleteAllWallets' => 'Confirm to permanently delete all member wallets',
        'walletsDeleted' => 'All member wallets have been deleted.',
    ],

    /**
     * Community economy payment pages.
     */
    'economyPayments' => [
        'title' => 'Payments',
        'description' => 'This page shows all payments initiated by community members in this economy.',
        'exportTitle' => 'Export payments',
        'exportDescription' => 'This page allows you to export all payments initiated by community members in this economy to a file to view or import in an external program.',
    ],

    /**
     * Community economy currency pages.
     */
    'currencies' => [
        'title' => 'Currencies',
        'description' => 'This page shows an overview of the currencies in the economy.<br>At least one currency must be enabled to use this economy for a bar.<br>Add a new currency, or click on one to manage it.',
        'change' => 'Change currency',
        'noCurrencies' => 'This economy has no currencies...',
        'createCurrency' => 'Add currency',
        'currencyCreated' => 'The currency has been added to the economy.',
        'deleteQuestion' => 'You\'re about to remove this currency from this economy. Are you sure you want to continue?',
        'deleteVoidNotice' => 'When you remove this currency, all configured product prices for this currency will be voided in bars that use this economy.<br>You might want to disable this currency instead by changing it.',
        'currencyDeleted' => 'The currency has been removed.',
        'currencyUpdated' => 'Currency changes saved.',
        'enabledDescription' => 'Specify whether this currency is enabled in bars that are using this economy. If disabled, bar members won\'t be able to purchase products with this currency, and must use a different currency if available until it is enabled again.',
        'changeCurrencyTitle' => 'Change currency?',
        'changeCurrencyDescription' => 'You must only make very small changes to the currency properties, to prevent introducing issues where this currency is currently used. Consider to add a new currency instead.',
        'allowWallets' => 'Allow wallet creation',
        'allowWalletsDescription' => 'With this option you can specify whether bar members can create a new personal wallet for this currency. Existing wallets will always be kept.',
        'manage' => 'Manage currencies',
        'namePlaceholder' => 'Euro',
        'detailDescription' => 'Configure properties for the new currency. Make sure these are as accurate as possible when adding an internationally used currency, because payment services rely on these properties. Some properties cannot be changed anymore after adding.',
        'nameDescription' => 'Provide a currency name, e.g. \'Euro\' or \'Tokens\'',
        'codeDescription' => 'Provide the international currency code as defined by ISO 4217 if this is an international currency. For custom currencies such as \'Tokesn\' you should leave it empty.',
        'symbolDescription' => 'Provide a desired symbol for this currency. E.g. \'€\' or \'T\'',
        'formatDescription' => 'Provide the currency format, to define how :app shows a money amount in this currency to users. E.g. \'€1.0,00\' or \'B1.0\'',
        'code' => 'Currency code',
        'codePlaceholder' => 'EUR',
        'symbolPlaceholder' => '€',
        'format' => 'Currency format',
        'formatPlaceholder' => '€1.0,00',
        'exampleNotation' => 'Example notation',
        'cannotDeleteHasWallet' => 'You cannot delete this currency, because a wallet exists using this currency.',
        'cannotDeleteHasMutation' => 'You cannot delete this currency, because a transaction exists using this currency.',
        'cannotDeleteHasPayment' => 'You cannot delete this currency, because a payment exists using this currency.',
        'cannotDeleteHasService' => 'You cannot delete this currency, because a payment service exists using this currency.',
        'cannotDeleteHasChange' => 'You cannot delete this currency, because a balance import change exists using this currency.',
    ],

    /**
     * Product pages.
     */
    'products' => [
        'title' => 'Products',
        'all' => 'All products',
        'select' => 'Select products',
        'search' => 'Search products',
        'clickBuyOrSearch' => 'Click products to buy or search',
        '#products' => '{0} No products|{1} 1 product|[2,*] :count products',
        'noProducts' => 'No products...',
        'searchingFor' => 'Searching for :term...',
        'noProductsFoundFor' => 'No products found for :term',
        'manageProduct' => 'Manage product',
        'manageProducts' => 'Manage products',
        'addProducts' => 'Add products',
        'newProduct' => 'New product',
        'cloneProduct' => 'Clone product',
        'editProduct' => 'Edit product',
        'created' => 'The product has been added.',
        'changed' => 'The product has been changed.',
        'restoreQuestion' => 'You\'re about to restore this product to make it available again. Are you sure you want to continue?',
        'restored' => 'The product has been restored.',
        'deleteQuestion' => 'You\'re about to delete this product. Are you sure you want to continue?',
        'permanentDescription' => 'Tick the checkbox below to permanently delete this product. You will not be able to restore it.',
        'permanentlyDelete' => 'Permanently delete product',
        'deleted' => 'The product has been trashed.',
        'deletedProduct' => 'Deleted product',
        'permanentlyDeleted' => 'The product has been permanently deleted.',
        'namePlaceholder' => 'Fancy Juice',
        'tagsPlaceholder' => 'cola soda',
        'enabledDescription' => 'Enabled, can be bought',
        'prices' => 'Prices',
        'pricesDescription' => 'Configure prices for this product in the fields below for currencies you want to support.',
        'localizedNames' => 'Localized names',
        'localizedNamesDescription' => 'Configure localized names for this product in the fields below if it differs from the main name.',
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
     * Inventory pages.
     */
    'inventories' => [
        'title' => 'Inventories',
        'inventory' => 'Inventory',
        'manage' => 'Manage inventories',
        'noInventories' => 'No inventories...',
        'createInventory' => 'Create inventory',
        'created' => 'The inventory has been created.',
        'namePlaceholder' => 'My inventory',
        'editInventory' => 'Edit inventory',
        'changed' => 'The inventory has been updated.',
        'deleteQuestion' => 'You\'re about to delete this inventory along with all its history. Are you sure you want to continue?',
        'deleted' => 'The inventory has been deleted.',
        'exhaustedProducts' => 'Exhausted products',
        'rebalance' => 'Rebalance',
        'rebalanceProducts' => 'Rebalance products',
        'rebalanceDescription' => 'Count all products in your inventory and enter the quantities in the list below. Enter either the quantity or the change (delta). Enter the same quantity for products as currently known to confirm the inventory is still in balance. Leave fields empty to skip balancing a product.',
        'defaultRebalanceComment' => 'Periodic rebalance by user',
        '#productsRebalanced' => '{0} No products rebalanced|{1} One product rebalanced|[2,*] :count products rebalanced',
        'confirmBalanceComplete' => 'I confirm I want to rebalance these products with the entered quantities',
        'mustBeInteger' => 'Must be an integer.',
        'changeType' => 'Change type',
        'type' => [
            1 => 'Balance',
            2 => 'Move',
            3 => 'Purchase',
            4 => 'Add/remove',
            5 => 'Set',
        ],
    ],

    /**
     * Payment service pages.
     */
    'paymentService' => [
        'title' => 'Payment services',
        'service' => 'Payment service',
        'noServices' => 'No payment services...',
        'manageService' => 'Manage payment service',
        'manageServices' => 'Manage payment services',
        'serviceType' => 'Payment service type',
        'availableTypes#' => '{0} No payment service types available|{1} Available payment service type|[2,*] :count payment service types available',
        'newService' => 'Add service',
        'newChooseType' => 'Please choose the type of payment service you\'d like to configure and add.',
        'editService' => 'Edit service',
        'deleteService' => 'Delete service',
        'created' => 'The payment service has been added.',
        'changed' => 'The payment service has been changed.',
        'restoreQuestion' => 'You\'re about to restore this payment service to make it available again. Are you sure you want to continue?',
        'restored' => 'The payment service has been restored.',
        'deleteQuestion' => 'You\'re about to delete this payment service. Are you sure you want to continue?',
        'permanentDescription' => 'Tick the checkbox below to permanently delete this payment service. You will not be able to restore it.',
        'permanentlyDelete' => 'Permanently delete payment service',
        'deleted' => 'The payment service has been trashed.',
        'permanentlyDeleted' => 'The payment service has been permanently deleted.',
        'enabledDescription' => 'Enabled, can be used',
        'enabledServices#' => '{0} No enabled services|{1} Enabled service|[2,*] :count enabled services',
        'disabledServices#' => '{0} No disabled services|{1} Disabled service|[2,*] :count disabled services',
        'supportDeposit' => 'Support deposits',
        'supportDepositDescription' => 'Enable deposits. Allow users to deposit money to their wallets in this economy.',
        'supportWithdraw' => 'Support withdrawals',
        'supportWithdrawDescription' => 'Enabled withdrawals. Allow users to withdraw money from their wallets in this economy. (Currenty not supported)',
        'backToServices' => 'Back to payment services',
        'viewService' => 'View service',
        'unknownService' => 'Unknown payment service',
        'startedWillUseOldDetails' => 'Payments that have already been initiated might still use the old details, even after changing them here.',
        'startedWillComplete' => 'No new payments will be accepted using this service. However, payments that have already been initiated will still be completed.',
        'amountInCurrency' => 'Amount in :currency',
        'amountToTopUpInCurrency' => 'Amount to top up with in :currency',
        'youSpendAboutEachMonth' => 'You spend :amount each month.',
        'noteTimeAdvance' => '+:time advance',
        'pay' => 'Pay',
        'otherPay' => 'Other amount, pay',
        'selectPaymentServiceToUse' => 'Payment method',
    ],

    /**
     * Balance import system pages.
     */
    'balanceImport' => [
        'title' => 'Balance imports',
        'system' => 'System',
        'systems' => 'Systems',
        'noSystems' => 'No systems...',
        'systems#' => '{0} No systems|{1} System|[2,*] :count systems',
        'manageSystem' => 'Manage system',
        'manageSystems' => 'Manage systems',
        'namePlaceholder' => 'Our paper system',
        'newSystem' => 'Add system',
        'editSystem' => 'Edit system',
        'deleteSystem' => 'Delete system',
        'created' => 'The balance import system has been added.',
        'changed' => 'The balance import system has been changed.',
        'deleteQuestion' => 'You\'re about to delete this balance import system. This will delete all related imports with it. Import changes that have already been committed to wallets will not be reverted. Are you sure you want to continue?',
        'deleted' => 'The balance import system has been deleted.',
        'backToSystems' => 'Back to systems',
        'viewSystem' => 'View system',
        'unknownSystem' => 'Unknown system',
        'exportUserList' => 'Export user list',
        'exportUserListDescription' => 'This lists all email addresses for users of balance imports within this system, that have at least one balance import change which has been committed to a user wallet. This means that only users are listed that have registered, joined a bar in this economy, and have verified their email address. These users automatically receive balance updates from :app.',
    ],

    /**
     * Balance import event pages.
     */
    'balanceImportEvent' => [
        'title' => 'Balance import events',
        'event' => 'Event',
        'events' => 'Events',
        'noEvents' => 'No events...',
        'events#' => '{0} No events|{1} Event|[2,*] :count events',
        'manageEvent' => 'Manage event',
        'manageEvents' => 'Manage events',
        'namePlaceholder' => '2019 January',
        'newEvent' => 'Add event',
        'editEvent' => 'Edit event',
        'deleteEvent' => 'Delete event',
        'created' => 'The balance import event has been added.',
        'changed' => 'The balance import event has been changed.',
        'deleteQuestion' => 'You\'re about to delete this balance import event. This will delete all related imports with it. Are you sure you want to continue?',
        'deleted' => 'The balance import event has been deleted.',
        'cannotDeleteHasChanges' => 'Cannot delete this event, because it has imported changes',
        'backToEvents' => 'Back to events',
        'viewEvent' => 'View event',
        'unknownEvent' => 'Unknown event',
    ],

    /**
     * Balance import change pages.
     */
    'balanceImportChange' => [
        'title' => 'Balance import changes',
        'change' => 'Change',
        'changes' => 'Changes',
        'noChanges' => 'No changes...',
        'approvedChanges' => 'Approved changes',
        'unapprovedChanges' => 'Unapproved changes',
        'noApprovedChanges' => 'No approved changes...',
        'noUnapprovedChanges' => 'No unapproved changes...',
        'changes#' => '{0} No changes|{1} Change|[2,*] :count changes',
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
        'created' => 'The balance import change has been imported.',
        'importedJson' => 'The JSON balance import changes have been imported.',
        'changed' => 'The balance import change has been changed.',
        'approveQuestion' => 'You\'re about to approve this balance import change. This will commit the balance change to the wallet of the user when available. Are you sure you want to continue?',
        'approved' => 'The balance import change has been approved and will be committed to the user in the background.',
        'approveAllQuestion' => 'You\'re about to approve all balance import changes in the \':event\' event. This will commit the balance changes to the wallet of the user when available. Are you sure you want to continue?',
        'approvedAll' => 'The balance import changes have been approved and will be committed to the user in the background.',
        'undoQuestion' => 'You\'re about to undo this balance import change. This will set its state to non-approved, and will revert any committed balance changes in the user\'s wallet. Are you sure you want to continue?',
        'undone' => 'The balance import change has been undone.',
        'deleteQuestion' => 'You\'re about to delete this balance import change. Any mutation in a user wallet as a result of this change that is already committed will not be reverted, and the wallet mutation is then unlinked. Are you sure you want to continue?',
        'deleted' => 'The balance import change has been deleted.',
        'migrateDescription' => 'Migrate this balance import alias to change the user details. When the email address is changed to a verified one of a user, all changes for this alias will be committed to their wallet. Balance import changes for this alias that have already been committed won\'t be transferred to a different user.',
        'migrated' => 'The balance import alias has been migrated.',
        'backToChanges' => 'Back to changes',
        'viewChange' => 'View change',
        'unknownChange' => 'Unknown change',
        'finalBalance' => 'Final balance',
        'jsonData' => 'JSON data',
        'cost' => 'Cost',
        'enterAliasNameEmail' => 'Enter the name and e-mail address of the user you\'re importing the balance for. The e-mail address will be used to automatically link the balance to the wallet of a registered user.',
        'selectCurrency' => 'Select the currency for this import.',
        'balanceOrCostDescription' => 'Enter either the final balance or cost for the user.<br><br>For periodic balance imports, enter the final balance at the time of the import event in the final balance field. On first import, the final balance is fully given to the user. On subsequent imports the difference between the last imported balance and the given final balance is given to the user.<br><br>For a one-time cost import, fill in the cost field to credit the user. Use a negative value to give the user balance. This has no effect on the tracked balance of period imports for this user.',
        'enterBalanceOrCost' => 'Provide either the final balance or cost.',
        'importJsonDescription' => 'Import periodic balance updates from JSON data.<br><br>On first import, the final balance is fully given to the user. On subsequent imports the difference between the last imported balance and the given final balance is given to the user.',
        'importJsonFieldsDescription' => 'Configure the field names used in the JSON data for each user.',
        'importJsonDataDescription' => 'Enter the JSON data. Must be a JSON array with objects, each having the fields as configured above.',
        'hasUnapprovedMustCommit' => 'Some changes are not yet approved, and will not be applied to users until they are.',
        'mustApprovePreviousFirst' => 'You must approve the previous balance import change that has a balance update first.',
        'mustApproveAllPreviousFirst' => 'You must approve the previous balance import changes for the changes you want to approve now that have a balance update first.',
        'cannotApproveWithFollowingApproved' => 'You cannot approve a change, having a later change has already been approved.',
        'cannotDeleteMustUndo' => 'You cannot delete a change that is approved. You must undo it first.',
        'cannotUndoIfNewerApproved' => 'You cannot undo this balance import change, because there\'s a newer balance change for this user that is still accepted.',
    ],

    /**
     * Balance import alias pages.
     */
    'balanceImportAlias' => [
        'newAliasMustProvideName' => 'The given email address is not known yet, you must provide a name.',
        'newJsonAliasMustProvideName' => 'The given email address \':email\' is not known yet, missing name field for this user.',
        'jsonHasDuplicateAlias' => 'The JSON data contains multiple items for \':email\'.',
        'aliasWithEmailAlreadyExists' => 'An alias with this email address already exists.',
        'aliasAlreadyInEvent' => 'The user \':email\' already has a change in this event',
        'allowAddingSameUserMultiple' => 'Allow adding the same user more than once in the current event (Not recommended)',
    ],

    /**
     * Balance import balance update email.
     */
    'balanceImportMailBalance' => [
        'title' => 'Send balance email',
        'description' => 'Send users in this balance import event a balance update email. This may be used to notify users when a new list of balances is imported. A message will only be sent for approved changes in this balance import event. The last known balance of users within this system will always be used. Users with a balance of zero will not receive a message.',
        'mailUnregisteredUsers' => 'Mail unregistered users (no account)',
        'mailNotJoinedUsers' => 'Mail not-joined users (with account, not a bar member)',
        'mailJoinedUsers' => 'Mail joined users (with account, and a bar member)',
        'extraMessage' => 'Extra message',
        'relatedBar' => 'Related bar',
        'noRelatedBar' => 'No related bar',
        'mustSelectBarToInvite' => 'You must select a bar to invite users',
        'inviteToJoinBar' => 'Invite unregistered users to join the bar',
        'limitToLastEvent' => 'Limit to users and members in the last event',
        'replyToAddress' => '\'Reply-To\' email address',
        'confirmSendMessage' => 'Confirm to send an email message',
        'sentBalanceUpdateEmail' => 'A balance update mail will now be sent to the selected balance import users. It may take a few minutes for it to arrive.',
    ],

    /**
     * Economy finance pages.
     */
    'finance' => [
        'title' => 'Financial report',
        'walletSum' => 'Cumulative balance',
        'paymentsInProgress' => 'In progress',
        'noAccountImport' => 'No account (import)',
        'membersWithNonZeroBalance' => 'Members with balance',
        'description' => 'This shows a simple financial report for the current economy state. Users from balance imports, that have not registered and joined this economy, are currently not listed here.',
    ],

    /**
     * bunq account pages.
     */
    'bunqAccounts' => [
        'title' => 'bunq accounts',
        'bunqAccount' => 'bunq account',
        'description' => 'Click on one of your bunq accounts to manage it, or add a new one.',
        'noAccounts' => 'You don\'t have any bunq accounts added yet...',
        'addAccount' => 'Add bunq account',
        'addAccountDescription' => 'This page allows you to add a bunq account for automatic payment processing.<br><br>Create an API token and an empty monetary account in the bunq app. Enter the token and the IBAN of the monetary account below.<br><br>Would you like to use a test account instead?',
        'createSandboxAccount' => 'Create bunq sandbox account',
        'descriptionPlaceholder' => 'bunq account for automating bar payments',
        'tokenDescription' => 'Create a new API key in the developer section of the bunq app on your phone, and enter the newly created token in this field. This token must never be shared with anyone else.',
        'ibanDescription' => 'Enter the IBAN of a monetary account in your bunq profile. This monetary account will be dedicated to payment processing and cannot be used for anything else. It is recommended to create a new monetary account through the bunq application for this.',
        'invalidApiToken' => 'Invalid API token',
        'addConfirm' => 'By adding this bunq account, you give :app full control over the monitary account assigned to the specified IBAN. That account will be dedicated to automated payment processing, until this link between :app and bunq is dropped. Never drop this link through the mobile bunq app by deleting the API key, but drop it through :app to ensure any ongoing payments can be finished properly. The account must have a current balance of €0.00. You cannot use this monetary account for any other payments, applications or :app instances, and you might risk serious money-flow issues if you do so. :app is not responsible for any damage caused by linking your bunq account to this application.',
        'createSandboxConfirm' => 'This will create a bunq sandbox account for testing purposes. Please be aware that this will allow anyone to top-up their :app wallet without paying real money. :app is not responsible for any damage caused by linking your bunq account to this application.',
        'mustEnterBunqIban' => 'You must enter a bunq IBAN',
        'accountAlreadyUsed' => 'This monetary is already used',
        'noAccountWithIban' => 'No active monetary account with this IBAN',
        'onlyEuroSupported' => 'Only accounts using EURO currency are supported',
        'notZeroBalance' => 'Account does not have a balance of €0.00, create a new monitary account',
        'added' => 'The bunq account has been added.',
        'changed' => 'The bunq account has been changed.',
        'paymentsEnabled' => 'Payments enabled',
        'checksEnabled' => 'Checks enabled',
        'enablePayments' => 'Enable payments: allow usage for new payments',
        'enableChecks' => 'Enable checks: check periodically for newly received payments',
        'confirm' => 'I agree with this and meet the requirements',
        'environment' => 'bunq API environment',
        'runHousekeeping' => 'Run housekeeping',
        'runHousekeepingSuccess' => 'The monetary bunq account has been reconfigured and any pending payments are now queued for processing.',
        'noHttpsNoCallbacks' => 'This site does not use HTTPS, real time bunq payments are not supported. Payment events will be processed daily.',
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
        'description' => 'Click on one of your wallets to manage it, or create a new one.',
        'walletEconomies' => 'Wallet economies',
        'myWallets' => 'My wallets',
        '#wallets' => '{0} No wallets|{1} 1 wallet|[2,*] :count wallets',
        'economySelectDescription' => 'Wallets in this community are divided by economy.<br>Select the economy to manage your wallets in it.',
        'noWallets' => 'No wallets...',
        'namePlaceholder' => 'My personal wallet',
        'nameDefault' => 'My wallet',
        'createWallet' => 'Create wallet',
        'walletCreated' => 'The wallet has been created.',
        'walletUpdated' => 'Wallet changes saved.',
        'deleteQuestion' => 'You\'re about to delete this wallet. Are you sure you want to continue?',
        'cannotDeleteNonZeroBalance' => 'To delete this wallet, it must have a balance of exactly :zero.',
        'walletDeleted' => 'The wallet has been deleted.',
        'cannotCreateNoCurrencies' => 'You can\'t create wallet at this moment. The community administrator did not configure a currency which allows this.',
        'all' => 'All wallets',
        'view' => 'View wallet',
        'noWalletsToMerge' => 'You do not have any wallets you can merge.',
        'mergeWallets' => 'Merge wallets',
        'mergeDescription' => 'Select the wallets you\'d like to merge for each currency.',
        'mustSelectTwoToMerge' => 'Select at least two :currency wallets to merge.',
        'mergedWallets#' => '{0} Merged no wallets|{1} Merged one wallet|[2,*] Merged :count wallets',
        'transfer' => 'Transfer',
        'transferToSelf' => 'Transfer to wallet',
        'transferToUser' => 'Transfer to user',
        'toSelf' => 'To wallet',
        'toUser' => 'To user',
        'topUp' => 'Top up wallet',
        'topUpNow' => 'Top up now',
        'noWalletToTopUp' => 'You don\'t have a wallet here to top up.',
        'modifyBalance' => 'Modify balance',
        'modifyMethod' => 'Modify method',
        'modifyMethodDeposit' => 'Deposit (add)',
        'modifyMethodWithdraw' => 'Withdraw (subtract)',
        'modifyMethodSet' => 'Set to balance',
        'modifyBalanceWarning' => 'This changes the wallet balance, but no real money is exchanged through :app. This change is logged and will be visible to the user.',
        'confirmModifyBalance' => 'Confirm balance modification',
        'balanceModified' => 'The wallet balance has been modified.',
        'successfullyTransferredAmount' => 'Successfully transfered :amount to :wallet',
        'backToWallet' => 'Back to wallet',
        'walletTransactions' => 'Wallet transactions',
        'noServiceConfiguredCannotTopUp' => 'You cannot top-up your wallet through :app. The bar or community administrator has not configured any payment method. Please ask at the bar for further information.',
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
        'products' => 'Products',
        'uniqueProducts' => 'Unique products',
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
            'title' => 'Purchased products',
            'chartName' => 'Product distribution chart',
        ],
        'smartText' => [
            'main' => 'In the :period you were active on <b>:active-days</b>:best-day. During this period you bought <b>:products</b>:products-unique.',
            'mainDays' => '{0} no days|{1} one day|[2,*] :count different days',
            'mainBestDay' => ' of which <b>:day</b> was your best day',
            'mainUniqueProducts' => ', of which :unique unique',
            'productCount' => '{0} no products|{1} one product|[2,*] :count products',
            'productUniqueCount' => '{0} none|{1} <b>one</b> was|[2,*] <b>:count</b> were',
            'partBestProduct' => 'You bought <b>:product</b> the most:extra.',
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
        'selectProductsToUndo' => 'Select products to undo',
        'noProductsSelected' => 'No products selected',
        'undoQuestion' => 'You\'re about to undo this transaction. Are you sure you want to continue?',
        'viewTransaction' => 'View transaction',
        'linkedTransaction' => 'Linked transaction',
        'state' => [
            'pending' => 'Pending',
            'processing' => 'Processing',
            'success' => 'Completed',
            'failed' => 'Failed',
        ],
        'descriptions' => [
            'balanceImport' => 'Import external balance',
            'fromWalletToProduct' => 'Purchased product(s)',
            'toProduct' => 'Purchased product(s)',
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
            'pending' => 'Pending',
            'processing' => 'Processing',
            'success' => 'Completed',
            'failed' => 'Failed',
        ],
        'types' => [
            'magic' => 'Special mutation',
            'walletTo' => 'Deposit to wallet',
            'walletFrom' => 'Paid with wallet',
            'walletToDetail' => 'Deposit to :wallet',
            'walletFromDetail' => 'Paid with :wallet',
            'productTo' => 'Paid for product(s)',
            'productFrom' => 'Received money for product(s)',
            'productToDetail' => 'Paid for :products',
            'productFromDetail' => 'Received money for :products',
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
        'description' => 'This shows all your notifications, both new and read.',
        'unread#' => '{0} No unread notifications|{1} Unread notification|[2,*] :count unread notifications',
        'persistent#' => '{0} No persistent notifications|{1} Persistent notification|[2,*] :count persistent notifications',
        'read#' => '{0} No read notifications|{1} Read notification|[2,*] :count read notifications',
        'noNotifications' => 'No notifications...',
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
        'description' => 'This shows all in progress and settled payments you\'ve made in any community.',
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
        'noPayments' => 'You have not made any payments yet',
        'viewPayment' => 'View payment',
        'unknownPayment' => 'Unknown payment',
        'handlePayments' => 'Review payments',
        'handleCommunityPayments' => 'Review community payments',
        'paymentsToApprove' => 'Payments awaiting action',
        'paymentsWaitingForAction' => 'Some payments are waiting for action by a community manager, please review these as soon as possible.',
        'paymentsToApproveDescription' => 'The following user payments are waiting for action by a community manager. Please go through these as soon as possible to minimize payment times.',
        'paymentRequiresCommunityAction' => 'This payment awaits action by a community manager. If you don\'t have access to the receiving bank account, do not take action and leave this check to a group manager who does have access.',
        'cancelPayment' => 'Cancel payment',
        'cancelPaymentQuestion' => 'You\'re about to cancel this payment. Never cancel a payment for which you\'ve already transfered money, or your transfer might be lost. Are you sure you want to continue?',
        'paymentCancelled' => 'Payment cancelled',
        'state' => [
            'init' => 'Initiated',
            'pendingUser' => 'Pending user action',
            'pendingCommunity' => 'Pending review',
            'pendingAuto' => 'Pending (automatic)',
            'processing' => 'Processing',
            'completed' => 'Completed',
            'revoked' => 'Revoked',
            'rejected' => 'Rejected',
            'failed' => 'Failed',
        ],
    ],

    /**
     * Bar pages.
     */
    'bar' => [
        'yourBars' => 'Your bars',
        'noBars' => 'No bars available...',
        'searchByCommunity' => 'Search by community',
        'searchByCommunityDescription' => 'It\'s usually easier to find a specific bar by it\'s community.',

        // TODO: remove duplicates
        'createBar' => 'Create bar',
        'editBar' => 'Edit bar',
        'deleteBar' => 'Delete bar',
        'join' => 'Join',
        'yesJoin' => 'Yes, join',
        'joined' => 'Joined',
        'youAreJoined' => 'You joined this bar.',
        'notJoined' => 'Not joined',
        'leave' => 'Leave',

        'hintJoin' => 'You aren\'t part of this bar yet.',
        'joinQuestion' => 'Would you like to join this bar?',
        'alsoJoinCommunity' => 'Also join their community',
        'alreadyJoinedTheirCommunity' => 'You already are a member of their community',
        'joinedThisBar' => 'You\'ve joined this bar.',
        'cannotSelfEnroll' => 'You cannot join this bar yourself, it is disabled.',
        'leaveQuestion' => 'Are you sure you want to leave this bar?',
        'leftThisBar' => 'You left this bar.',
        'cannotLeaveHasWallets' => 'You cannot leave this bar while you have a wallet in it.',
        'protectedByCode' => 'This bar is protected by a passcode. Request it at the bar, or scan the bar QR code if available.',
        'incorrectCode' => 'Incorrect bar code.',
        'namePlaceholder' => 'Viking bar',
        'descriptionPlaceholder' => 'Welcome to the Viking bar!',
        'slugDescription' => 'A slug allows you to create an easy to remember URL to access this bar, by defining a short keyword.',
        'slugDescriptionExample' => 'This could simplify your bar URL:',
        'slugPlaceholder' => 'viking',
        'slugFieldRegexError' => 'The slug must start with an alphabetical character.',
        'codeDescription' => 'With a bar code, you prevent random users from joining. To join the bar, users are required to enter the specified code.',
        'economyDescription' => 'The economy defines what products, currencies and wallets are used in this bar. Be very careful with changing it after the bar is created, as this immediately affects the list of products, currencies and wallets used in this bar. Users probably don\'t expect this, and might find it hard to understand.',
        'inventoryDescription' => 'Select an inventory to automatically keep track of inventory when products are sold.',
        'selectInventoryAfterCreate' => 'You may select the inventory after the bar is created.',
        'showExploreDescription' => 'List on public \'Explore bars\' page',
        'showCommunityDescription' => 'List on community page for community members',
        'selfEnrollDescription' => 'Allow self enrollment (with code if specified)',
        'joinAfterCreate' => 'Join the bar after creating.',
        'created' => 'The bar has been created.',
        'updated' => 'The bar has been updated.',
        'mustCreateEconomyFirst' => 'To create a bar, you must create an economy first.',
        'backToBar' => 'Back to bar',
        'quickBuy' => 'Buy now',
        'boughtProductForPrice' => 'Bought :product for :price',
        'noDescription' => 'This bar has no description',
        'manageBar' => 'Manage bar',
        'barInfo' => 'Bar info',
        'viewBar' => 'View bar',
        'deleted' => 'The bar has been deleted.',
        'deleteQuestion' => 'You\'re about to permanently delete this bar. All members including yourself will lose access to it, and won\'t be possible to link product transactions to it anymore. The products and user wallets will remain as part of the economy that was used in this bar. Are you sure you want to continue?',
        'exactBarNameVerify' => 'Exact name of bar to delete (Verification)',
        'incorrectNameShouldBe' => 'Incorrect name, should be: \':name\'',
        'kioskManagement' => 'Kiosk management',
        'startKiosk' => 'Start kiosk',
        'startKioskDescription' => 'Here you can start kiosk mode. When you start kiosk mode, you will be logged out here from your personal account, and a central terminal interface will be started for this bar which everybody can use to purchase products. This mode will be active until you manually turn it off by logging out once more on this device.',
        'startKioskConfirm' => 'Confirm to enter kiosk mode',
        'startKioskConfirmDescription' => 'Entering kiosk mode will allow any user having access to this machine to purchase products on anybodies behalf.',
        'kioskSessions' => 'Kiosk sessions',
        'kioskSessionsDescription' => 'This page shows the active and terminated kiosk sessions for this bar. Click on an active session to see details or to terminate it. Terminated sessions are automatically forgotten after a while.',
        'expireAllKioskSessionsQuestion' => 'Are you sure you want to terminate all kiosk sessions? This will log out all kiosks for this bar.',
        'generatePoster' => 'Bar poster',
        'generatePosterDescription' => 'Create a poster for this bar to hang on a wall. Visitors will then be able to easily use :app and join this bar by scanning a QR code with their mobile phone.',
        'showCodeOnPoster' => 'Show code to join this bar on the poster',
        'lowBalanceText' => 'Negative balance text',
        'lowBalanceTextPlaceholder' => 'You currently have a negative balance. Please top-up your wallet now before buying new products.',
        'allPurchases' => 'All purchases',
        'purchases' => 'Purchases',
        'purchasesDescription' => 'This page shows a history of all purchased products in this bar.',
        'exportPurchasesTitle' => 'Export purchases',
        'exportPurchasesDescription' => 'This page allows you to export all purchases made in this bar to a file.',
        'noPurchases' => 'No purchases',
        'poster' => [
            'thisBarUses' => 'This bar uses',
            'toDigitallyManage' => 'to digitally manage payments and inventory for consumptions',
            'scanQr' => 'scan the QR code with your phone to join and make a purchase',
            'orVisit' => 'Or visit',
        ],
        'buy' => [
            'forMe' => 'Buy myself',
            'forOthers' => 'For others/more',
        ],
        'advancedBuy' => [
            'tapProducts' => 'Select products to buy for any user.',
            'tapUsers' => 'Tap users to add the selected products in cart for.',
            'tapBuy' => 'Tap the blue buy button to commit the purchase.',
            'addToCartFor' => 'Add selected to cart for',
            'searchUsers' => 'Search users',
            'searchingFor' => 'Searching for :term',
            'noUsersFoundFor' => 'No users found for :term',
            'inCart' => 'In cart',
            'buyProducts#' => '{0} Buy no products|{1} Buy product|[2,*] Buy :count products',
            'buyProductsUsers#' => '{0} Buy no products for :users users|{1} Buy product for :users users|[2,*] Buy :count products for :users users',
            'pressToConfirm' => 'Tap again to confirm',
            'boughtProducts#' => '{0} Bought no products.|{1} Bought product.|[2,*] Bought :count products.',
            'boughtProductsUsers#' => '{0} Bought no products for :users users.|{1} Bought product for :users users.|[2,*] Bought :count products for :users users.',
            'pageCloseWarning' => 'You have selected products or have products in cart that have not been bought yet. You must add a product selection to at least one user and tap the Buy button to commit the purchase, or the selection will be lost.',
        ],
        'links' => [
            'title' => 'Useful links',
            'description' => 'This page lists various shareable links for this bar. You may share these through email or print them on a poster. Some of these links allow you to direct users to specific otherwise hidden pages and intents.<br><br>Please be aware that some links change when modifying bar settings, and some links contain secret bits.',
            'linkBar' => 'Main bar page',
            'linkBarAction' => 'Visit :bar',
            'linkJoinBar' => 'Invite new user to join bar',
            'linkJoinBarAction' => 'Join :bar',
            'linkJoinBarCode' => 'Invite new user to join bar (with code)',
            'linkJoinBarCodeAction' => 'Join :bar',
            'linkQuickWallet' => 'Show main personal wallet',
            'linkQuickWalletAction' => 'View your personal wallet',
            'linkQuickTopUp' => 'Top-up main personal wallet',
            'linkQuickTopUpAction' => 'Top-up your wallet',
            'linkVerifyEmail' => 'Verify email addresses',
            'linkVerifyEmailAction' => 'Verify your email address',
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
        'updated' => 'Your settings have been saved.',
        'visibility' => 'Visibility',
        'visibilityDescription' => 'Below you can configure your visibility as bar member. Disabling visibility is useful if you want to prevent other users from buying products on your behalf. It is recommended to keep all toggles on.<br><br>Visibility in buy screens specifies whether you are shown in the list of users when buying products using their phone. Visibility in kiosk specifies whether you are shown in the list of users on a central bar kiosk device.',
    ],

    /**
     * Kiosk page.
     */
    'kiosk' => [
        'loading' => 'Loading Kiosk',
        'selectUser' => 'Select member',
        'searchUsers' => 'Search users',
        'searchingFor' => 'Searching :term...',
        'noUsersFoundFor' => 'Nobody for :term',
        'firstSelectUser' => 'Select a member on the left to make a purchase for',
        'selectProducts' => 'Select products',
        'buyProducts#' => '{0} Buy no products|{1} Buy product|[2,*] Buy :count products',
        'buyProductsUsers#' => '{0} Buy no products for :users users|[1,*] Buy :count× for :users users',
        'deselect' => 'Deselect',
        'backToKiosk' => 'Back to kiosk',
        'noConnectionBanner' => 'No connection. Please ensure this device has an active internet connection before making any purchases. Pull the page down to refresh.',
    ],

    /**
     * Kiosk join pages.
     */
    'kioskJoin' => [
        'title' => 'Add user / join',
        'joinBar' => 'Join :bar',
        'description' => 'New users can add themselves to this bar by registering an account.',
        'scanQr' => 'Scan the QR code below with your phone to start:',
        'orUrl' => 'Or visit the following link in your browser:',
    ],

    /**
     * Community/bar statistics pages.
     */
    'stats' => [
        'title' => 'Statistics',
        'barStats' => 'Bar statistics',
        'communityStats' => 'Community statistics',
        'activePastHour' => 'Active past hour',
        'activePastDay' => 'Active past day',
        'activePastWeek' => 'Active past week',
        'activePastMonth' => 'Active past month',
        'productsPastHour' => 'Products past hour',
        'productsPastDay' => 'Products past day',
        'productsPastWeek' => 'Products past week',
        'productsPastMonth' => 'Products past month',
    ],

    /**
     * Bar member pages.
     */
    'barMembers' => [
        'title' => 'Bar members',
        'description' => 'This page shows an overview of all bar members.<br>Clicking on a member allows you to remove the member, or change it\'s role.',
        'nickname' => 'Display name',
        'nicknameDescription' => 'You can set a custom display name for your account. With a display name set, your full name will be hidden and your custom name will be shown in buy and kiosk screens. This intended for special users/accounts where showing their own name doesn\'t make sense. To prevent confusion set a clear and descriptive name or better yet, don\'t set a name at all.',
        'noMembers' => 'This bar has no members...',
        'memberSince' => 'Member since',
        'lastVisit' => 'Last visit',
        'deleteQuestion' => 'You\'re about to remove this member from this bar. Are you sure you want to continue?',
        'memberRemoved' => 'The member has been removed.',
        'memberUpdated' => 'Member changes saved.',
        'incorrectMemberRoleWarning' => 'Assigning an incorrect role that is too permissive to a member may introduce significant security issues.',
        'ownRoleDowngradeWarning' => 'By downgrading your own role you might lose management access to this bar. Be very careful.',
        'confirmRoleChange' => 'Confirm role change for bar member',
        'confirmSelfDelete' => 'Confirm to kick yourself as bar member, you will lose your role',
        'cannotDemoteLastManager' => 'You cannot demote the last bar member with this (or a more permissive) management role.',
        'cannotEditMorePermissive' => 'You cannot edit a bar member with a more permissive role than yourself.',
        'cannotSetMorePermissive' => 'You cannot set a more permissive role for a bar member than your current role.',
        'cannotDeleteLastManager' => 'You cannot kick the last bar member with this (or a more permissive) management role.',
    ],

    /**
     * Verify email address page.
     */
    'verifyEmail' => [
        'title' => 'Verify email address',
        'description' => 'Please enter the verification token of the email address you\'d like to verify.<br>'
            . 'This token can be found at the bottom of the verification email you\'ve received in your mailbox.',
        'invalid' => 'Unknown token. Maybe the e-mail address is already verified, or the token has expired.',
        'expired' => 'The token has expired. Please request a new verification email.',
        'alreadyVerified' => 'This email address has already been verified.',
        'verified' => 'You\'re all set! Your email has been verified.',
    ],

    /**
     * Password request sent page.
     */
    'passwordRequestSent' => [
        'title' => 'Check your mailbox',
        'message' => 'If the email address you\'ve submitted is known by our system, we\'ve sent instructions for resetting your password to your mailbox.<br><br>'
            . 'Please note that these instructions would only be valid for <b>:hours hours</b>.<br><br>'
            . 'You may close this webpage now.',
    ],

    /**
     * Password disable page.
     */
    'passwordDisable' => [
        'title' => 'Disable password',
        'description' => 'Enter your current password in the field bellow, to disable using a password for logging in in the future. You\'ll still be able to login using a link sent to your email inbox.',
    ],

    /**
     * Password reset page.
     */
    'passwordReset' => [
        'enterResetToken' => 'Please enter the password reset token. '
            . 'This token can be found in the email message you\'ve received with password reset instructions.',
        'enterNewPassword' => 'Please enter the new password you\'d like to use from now on.',
        'invalid' => 'Unknown token. The token might have been expired.',
        'expired' => 'The token has expired. Please request a new password reset.',
        'used' => 'Your password has already been changed using this token.',
        'changed' => 'As good as new! Your password has been changed.',
    ],

    /**
     * Privacy policy page.
     */
    'privacy' => [
        'title' => 'Privacy',
        'description' => 'When you use our service, you\'re trusting us with your information. We understand this is a big responsibility.<br><br>The Privacy Policy below is meant to help you understand how we manage your information.',
        'questions' => 'Questions?',
        'questionsDescription' => 'If you have any further questions about our Privacy Policy or your privacy when using our service, be sure to get in touch with us.',
    ],

    /**
     * Terms of Service page.
     */
    'terms' => [
        'title' => 'Terms',
        'description' => 'When you use our service, your\'re agreeing with our Terms of Service as shown below.',
        'questions' => 'Questions?',
        'questionsDescription' => 'If you have any further questions about our Terms of Service, be sure to get in touch with us.',
    ],

    /**
     * License page.
     */
    'license' => [
        'title' => 'License',
        'description' => 'The Barbapappa software project is open-source, and is released under the GNU AGPL-3.0 license. This license describes what you are and are not allowed to with the public source code of this software project. This license does not have any effect on the usage information processed within this application.<br><br>Read the full license below, or check out the summary for this license as quick summary.',
        'questions' => 'Questions?',
        'questionsDescription' => 'If you have any further questions about the license used for this project, be sure to get in touch with us. You can also check out the plain text license readable on any device.',
        'plainTextLicense' => 'Plain text license',
        'licenseSummary' => 'License summary',
    ],

    /**
     * Contact page.
     */
    'contact' => [
        'title' => 'Contact',
        'contactUs' => 'Contact us',
        'description' => 'Use the following details to contact Barbapappa:',
        'issuesDescription' => 'This application is open-source, and its development process is open. You can view the source code and issue list at the links below.',
        'issueList' => 'Issue list',
        'newIssueMail' => 'Report issue',
        'thisAppIsOpenSource' => 'This application is Open Source',
    ],

    /**
     * No permission page.
     */
    'noPermission' => [
        'title' => 'You shouldn\'t be here...',
        'description' => 'You took a wrong turn.<br />You don\'t have enough permission to access this content.',
        'notLoggedIn' => 'Not logged in',
        'notLoggedInDescription' => 'You\'re currently not logged in. You may want to login to get proper access rights.',
    ],

    /**
     * About page.
     */
    'about' => [
        'title' => 'About',
        'aboutUs' => 'About us',
        'description' => ':app is a digital bar management application to facilitate a user-controlled platform for purchase processing, payment handling and inventory management.<br><br>:app is a fully automated solution for small self-managed bars and communities, to take away the hassle of manually registering purcahses using tally marks on paper.<br><br>For any interest in using this platform for your own community, be sure to send us a message!',
        'developedBy' => 'The project is developed & maintained by',
        'sourceDescription' => 'I make the software that I develop open-source. The complete project with its source code is available free of charge, for everyone. I believe it is important to allow anybody to inspect, modify, improve, contribute, and verify without restrictions.',
        'sourceAt' => 'The latest source code can be found on GitLab',
        'withLicense' => 'It is released with the following license',
        'usedTechnologies' => 'Some awesome technologies that are used include',
        'noteLaravel' => 'backend framework',
        'noteSemanticUi' => 'frontend theming framework',
        'noteGlyphicons' => 'icons & symbols',
        'noteFlags' => 'flags',
        'noteGetTerms' => 'terms & privacy policy template',
        'noteEDegen' => 'suggested \'Barbapappa\'',
        'otherResources' => 'Other awesome resources include',
        'donate' => 'A lot of effort went into this project.<br>Want to donate me a coffee?',
        'thanks' => 'Thank you for using this product.<br>Thank you for being awesome.',
        'copyright' => 'Copyright © :app :year.<br>All rights reserved.',
    ],

    /**
     * Error pages.
     */
    'errors' => [
        // TODO: move noPermission view into this
        '401' => [
            'title' => '401 Unauthorized',
            'description' => 'You took a wrong turn.<br />You don\'t have access to the page you\'re looking for.',
        ],
        '403' => [
            'title' => '403 Forbidden',
            'description' => 'You took a wrong turn.<br />You don\'t have access to the page you\'re looking for.',
        ],
        '404' => [
            'title' => '404 Not Found',
            'description' => 'You took a wrong turn.<br />The page you\'re looking for does not exist.',
        ],
        '419' => [
            'title' => '419 Page Expired',
            'description' => 'Whoops! This page has expired.',
        ],
        '429' => [
            'title' => '429 Too Many Requests',
            'description' => 'Whoops! Too many requests have made to this page recently on this network. Please wait some time before trying again.',
        ],
        '500' => [
            'title' => '500 Server Error',
            'description' => '<i>Houston, we have a problem!</i><br><br>An error occurred on our end. The administrators have been notified and are looking into it.',
        ],
        '503' => [
            'title' => '503 Service Unavailable',
            'description' => '<i>Houston, we have a problem!</i><br><br>An error occurred on our end, which results in us not being able to serve your request. The administrators have been notified and are looking into it.',
        ],
    ]
];
