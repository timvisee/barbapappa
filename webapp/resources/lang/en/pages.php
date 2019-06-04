<?php

/**
 * Pages and their names.
 */
return [
    'pages' => 'Pages',
    'index' => 'Home page',
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
    'about' => 'About',
    'contact' => 'Contact',
    'contactUs' => 'Contact us',

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
        'title' => 'Visit last',
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
            'yourEmails' => 'Your email addresses',
            'resendVerify' => 'Resend verification',
            'verifySent' => 'A new verification email will be sent shortly.',
            'alreadyVerified' => 'This email address has already been verified.',
            'cannotDeleteMustHaveOne' => 'You cannot delete this email address, you must have at least one address.',
            'cannotDeleteMustHaveVerified' => 'You cannot delete this email address, you must have at least one verified address.',
            'deleted' => 'The email address has been deleted.',
            'deleteQuestion' => 'Are you sure you want to delete this email address?',
        ],
        'addEmail' => [
            'title' => 'Add email address',
            'description' => 'Fill in the email address you\'d like to add.',
            'added' => 'Email address added. A verification email has been sent.',
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
    ],

    /**
     * Community pages.
     */
    'community' => [
        'yourCommunities' => 'Your communities',
        'noCommunities' => 'No communities available...',
        'viewCommunity' => 'View community',
        'viewCommunities' => 'View communities',
        'createCommunity' => 'Create community',
        'editCommunity' => 'Edit community',
        'deleteCommunity' => 'Delete community',
        'join' => 'Join',
        'yesJoin' => 'Yes, join',
        'joined' => 'Joined',
        'notJoined' => 'Not joined',
        'hintJoin' => 'You aren\'t part of this community yet.',
        'joinedClickToLeave' => 'Click to leave.',
        'joinQuestion' => 'Would you like to join this community?',
        'joinedThisCommunity' => 'You\'ve joined this community.',
        'cannotSelfEnroll' => 'You cannot join this community yourself, it is disabled.',
        'leaveQuestion' => 'Are you sure you want to leave this community?',
        'leftThisCommunity' => 'You left this community.',
        'protectedByCode' => 'This community is protected by a passcode. Request it at the community, or scan the community QR code if available.',
        'protectedByCodeFilled' => 'This community is protected by a passcode. We\'ve filled it in for you.',
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
        'economyCreated' => 'The economy has been created. Please add and configure a currency now.',
        'deleteQuestion' => 'You\'re about to delete this economy from this community. Are you sure you want to continue?',
        'deleteBlocked' => 'You\'re about to permanently delete this economy. You must delete the entities listed below first before you can continue with deleting this economy.',
        'cannotDeleteDependents' => 'This economy cannot be deleted, as entities are depending on it which cannot just be deleted.',
        'economyDeleted' => 'The economy has been removed.',
        'economyUpdated' => 'Economy changes saved.',
        'namePlaceholder' => 'Main economy',
        'backToEconomy' => 'Back to economy',
        'backToEconomies' => 'Back to economies',
        'inEconomy' => 'in economy',
    ],

    /**
     * Community economy currency pages.
     */
    'currencies' => [
        'title' => 'Enabled currencies',
        'description' => 'This page shows an overview of the enabled currencies in the economy.<br>At least one currency must be enabled to use this economy for a bar.<br>Add a new currency, or click on one to manage it.',
        'change' => 'Change currency',
        'noCurrencies' => 'This economy has no enabled currencies...',
        'createCurrency' => 'Add currency',
        'currencyCreated' => 'The currency has been added to the economy.',
        'deleteQuestion' => 'You\'re about to remove this currency from this economy. Are you sure you want to continue?',
        'deleteVoidNotice' => 'When you remove this currency, all configured product prices for this currency will be voided in bars that use this economy.<br>You might want to disable this currency instead by changing it, which allows you to enable it again at a later time without having to reenter all prices again.',
        'currencyDeleted' => 'The currency has been removed.',
        'currencyUpdated' => 'Currency changes saved.',
        'enabledTitle' => 'Enable currency',
        'enabledDescription' => 'Specify whether this currency is enabled in bars that are using this economy. If disabled, bar members won\'t be able to purchase products with this currency, and must use a different currency if available until it is enabled again.',
        'changeCurrencyTitle' => 'Change currency?',
        'changeCurrencyDescription' => 'The currency can\'t be changed directly. To change the currency, you must remove this configuration and add a new one to this economy.',
        'allowWallets' => 'Allow wallet creation',
        'allowWalletsDescription' => 'With this option you can specify whether bar members can create a new personal wallet for this currency. Existing wallets will always be kept.',
        'noCurrenciesToAdd' => 'There are no currencies you can add. Ask the site administrator to configure a currency.',
        'noMoreCurrenciesToAdd' => 'There are no other currencies you can add.',
        'manage' => 'Manage currencies',
    ],

    /**
     * Product pages.
     */
    'products' => [
        'title' => 'Products',
        'all' => 'All products',
        'search' => 'Search products',
        'noProducts' => 'No products...',
        'manageProduct' => 'Manage product',
        'manageProducts' => 'Manage products',
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
        'permanentlyDeleted' => 'The product has been permanently deleted.',
        'namePlaceholder' => 'Fancy Juice',
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
        'supportWithdrawDescription' => 'Enabled withdrawals. Allow users to withdraw money from their wallets in this economy.',
        'backToServices' => 'Back to payment services',
        'viewService' => 'View service',
        'unknownService' => 'Unknown payment service',
        'startedWillUseOldDetails' => 'Payments that have already been initiated might still use the old details, even after changing them here.',
        'startedWillComplete' => 'No new payments will be accepted using this service. However, payments that have already been initiated will still be completed.',
        'amountToTopUpInCurrency' => 'Amount to top-up with in :currency',
        'selectPaymentServiceToUse' => 'Payment method',
    ],

    /**
     * Wallet pages.
     */
    'wallets' => [
        'title' => 'Wallets',
        'description' => 'Click on one of your wallets to manage it, or create a new one.',
        'walletEconomies' => 'Wallet economies',
        'yourWallets' => 'Your wallets',
        '#wallets' => '{0} No wallets|{1} 1 wallet|[2,*] :count wallets',
        'economySelectDescription' => 'Wallets in this community are divided by economy.<br>Select the economy to manage your wallets in it.',
        'noWallets' => 'You don\'t have any wallets yet...',
        'namePlaceholder' => 'My personal wallet',
        'nameDefault' => 'My new wallet',
        'createWallet' => 'Create wallet',
        'walletCreated' => 'The wallet has been created.',
        'walletUpdated' => 'Wallet changes saved.',
        'deleteQuestion' => 'You\'re about to delete this wallet. Are you sure you want to continue?',
        'cannotDeleteNonZeroBalance' => 'To delete this wallet, it must have a balance of exactly :zero.',
        'walletDeleted' => 'The wallet has been deleted.',
        'cannotCreateNoCurrencies' => 'You can\'t create wallet at this moment. The community administrator did not configure a currency which allows this.',
        'all' => 'All wallets',
        'view' => 'View wallet',
        'transfer' => 'Transfer',
        'transferToSelf' => 'Transfer to wallet',
        'transferToUser' => 'Transfer to user',
        'toSelf' => 'To wallet',
        'toUser' => 'To user',
        'topUp' => 'Top-up wallet',
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
        'undoQuestion' => 'You\'re about to undo this transaction. Are you sure you want to continue?',
        'state' => [
            'pending' => 'Pending',
            'processing' => 'Processing',
            'success' => 'Completed',
            'failed' => 'Failed',
        ],
        'descriptions' => [
            'fromWalletToProduct' => 'Payment for product(s) with wallet',
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
        'state' => [
            'pending' => 'Pending',
            'processing' => 'Processing',
            'success' => 'Completed',
            'failed' => 'Failed',
        ],
        'types' => [
            'magic' => 'Special mutation',
            'walletTo' => 'Deposit to wallet',
            'walletFrom' => 'Payed with wallet',
            'walletToDetail' => 'Deposit to :wallet',
            'walletFromDetail' => 'Payed with :wallet',
            'productTo' => 'Payed for product(s)',
            'productFrom' => 'Received money for product(s)',
            'productToDetail' => 'Payed for :products',
            'productFromDetail' => 'Received money for :products',
            'paymentTo' => 'Withdrawal to external account',
            'paymentFrom' => 'Deposit from external account',
        ],
    ],

    /**
     * Payment pages.
     */
    'payments' => [
        'title' => 'Payments',
        'details' => 'Payment details',
        'payment' => 'Payment progress',
        'last#' => '{0} Last payments|{1} Last payment|[2,*] Last :count payments',
        'backToPayment' => 'Back to payment',
        'inProgress' => 'Payment in progress',
        'inProgressDescription' => 'This payment is still in progress.',
        'state' => [
            'init' => 'Initiated',
            'pendingManual' => 'Pending (manual)',
            'pendingAuto' => 'Pending (automatic)',
            'processing' => 'Processing',
            'completed' => 'Completed',
            'revoked' => 'Revoked',
            'rejected' => 'Rejected',
            'failed' => 'Failed',
            'cancelled' => 'Cancelled',
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
        'notJoined' => 'Not joined',

        'hintJoin' => 'You aren\'t part of this bar yet.',
        'joinedClickToLeave' => 'Click to leave.',
        'joinQuestion' => 'Would you like to join this bar?',
        'alsoJoinCommunity' => 'Also join their community',
        'alreadyJoinedTheirCommunity' => 'You already are a member of their community',
        'joinedThisBar' => 'You\'ve joined this bar.',
        'cannotSelfEnroll' => 'You cannot join this bar yourself, it is disabled.',
        'leaveQuestion' => 'Are you sure you want to leave this bar?',
        'leftThisBar' => 'You left this bar.',
        'protectedByCode' => 'This bar is protected by a passcode. Request it at the bar, or scan the bar QR code if available.',
        'protectedByCodeFilled' => 'This bar is protected by a passcode. We\'ve filled it in for you.',
        'incorrectCode' => 'Incorrect bar code.',
        'namePlaceholder' => 'Viking bar',
        'descriptionPlaceholder' => 'Welcome to thé Viking bar!',
        'slugDescription' => 'A slug allows you to create an easy to remember URL to access this bar, by defining a short keyword.',
        'slugDescriptionExample' => 'This could simplify your bar URL:',
        'slugPlaceholder' => 'viking',
        'slugFieldRegexError' => 'The slug must start with an alphabetical character.',
        'codeDescription' => 'With a bar code, you prevent random users from joining. To join the bar, users are required to enter the specified code.',
        'economyDescription' => 'The economy defines what products, currencies and wallets are used in this bar. Be very careful with changing it after the bar is created, as this immediately affects the list of products, currencies and wallets used in this bar. Users probably don\'t expect this, and might find it hard to understand.',
        'showExploreDescription' => 'List on public \'Explore bars\' page',
        'showCommunityDescription' => 'List on community page for community members',
        'selfEnrollDescription' => 'Allow self enrollment (with code if specified)',
        'joinAfterCreate' => 'Join the bar after creating.',
        'created' => 'The bar has been created.',
        'updated' => 'The bar has been updated.',
        'mustCreateEconomyFirst' => 'To create a bar, you must create an economy first.',
        'backToBar' => 'Back to bar',
        'quickBuy' => 'Quick buy',
        'boughtProductForPrice' => 'Bought :product for :price',
        'noDescription' => 'This bar has no description',
        'barInfo' => 'Bar info',
        'viewBar' => 'View bar',
        'deleted' => 'The bar has been deleted.',
        'deleteQuestion' => 'You\'re about to permanently delete this bar. All members including yourself will lose access to it, and won\'t be possible to link product transactions to it anymore. The products and user wallets will remain as part of the economy that was used in this bar. Are you sure you want to continue?',
        'exactBarNameVerify' => 'Exact name of bar to delete (Verification)',
        'incorrectNameShouldBe' => 'Incorrect name, should be: \':name\'',
        'generatePoster' => 'Create bar poster',
        'generatePosterDescription' => 'Create a poster for this bar to hang on a wall. Visitors will then be able to easily use :app and join this bar by scanning a QR code with their mobile phone.',
        'showCodeOnPoster' => 'Show code to join this bar on the poster',
        'poster' => [
            'thisBarUses' => 'This bar uses',
            'toDigitallyManage' => 'to digitally manage payments and inventory for consumptions',
            'scanQr' => 'scan the QR code with your phone to join and make a purchase',
            'orVisit' => 'Or visit',
        ],
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
        'activePastMonth' => 'Active past month',
        'productsPastHour' => 'Products past hour',
        'productsPastDay' => 'Products past day',
        'productsPastMonth' => 'Products past month',
    ],

    /**
     * Bar member pages.
     */
    'barMembers' => [
        'title' => 'Bar members',
        'description' => 'This page shows an overview of all bar members.<br>Clicking on a member allows you to remove the member, or change it\'s role.',
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
        'description' => 'When you use our service, you\'re trusting us with your information. We understand this is a big responsibility.<br />The Privacy Policy below is meant to help you understand how we manage your information.',
        'onlyEnglishNote' => 'Note that the Privacy Policy is only available in English, although it applies to our service in any language.',
        'questions' => 'Questions?',
        'questionsDescription' => 'If you have any further questions about our Privacy Policy or your privacy when using our service, be sure to get in touch with us.',
    ],

    /**
     * Terms of Service page.
     */
    'terms' => [
        'title' => 'Terms',
        'description' => 'When you use our service, your\'re agreeing with our Terms of Service as shown below.',
        'onlyEnglishNote' => 'Note that the Terms of Service is only available in English, although it applies to our service in any language.',
        'questions' => 'Questions?',
        'questionsDescription' => 'If you have any further questions about our Terms of Service, be sure to get in touch with us.',
    ],

    /**
     * License page.
     */
    'license' => [
        'title' => 'License',
        'description' => 'The BARbapAPPa software project is released under the GNU GPL-3.0 license. This license describes what you are and are not allowed to with the source code of this project.<br />Read the full license below, or check out the summary for this license as quick summary.',
        'onlyEnglishNote' => 'Note that the license is only available in English, although it applies to this project in any language.',
        'questions' => 'Questions?',
        'questionsDescription' => 'If you have any further questions about the license used for this project, be sure to get in touch with us. You can also check out the plain text license readable on any device.',
        'plainTextLicense' => 'Plain text license',
        'licenseSummary' => 'License summary',
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
];
