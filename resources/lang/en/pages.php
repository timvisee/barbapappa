<?php

/**
 * Pages and their names.
 */
return [
    'pages' => 'Pages',
    'index' => 'Main page',
    'dashboard' => 'Dashboard',
    'yourPersonalDashboard' => 'Your personal dashboard',
    'emailPreferences' => 'Email preferences',
    'communities' => 'Communities',
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
            'cannotDeleteMustHaveVerified' => 'You cannot delete this email address, you must have at least one verified address.',
            'deleted' => 'The email address has been deleted.',
            'deleteQuestion' => 'Are you sure you want to delete this email address?',
        ],
        'addEmail' => [
            'title' => 'Add email address',
            'description' => 'Fill in the email address you\'d like to add.',
            'added' => 'Email address added. A verification email has been sent.',
        ],
    ],

    /**
     * Community pages.
     */
    'community' => [
        'yourCommunities' => 'Your communities',
        'noCommunities' => 'No communities available...',
        'viewCommunity' => 'View community',
        'viewCommunities' => 'View communities',
        'editCommunity' => 'Edit community',
        'join' => 'Join',
        'yesJoin' => 'Yes, join',
        'joined' => 'Joined',
        'notJoined' => 'Not joined',
        'hintJoin' => 'You aren\'t part of this community yet.',
        'joinedClickToLeave' => 'Click to leave.',
        'joinQuestion' => 'Would you like to join this community?',
        'joinedThisCommunity' => 'You\'ve joined this community.',
        'leaveQuestion' => 'Are you sure you want to leave this community?',
        'leftThisCommunity' => 'You left this community.',
        'protectedByCode' => 'This community is protected by a passcode. Request it at the community, or scan the community QR code if available.',
        'protectedByCodeFilled' => 'This community is protected by a passcode. We\'ve filled it in for you.',
        'incorrectCode' => 'Incorrect community code.',
        'namePlaceholder' => 'The Vikings',
        'slugDescription' => 'A slug allows you to create an easy to remember URL to access this community, by defining a short keyword.',
        'slugDescriptionExample' => 'This could simplify your community URL:',
        'slugPlaceholder' => 'vikings',
        'slugFieldRegexError' => 'The slug must start with an alphabetical character.',
        'codeDescription' => 'With a community code, you prevent random users from joining. To join the community, users are required to enter the specified code.',
        'visibleDescription' => 'Visible in the list of communities.',
        'publicDescription' => 'Allow users to join without a code.',
        'updated' => 'The community has been updated.',
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
        'editBar' => 'Edit bar',
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
        'leaveQuestion' => 'Are you sure you want to leave this bar?',
        'leftThisBar' => 'You left this bar.',
        'protectedByCode' => 'This bar is protected by a passcode. Request it at the bar, or scan the bar QR code if available.',
        'protectedByCodeFilled' => 'This bar is protected by a passcode. We\'ve filled it in for you.',
        'incorrectCode' => 'Incorrect bar code.',
        'namePlaceholder' => 'Viking bar',
        'slugDescription' => 'A slug allows you to create an easy to remember URL to access this bar, by defining a short keyword.',
        'slugDescriptionExample' => 'This could simplify your bar URL:',
        'slugPlaceholder' => 'viking',
        'slugFieldRegexError' => 'The slug must start with an alphabetical character.',
        'codeDescription' => 'With a bar code, you prevent random users from joining. To join the bar, users are required to enter the specified code.',
        'visibleDescription' => 'Visible in the list of bars.',
        'publicDescription' => 'Allow users to join without a code.',
        'updated' => 'The bar has been updated.',
    ],

    /**
     * Bar member pages.
     */
    'barMembers' => [
        'title' => 'Bar members',
        'description' => 'This page shows an overview of all bar members.<br>Clicking on a member allows you to remove the member, or change it\'s role.',
        'noMembers' => 'This bar has no members...',
        'memberSince' => 'Member since',
        'deleteQuestion' => 'You\'re about to remove this member from this bar. Are you sure you want to continue?',
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
     * No permission page.
     */
    'noPermission' => [
        'title' => 'You shouldn\'t be here...',
        'description' => 'You took a wrong turn.<br />You don\'t have enough permission to access this content.',
        'notLoggedIn' => 'Not logged in',
        'notLoggedInDescription' => 'You\'re currently not logged in. You may want to login to get proper access rights.',
    ],
];
