<?php

/**
 * Pages and their names.
 */
return [
    'dashboard' => 'Dashboard',
    'yourPersonalDashboard' => 'Your personal dashboard',
    'emailPreferences' => 'Email preferences',
    'account' => 'Account',
    'yourAccount' => 'Your account',
    'profile' => 'Profile',
    'editProfile' => 'Edit profile',
    'requestPasswordReset' => 'Request password reset',
    'changePassword' => 'Change password',
    'changePasswordDescription' => 'To change your password, fill in the fields below.',
    'about' => 'About',
    'terms' => 'Terms',
    'privacy' => 'Privacy',
    'contact' => 'Contact',

    /**
     * Account page.
     */
    'accountOverview' => [
        'description' => 'This page shows an overview of your account.',
    ],

    /**
     * Password request sent page.
     */
    'passwordRequestSent' => [
        'title' => 'Check your mailbox',
        'message' => 'If the email address you\'ve submitted is known by our system, we\'ll send you instructions to reset your password.<br><br>'
            . 'Please note that if we\'ve send instructions, they are only valid for <b>:hours hours</b>.<br><br>'
            . 'You may close this webpage now.',
    ],

    /**
     * Password reset page.
     */
    'passwordReset' => [
        'enterResetToken' => 'Please enter the password reset token. '
            . 'This token can be found in the email message you\'ve received with password reset instructions.',
        'enterNewPassword' => 'Please enter the new password you\'d like to use from now on.',
    ],
];
