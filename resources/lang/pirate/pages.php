<?php

/**
 * Pages and their names.
 */
return [
    'pages' => 'Parchments',
    'dashboard' => 'Home port',
    'yourPersonalDashboard' => 'Ye home port',
    'emailPreferences' => 'E-bottle preferences',
    'account' => 'Ye ship',
    'yourAccount' => 'Ye ship',
    'requestPasswordReset' => 'Request passcode reset',
    'changePassword' => 'Change passcode',
    'changePasswordDescription' => 'First enter yer ol\' n\' passcode. Den enter ye shiny, fresh n\' new passcode to take abroad.',
    'about' => '\'bout',
    'terms' => 'Terms',
    'privacy' => 'Piracy',
    'contact' => 'Contact',

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
            'verifySent' => 'A fresh verification e-bottle be sally forth.',
            'alreadyVerified' => 'Th\' e-bottle coordinate be verified.',
        ],
        'addEmail' => [
            'title' => 'Add e-bottle coordinate',
            'description' => 'Enter yer e-bottle coordinate to conquer.',
            'added' => 'E-bottle coordinate added. \'ll verification be sent.',
        ],
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
];
