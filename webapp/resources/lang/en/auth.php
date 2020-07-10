<?php

return [

    /**
     * Common actions.
     */
    'login' => 'Login',
    'loginPassword' => 'Login with password',
    'loginEmail' => 'Login with email',
    'logout' => 'Logout',
    'register' => 'Register',
    'forgotPassword' => 'Forgot your password?',

    /**
     * Authentication related messages.
     */
    'authRequired' => 'Please login or register to continue.',
    'guestRequired' => 'You can\'t visit this page while you\'re logged in.',
    'unauthorized' => 'You are unauthorized to view this page.',
    'passwordChanged' => 'Your password has been changed.',
    'passwordDisabled' => 'Your password has been disabled.',
    'currentPasswordInvalid' => 'Your current password is invalid.',
    'invalidCredentials' => 'Incorrect email address or password.',
    'invalidPassword' => 'Incorrect password.',
    'invalidCurrentPassword' => 'Your current password is incorrect.',
    'newPasswordDifferent' => 'The new password must be different than your current password.',
    'emailUsed' => 'This email address has been used.',
    'iAgreeToTerms' => 'I understand and agree to the <a href=":terms" target="_blank" title="Show Terms of Service">Terms of Service</a> and <a href=":privacy" target="_blank" title="Show Privacy Policy">Privacy Policy</a>.',
    'mustAcceptTerms' => 'You must agree to register.',

    'loggedIn' => 'You\'ve been logged in.',
    'alreadyLoggedIn' => 'You\'re already logged in.',
    'registeredAndLoggedIn' => 'Account registered, you\'ve been logged in.',
    'loggedOut' => 'You\'ve been logged out.',

    'sessionLinkUnknown' => 'Failed to sign in. Expired or invalid session link.',
    'sessionLinkSent' => 'We sent you a magic link to :email. Click on the link to sign in.',
    'unrecognizedEmailRegister' => 'We do not recognize your email address yet. Please register an account to start using :app.',

    'mustVerifyEmail' => 'You must verify your email address to fully use our service.',

    'loginTroubleContact' => 'Trouble logging in? Contact us!',

    /*
    | TODO: Are these properties still required/used?
    |
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed' => 'These credentials do not match our records.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',

];
