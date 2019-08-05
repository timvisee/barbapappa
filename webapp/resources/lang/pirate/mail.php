<?php

/**
 * Mail related translations.
 */
return [
    'signature' => [
        'caption' => 'Blimey!|Sail ho!',
        'signoff' => '~ :app pirate robot',
    ],

    /**
     * Email emails.
     * TODO: Translate this
     */
    'email' => [
        /**
         * Email verification email.
         */
        'verify' => [
            'subject' => 'E-bottle check',
            'subjectRegistered' => 'New ship & e-bottle check',
            'subtitle' => 'You be \'bout to check yer e-bottle address',
            'subtitleRegistered' => 'Yer ship be almost sailable.',
            'registered' => 'Ahoy mate for entering a new ship.',
            'addNewEmail' => 'Ye just entered a new e-bottle address to yer ship.',
            'verifyBeforeUseAccount' => 'Before ye use our seas, be need to verify ye e-bottle address.',
            'verifyBeforeUseEmail' => 'Before ye can use it on our seas, be need to verify it.',
            'soon' => 'Please do dis as soon as possible, th\' verification link sinks **within :hours turns o\'the hourglass**.',
            'clickButtonToVerify' => 'Please click th\' following button be verify yer e-bottle address.',
            'verifyButton' => 'Verify yer e-bottle address',
            'manual' => 'If th\' above button doesn\'t work, ye may use the following coordinate n\' token to verify yer e-bottle address by hook.',
        ],

        /**
         * Email verified email.
         */
        'verified' => [
            'subject' => 'Sail wit :app',
            'subtitle' => 'First, sail ho to th\' crew!',
            'accountReady' => 'Yer e-bottle coordinate has just be verified n\' yer ship be now to sail.',
            'startUsingSeeDashboard' => 'To sail wit :app, navigate to yer pirate dashboard.',
            'configureEmailPreferences' => 'To mend th\' sails \'bout how often ye receive e-bottle parchments from :app, navigate to yer e-bottle preferences panel.',
        ]
    ],

    /**
     * Authentication emails.
     */
    // TODO: use :app variable here, instead of BARbapAPPa
    'auth' => [
        /**
         * Session link email.
         */
        'sessionLink' => [
            'subject' => 'Sign in to BARbapAPPa',
            'subtitle' => 'Tap th\' button to sign in to yer BARbapAPPa account.',
            'soon' => 'The link expires **within :minutes minutes**, and can be used once.',
            'button' => 'Sign in to BARbapAPPa',
            'manual' => 'If th\' above button don\'t work, open the following link in yer web browser:',
        ],
    ],

    /**
     * Password emails.
     * TODO: Translate this
     */
    'password' => [
        /**
         * Password request email.
         */
        'request' => [
            'subject' => 'Passcode reset request',
            'subtitle' => 'We\'ll help ye to mend th\' sails for a shiny passcode.',
            'requestedReset' => 'Ye just requested yer shiny passcode.',
            'visitResetPage' => 'Navigate to th\' passcode reset parchment n\' enter yer shiny passcode.',
            'soon' => 'Do dis as soon as possible. Th\' reset coordinate sinks **within :hours turns o\'the hourglass**.',
            'clickButtonToReset' => 'Navigate to th\' following button to reset yer passcode.',
            'resetButton' => 'Reset yer passcode',
            'manual' => 'If th\' above button doesn\'t work, ye may use be following coordinate n\' token to reset yer passcode by hook.',
            'notRequested' => 'If ye have nay requested a shiny passcode, ye may ignore dis e-bottle message.',
        ],

        /**
         * Password reset email.
         */
        'reset' => [
            'subject' => 'Yer passcode mend th\' sails',
            'forSecurity' => 'Our jolly crew notified ye for piracy reasons.',
            'useNewPassword' => 'From now, use yer shiny passcode to enter ye ship.',
            'noChangeThenReset' => 'If ye did nay change yer passcode, change it as soon as possible using th\' following coordinate n\' token.',
            'orContact' => 'Or [contact](:contact) th\' :app crew as soon as possible \'bout dis piracy issue.',
            'noChangeThenContact' => 'If ye received dis message but have nay change yer passcode, [contact](:contact) th\' :contact crew as soon as possible \'bout dis piracy issue.',
        ]
    ],

    'payment' => [
        'completed' => [
            'subject' => 'Payment accepted',
            'subtitle' => 'Ye topped up yer wallet.',
            'paymentReceived' => 'Yer payment be received. It be processed and be accepted.',
            'amountReadyToUse' => 'The amount now be available in yer wallet and be ready for use.',
        ],
        'failed' => [
            'subject' => 'Payment failed',
            'subtitle' => 'Yer wallet top up nay be succesful.',
            'stateFailed' => 'A payment ye started nay be completed, because it failed. If ye believe dis is an error, please bottle-message us.',
            'stateRevoked' => 'A payment ye started nay be completed, because it be revoked. If ye believe dis is an error, please bottle-message us.',
            'stateRejected' => 'A payment ye started nay be completed, because it be rejected. If ye believe dis is an error, please bottle-message us.',
        ],
    ],
];
