<?php

use app\config\Config;
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
    <meta name="description" content="BarApp by Tim Vis&eacute;e">
    <meta name="keywords" content="BarApp,Bar,App">
    <meta name="author" content="Tim Vis&eacute;e">
    <link rel="copyright" href="about.php">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#2A2A2A">
    <meta name="application-name" content="BarApp">
    <meta name="msapplication-TileColor" content="#2a2a2a">
    <meta name="msapplication-config" content="<?=$site_root; ?>style/image/favicon/browserconfig.xml?v=eEEv324W35">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta property="og:title" content="BarApp">
    <meta property="og:image" content="<?=$site_root; ?>style/image/favicon/favicon-194x194.png">
    <meta property="og:description" content="BarApp by Tim Vis&eacute;e">
    <meta name="twitter:card" content="summary" />
    <meta name="twitter:title" content="BarApp">
    <meta name="twitter:image" content="<?=$site_root; ?>style/image/favicon/apple-touch-icon-120x120.png">
    <meta name="twitter:description" content="BarApp by Tim Vis&eacute;e">

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="57x57" href="<?=$site_root; ?>style/image/favicon/apple-touch-icon-57x57.png?v=eEEv324W35">
    <link rel="apple-touch-icon" sizes="60x60" href="<?=$site_root; ?>style/image/favicon/apple-touch-icon-60x60.png?v=eEEv324W35">
    <link rel="apple-touch-icon" sizes="72x72" href="<?=$site_root; ?>style/image/favicon/apple-touch-icon-72x72.png?v=eEEv324W35">
    <link rel="apple-touch-icon" sizes="76x76" href="<?=$site_root; ?>style/image/favicon/apple-touch-icon-76x76.png?v=eEEv324W35">
    <link rel="apple-touch-icon" sizes="114x114" href="<?=$site_root; ?>style/image/favicon/apple-touch-icon-114x114.png?v=eEEv324W35">
    <link rel="apple-touch-icon" sizes="120x120" href="<?=$site_root; ?>style/image/favicon/apple-touch-icon-120x120.png?v=eEEv324W35">
    <link rel="apple-touch-icon" sizes="144x144" href="<?=$site_root; ?>style/image/favicon/apple-touch-icon-144x144.png?v=eEEv324W35">
    <link rel="apple-touch-icon" sizes="152x152" href="<?=$site_root; ?>style/image/favicon/apple-touch-icon-152x152.png?v=eEEv324W35">
    <link rel="apple-touch-icon" sizes="180x180" href="<?=$site_root; ?>style/image/favicon/apple-touch-icon-180x180.png?v=eEEv324W35">
    <link rel="icon" type="image/png" href="<?=$site_root; ?>style/image/favicon/favicon-32x32.png?v=eEEv324W35" sizes="32x32">
    <link rel="icon" type="image/png" href="<?=$site_root; ?>style/image/favicon/favicon-194x194.png?v=eEEv324W35" sizes="194x194">
    <link rel="icon" type="image/png" href="<?=$site_root; ?>style/image/favicon/favicon-96x96.png?v=eEEv324W35" sizes="96x96">
    <link rel="icon" type="image/png" href="<?=$site_root; ?>style/image/favicon/android-chrome-192x192.png?v=eEEv324W35" sizes="192x192">
    <link rel="icon" type="image/png" href="<?=$site_root; ?>style/image/favicon/favicon-16x16.png?v=eEEv324W35" sizes="16x16">
    <link rel="manifest" href="<?=$site_root; ?>style/image/favicon/manifest.json?v=eEEv324W35">
    <link rel="shortcut icon" href="<?=$site_root; ?>style/image/favicon/favicon.ico?v=eEEv324W35">
    <meta name="apple-mobile-web-app-title" content="BarApp">
    <meta name="msapplication-TileImage" content="<?=$site_root; ?>style/image/favicon/mstile-144x144.png?v=eEEv324W35">

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
    $loggedIn = SessionManager::isLoggedIn();

    // Show an error if the user isn't logged in
    if(!$loggedIn) {
        ?>
        <div data-role="page" id="page-main">
            <?php PageHeaderBuilder::create('Whoops!')->setBackButton('index.php')->build(); ?>

            <div data-role="main" class="ui-content">
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
 * Require the current user to be an administrator (the user must be logged in.
 * Show an error page instead if that's not the case.
 */
function requireAdmin() {
    // Make sure the user is logged in
    requireLogin();

    // Get the logged in team
    $team = SessionManager::getLoggedInSession()->getTeam();

    // Check whether the user is logged in
    $isAdmin = $team->isAdmin();

    // Show an error if the user isn't admin
    if(!$isAdmin) {
        ?>
        <div data-role="page" id="page-main">
            <?php PageHeaderBuilder::create('Whoops!')->setBackButton('index.php')->build(); ?>

            <div data-role="main" class="ui-content">
                <p>Whoops! You don't have the right privileges to visit this page.<br />Please go back to the previous page.</p><br />

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
 */
function showErrorPage($errorMsg = null) {
    // Show an error page
    ?>
    <div data-role="page" id="page-main">
        <?php PageHeaderBuilder::create('Whoops!')->setBackButton('index.php')->build();

        if($errorMsg === null): ?>
            <div data-role="main" class="ui-content">
                <p>Whoops! An error occurred that couldn't be recovered.<br />Please go back and try it again!</p><br />

                <a href="index.php" data-rel="back" class="ui-btn ui-icon-back ui-btn-icon-left" data-direction="reverse">Go Back</a>
            </div>
        <?php else: ?>
            <div data-role="main" class="ui-content">
                <p><?=$errorMsg; ?></p><br />

                <a href="index.php" data-rel="back" class="ui-btn ui-icon-back ui-btn-icon-left" data-direction="reverse">Go Back</a>
            </div>
        <?php endif;

        PageFooterBuilder::create()->build(); ?>
    </div>
    <?php

    // Print the bottom of the page
    require('bottom.php');
    die();
}