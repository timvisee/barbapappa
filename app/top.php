<?php

use app\config\Config;
use app\mail\verification\MailVerificationManager;
use app\session\SessionManager;
use app\template\PageFooterBuilder;
use app\template\PageHeaderBuilder;

// Initialize the app
require_once('app/init.php');

// Set the site's path
$site_root = Config::getValue('general', 'site_url', '');

?>
<!DOCTYPE>
<html>
<head>

    <title><?=APP_NAME; ?></title>

    <!-- Meta -->
    <meta charset="UTF-8">
    <meta name="description" content="BARbapAPPa by Tim Vis&eacute;e">
    <meta name="keywords" content="BARbapAPPa,Bar,App">
    <meta name="author" content="Tim Vis&eacute;e">
    <link rel="copyright" href="about.php">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#2A2A2A">
    <meta name="application-name" content="BARbapAPPa">
    <meta name="msapplication-TileColor" content="#2a2a2a">
    <meta name="msapplication-config" content="<?=$site_root; ?>style/image/favicon/browserconfig.xml">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta property="og:title" content="BARbapAPPa">
    <meta property="og:image" content="<?=$site_root; ?>style/image/favicon/favicon-194x194.png">
    <meta property="og:description" content="BARbapAPPa by Tim Vis&eacute;e">
    <meta name="twitter:card" content="summary" />
    <meta name="twitter:title" content="BARbapAPPa">
    <meta name="twitter:image" content="<?=$site_root; ?>style/image/favicon/apple-touch-icon-120x120.png">
    <meta name="twitter:description" content="BARbapAPPa by Tim Vis&eacute;e">

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="57x57" href="<?=$site_root; ?>style/image/favicon/apple-touch-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="<?=$site_root; ?>style/image/favicon/apple-touch-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="<?=$site_root; ?>style/image/favicon/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="<?=$site_root; ?>style/image/favicon/apple-touch-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="<?=$site_root; ?>style/image/favicon/apple-touch-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="<?=$site_root; ?>style/image/favicon/apple-touch-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="<?=$site_root; ?>style/image/favicon/apple-touch-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="<?=$site_root; ?>style/image/favicon/apple-touch-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?=$site_root; ?>style/image/favicon/apple-touch-icon-180x180.png">
    <link rel="icon" type="image/png" href="<?=$site_root; ?>style/image/favicon/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="<?=$site_root; ?>style/image/favicon/favicon-194x194.png" sizes="194x194">
    <link rel="icon" type="image/png" href="<?=$site_root; ?>style/image/favicon/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/png" href="<?=$site_root; ?>style/image/favicon/android-chrome-192x192.png" sizes="192x192">
    <link rel="icon" type="image/png" href="<?=$site_root; ?>style/image/favicon/favicon-16x16.png" sizes="16x16">
    <link rel="manifest" href="<?=$site_root; ?>style/image/favicon/manifest.json">
    <link rel="shortcut icon" href="<?=$site_root; ?>style/image/favicon/favicon.ico">
    <meta name="apple-mobile-web-app-title" content="BARbapAPPa">
    <meta name="msapplication-TileImage" content="<?=$site_root; ?>style/image/favicon/mstile-144x144.png">

    <!-- Script -->
    <script src="<?=$site_root; ?>lib/jquery/jquery-1.11.3.min.js"></script>
    <script src="<?=$site_root; ?>js/jquery.mobile.settings.js"></script>
    <script src="<?=$site_root; ?>js/main.js"></script>

    <!-- Library: jQuery Mobile -->
    <link rel="stylesheet" href="<?=$site_root; ?>lib/jquery-mobile/jquery.mobile-1.4.5.min.css" />
    <script src="<?=$site_root; ?>lib/jquery-mobile/jquery.mobile-1.4.5.min.js"></script>

    <!-- Style -->
    <link rel="stylesheet" type="text/css" href="<?=$site_root; ?>style/style.css">

</head>
<body>

<?php

/**
 * Require the current user to be logged in. If that's not the case, show an error page instead.
 */
function requireLogin() {
    // Check whether the user is logged in
    $user = SessionManager::getLoggedInUser();
    $loggedIn = SessionManager::isLoggedIn();

    // Make sure the user's mail address is verified
    if(!$user->isVerified()) {
        // Get all mails waiting for verification for this user
        $mailsVerify = MailVerificationManager::getMailVerificationsFromUser($user);

        // Check whether this user has any mails waiting for verification
        $hasMailsVerify = sizeof($mailsVerify) > 0;

        ?>
        <div data-role="page" id="page-main">
            <?php PageHeaderBuilder::create()->setMenuButton(true)->build(); ?>

            <div data-role="main" class="ui-content">
                <p>
                    <?=__('general', 'hello'); ?> <?=$user->getFullName(); ?>.<br />
                    <br />
                    <?php
                    if($hasMailsVerify)
                        echo __('mail', 'beforeCanUseServiceMustVerify');
                    else
                        echo __('mail', 'beforeCanUseServiceMustAddMail');
                    ?>
                </p><br />

                <fieldset data-role="controlgroup" data-type="vertical">
                    <?php if($hasMailsVerify): ?>
                        <a href="mailverification.php?a=resend" class="ui-btn ui-icon-mail ui-btn-icon-left"><?=__('mail', 'resendVerification'); ?></a>
                        <a href="mailmanager.php" class="ui-btn ui-icon-edit ui-btn-icon-left"><?=__('mail', 'manageMailAddresses'); ?></a>
                    <?php else: ?>
                        <a href="mailmanager.php?a=add" class="ui-btn ui-icon-plus ui-btn-icon-left"><?=__('mail', 'addMailAddress'); ?></a>
                    <?php endif; ?>
                </fieldset>

                <fieldset data-role="controlgroup" data-type="vertical">
                    <a href="logout.php" class="ui-btn ui-icon-delete ui-btn-icon-left" data-direction="reverse"><?=__('account', 'logout'); ?></a>
                </fieldset>
            </div>

            <?php PageFooterBuilder::create()->build(); ?>
        </div>
        <?php

        // Print the bottom of the page
        require('bottom.php');
        die();
    }

    // Show an error if the user isn't logged in
    if(!$loggedIn) {
        ?>
        <div data-role="page" id="page-main">
            <?php PageHeaderBuilder::create('Whoops!')->setBackButton('index.php')->build(); ?>

            <div data-role="main" class="ui-content">
                <!-- TODO: Make this multi-language! -->
                <p>Whoops! You need to be logged in to view this page.<br />Please go to the front page to login.</p><br />

                <a href="index.php" data-ajax="false" class="ui-btn ui-icon-home ui-btn-icon-left" data-direction="reverse">Go to Front Page</a>
                <a href="index.php" data-rel="back" class="ui-btn ui-icon-back ui-btn-icon-left" data-direction="reverse">Go Back</a>
            </div>

            <?php PageFooterBuilder::create()->build(); ?>
        </div>
        <?php

        // Print the bottom of the page
        require('bottom.php');
        die();
    }
}

/**
 * Show a regular error page.
 *
 * @param string|null $errorMsg [optional] A custom error message, or null to show the default.
 */
function showErrorPage($errorMsg = null) {
    // Show an error page
    ?>
    <div data-role="page" id="page-main">
        <?php PageHeaderBuilder::create(__('error', 'oops'))->setBackButton('index.php')->build();

        if($errorMsg === null): ?>
            <div data-role="main" class="ui-content">
                <p><?=__('error', 'errorOccurred'); ?><br /><?=__('error', 'goBackTryAgain'); ?></p><br />

                <fieldset data-role="controlgroup" data-type="vertical">
                    <a href="index.php" data-ajax="false" data-rel="back" class="ui-btn ui-icon-back ui-btn-icon-left" data-direction="reverse"><?=__('navigation', 'goBack'); ?></a>
                </fieldset>
            </div>
        <?php else: ?>
            <div data-role="main" class="ui-content">
                <p><?=$errorMsg; ?></p><br />

                <fieldset data-role="controlgroup" data-type="vertical">
                    <a href="index.php" data-ajax="false" data-rel="back" class="ui-btn ui-icon-back ui-btn-icon-left" data-direction="reverse"><?=__('navigation', 'goBack'); ?></a>
                </fieldset>
            </div>
        <?php endif;

        PageFooterBuilder::create()->build(); ?>
    </div>
    <?php

    // Print the bottom of the page
    require('bottom.php');
    die();
}