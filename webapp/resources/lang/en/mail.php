<?php

/**
 * Mail related translations.
 */
return [
    'signature' => [
        'caption' => 'Thanks,|That\'s all,',
        'signoff' => '~ A :app robot',
    ],

    /**
     * Email emails.
     */
    'email' => [
        /**
         * Email verification email.
         */
        'verify' => [
            'subject' => 'Email verification',
            'subjectRegistered' => 'Registration & email verification',
            'subtitle' => 'You\'re about to verify your email address.',
            'subtitleRegistered' => 'Your account is almost ready.',
            'registered' => 'Thank you for registering an account.',
            'addNewEmail' => 'You\'ve just added a new email address to your account.',
            'verifyBeforeUseAccount' => 'Before you can use our service, you need to verify your email address.',
            'verifyBeforeUseEmail' => 'Before you can use it on our service, you need to verify it.',
            'soon' => 'Please do this as soon as possible as the verification link expires **within :hours hours**.',
            'clickButtonToVerify' => 'Please click the following button to verify your email address.',
            'verifyButton' => 'Verify your email address',
            'manual' => 'If the above button doesn\'t work, you may use the following link and token to verify your email address manually.',
        ],

        /**
         * Email verified email.
         */
        'verified' => [
            'subject' => 'Start using :app',
            'subtitle' => 'First of all, welcome to the club!',
            'accountReady' => 'Your email address has just been verified and your account is now ready.',
            'startUsingSeeDashboard' => 'To start using :app, take a look at your personalized dashboard.',
            'configureEmailPreferences' => 'To configure how often you receive email updates from :app, check out your email preferences panel.',
        ]
    ],

    /**
     * Password emails.
     */
    'password' => [
        /**
         * Password request email.
         */
        'request' => [
            'subject' => 'Password reset request',
            'subtitle' => 'We\'ll help you to configure a new password.',
            'requestedReset' => 'You\'ve just requested to reset your password.',
            'visitResetPage' => 'Simply visit the password reset page and enter your preferred password.',
            'soon' => 'Please do this as soon as possible as the reset link expires **within :hours hours**.',
            'clickButtonToReset' => 'Please click the following button to reset your password.',
            'resetButton' => 'Reset your password',
            'manual' => 'If the above button doesn\'t work, you may use the following link and token to reset your password manually.',
            'notRequested' => 'If you haven\'t requested a password reset, you may ignore this email message.',
        ],

        /**
         * Password reset email.
         */
        'reset' => [
            'subject' => 'Password changed',
            'forSecurity' => 'We\'re just notifying you for security reasons.',
            'useNewPassword' => 'From now on, use your new password to login to your account.',
            'noChangeThenReset' => 'If you didn\'t change your password yourself, please change it as soon as possible using the following link and token.',
            'orContact' => 'Or [contact](:contact) the :app team as soon as possible about this security issue.',
            'noChangeThenContact' => 'If you received this message but haven\'t changed your password, please [contact](:contact) the :contact team as soon as possible about this security issue.',
        ]
    ],

    'payment' => [
        'completed' => [
            'subject' => 'Payment accepted',
            'subtitle' => 'You have topped up your wallet.',
            'paymentReceived' => 'Your payment has been received. It has been processed and accepted.',
            'amountReadyToUse' => 'The amount is now available on your account and is ready to be used.',
        ],
    ],
];
