<?php

/**
 * Pages and their names.
 */
return [
    'pages' => 'Parchments',
    'index' => 'Home port',
    'dashboard' => 'Home port',
    'yourPersonalDashboard' => 'Ye home port',
    'emailPreferences' => 'E-bottle preferences',
    'communities' => 'Crews',
    'bars' => 'Bars',
    'account' => 'Ye ship',
    'yourAccount' => 'Ye ship',
    'requestPasswordReset' => 'Request passcode reset',
    'changePassword' => 'Change passcode',
    'changePasswordDescription' => 'First enter yer ol\' n\' passcode. Den enter ye shiny, fresh n\' new passcode to take abroad.',
    'about' => '\'bout',
    'contact' => 'Contact',
    'contactUs' => 'Contact us',

    /**
     * Last page.
     */
    'last' => [
        'title' => 'Visit last',
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
            'resendVerify' => 'Sally forth verification',
            'verifySent' => 'A fresh verification e-bottle be sally forth.',
            'alreadyVerified' => 'Th\' e-bottle coordinate be verified.',
            'cannotDeleteMustHaveOne' => 'Ye no delete \'his e-bottle coordinate, ye must be one coordinate.',
            'cannotDeleteMustHaveVerified' => 'Ye no delete \'his e-bottle coordinate, ye must be one verified coordinate.',
            'deleted' => 'Th\' e-bottle coordinate be deleted.',
            'deleteQuestion' => 'Ye be sure ye want to sunk dis e-bottle address?',
        ],
        'addEmail' => [
            'title' => 'Add e-bottle coordinate',
            'description' => 'Enter yer e-bottle coordinate to conquer.',
            'added' => 'E-bottle coordinate added. \'ll verification be sent.',
        ],
        'backToAccount' => 'Back to account',
    ],

    /**
     * Community pages.
     */
    'community' => [
        'yourCommunities' => 'Ye crews',
        'noCommunities' => 'Nay crews asea...',
        'viewCommunity' => 'View crew',
        'viewCommunities' => 'View crews',
        'createCommunity' => 'Create crew',
        'editCommunity' => 'Edit crew',
        'join' => 'Join',
        'yesJoin' => 'Yey, sail ho!',
        'joined' => 'Joined',
        'notJoined' => 'Strangers!',
        'hintJoin' => 'Ye be not part of th\' crew.',
        'joinedClickToLeave' => 'Click to be sunk.',
        'joinQuestion' => 'Ye like be joined th\' crew?',
        'joinedThisCommunity' => 'Ye joined th\' crew.',
        'leaveQuestion' => 'Ye be sure to sink th\' crew?',
        'leftThisCommunity' => 'Ye sunk th\' crew.',
        'protectedByCode' => 'Dis crew be protected by a secret. Request it at yer crew, or use yer binoculars to scan the crew Q-ARRRR code if available.',
        'protectedByCodeFilled' => 'Dis crew be protected by a secret. We filled it for ye.',
        'incorrectCode' => 'Crew code be incorrect.',
        'namePlaceholder' => 'Seven Seas',
        'slugDescription' => 'Ye slug allows ye to create \'n easy to remember URL to access dis crew, by defining a short keyword.',
        'slugDescriptionExample' => 'Dis could simplify ye crew URL:',
        'slugPlaceholder' => 'seven-seas',
        'slugFieldRegexError' => 'Dis slug must start with n alphabetical character.',
        'codeDescription' => 'With a crew secret, ye prevent random pirates from joining. To join the crew, users be required to enter th\' secret.',
        'visibleDescription' => 'Visible in th\' list o\' crews.',
        'publicDescription' => 'Allow pirates be joined with no secret.',
        'created' => 'Th\' crew be created.',
        'updated' => 'Th\' crew be updated.',
        'economy' => 'Booty',
        'goTo' => 'Go to crew',
        'backToCommunity' => 'Back to crew',
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
        'economyDeleted' => 'Th\' booty be sunk.',
        'economyUpdated' => 'Booty changes saved.',
        'namePlaceholder' => 'Our booty',
        'all' => 'All booties',
        'backToEconomy' => 'Back to booty',
    ],

    /**
     * Community economy currency pages.
     */
    'currencies' => [
        'title' => 'Enabled currencies',
        'description' => 'Dis page shows \'n overview of enabled currencies in th\' booty.<br>At least one currency must be enabled to use dis booty for a bar.<br>Add new currency, or click one to manage.',
        'change' => 'Change currency',
        'noCurrencies' => 'Dis booty be nay enabled currencies...',
        'createCurrency' => 'Add currency',
        'currencyCreated' => 'Th\' currency be added.',
        'deleteQuestion' => 'Yer \'bout to sink dis currency from dis booty. Ye be sure ye want to continue?',
        'deleteVoidNotice' => 'When ye remove dis booty, all configured product prices for dis currency will be voided in bars dat use dis booty.<br>Ye may want to disable dis currency instead by changing it, which allows yer to enable it again later without having to reenter all prices again.',
        'currencyDeleted' => 'Th\' currency be sunk.',
        'currencyUpdated' => 'Currency changes saved.',
        'enabledTitle' => 'Enable currency',
        'enabledDescription' => 'Specify whether dis currency be enabled in bars dat arrr using dis booty. If disabled, bar maties won\'t be able to purchase products with dis currency, \'n must use a different currency if available until it is enabled again.',
        'changeCurrencyTitle' => 'Change currency?',
        'changeCurrencyDescription' => 'Th\' currency nay be changed directly. To change th\' currency, ye must remove dis configuration and add a new one to dis booty.',
        'allowWallets' => 'Allow wallet creation',
        'allowWalletsDescription' => 'With dis option ye specify whether bar mateies be create a new personal wallet for dis currency. Existing wallets always be kept afloat.',
        'noCurrenciesToAdd' => 'There are no currencies you can add. Ask the site administrator to configure a currency.',
        'noMoreCurrenciesToAdd' => 'There are no other currencies you can add.',
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
        'economySelectDescription' => 'Wallets in dis community be grouped by booty.<br>Select \'the booty to manage yer wallets.',
        'noWallets' => 'Yer nay be any wallets yet...',
        'namePlaceholder' => 'My wallet',
        'createWallet' => 'Create wallet',
        'walletCreated' => 'Th\' wallet be created.',
        'walletUpdated' => 'Wallet changes saved.',
        'deleteQuestion' => 'Yer \'bout to sink this wallet. Ye be sure ye want to continue?',
        'cannotDeleteNonZeroBalance' => 'To sink dis wallet, be have a balance of exactly :zero.',
        'walletDeleted' => 'Th\' wallet be sunk.',
        'cannotCreateNoCurrencies' => 'Ye nay create a wallet. Th\' crew admin did nay configure a currency which allows dis.',
        'all' => 'All wallets',
        'view' => 'View wallet',
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
        'state' => [
            'pending' => 'Pendin\'',
            'processing' => 'Processin\'',
            'success' => 'Hurray!',
            'failed' => 'Sunk',
        ],
        'types' => [
            'magic' => 'Special mutation',
            'walletTo' => 'Deposit to wallet',
            'walletFrom' => 'Payed wit\' wallet',
            'walletToDetail' => 'Deposit to :wallet',
            'walletFromDetail' => 'Payed wit\' :wallet',
            'productTo' => 'Received booty for product(s)',
            'productFrom' => 'Payed for product(s)',
            'paymentTo' => 'Withdrawal to external account',
            'paymentFrom' => 'Deposit from external account',
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
        'leaveQuestion' => 'Ye be sure to sink th\' bar?',
        'leftThisBar' => 'Ye sunk th\' bar.',
        'protectedByCode' => 'Dis bar be protected by a secret. Request it at yer bar, or use yer spyglass to scan the bar Q-ARRRR code if available.',
        'protectedByCodeFilled' => 'Dis bar be protected by a secret. We filled it for ye.',
        'incorrectCode' => 'Bar code be incorrect.',
        'namePlaceholder' => 'Queen Anne\'s ship',
        'slugDescription' => 'Ye slug allows ye to create \'n easy to remember URL to access dis bar, by defining a short keyword.',
        'slugDescriptionExample' => 'Dis could simplify ye bar URL:',
        'slugPlaceholder' => 'anne',
        'slugFieldRegexError' => 'Dis slug must start with n alphabetical character.',
        'codeDescription' => 'With a bar secret, ye prevent random pirates from joining. To join the bar, users be required to enter th\' secret.',
        'visibleDescription' => 'Visible in th\' list o\' bars.',
        'publicDescription' => 'Allow pirates be joined with no secret.',
        'created' => 'Th\' bar be created.',
        'updated' => 'Th\' bar be updated.',
        'mustCreateEconomyFirst' => 'To create a bar, ye must create booty first.',
        'backToBar' => 'Back to bar',
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
        'description' => 'When ye use our seas, yer trusting us with yer information. We understand this be a big responsibility. We be pirates but we must follow landlubber laws.<br />Th\' Piracy Policy below is meant to help ye understand how we manage yer information.',
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
        'description' => 'Th\' ARRbapAPPa software project be released under th\' GNU GPL-3.0 license. Dis license maps what ye are and nay be allowed to do with th\' source code of dis project.<br />Read th\' full license below, o\' check out th\' TL;DR for dis license as quick summary.',
        'onlyEnglishNote' => 'Note th\' license only be available in landlubber English, although it applies to our seas in any speak.',
        'questions' => 'Questions?',
        'questionsDescription' => 'If ye have any further questions about our license, be sure to ship us a bottle message. Ye can also check th\' raw license readable on any ship.',
        'rawLicense' => 'Raw license',
        'licenseTldr' => 'License TL;DR',
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
];
