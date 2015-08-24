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

    <!-- Meta -->
    <!-- TODO: Add proper meta tags here! -->
    <title><?=APP_NAME; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#2A2A2A">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="BarApp by Tim Vis&eacute;e's">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?=$site_root; ?>style/image/favicon.png" />

    <!-- Script -->
    <script src="<?=$site_root; ?>lib/jquery/jquery-1.11.3.min.js"></script>
    <script src="<?=$site_root; ?>js/jquery.mobile.settings.js"></script>
    <script src="<?=$site_root; ?>js/main.js"></script>

    <!-- Libraries: jQuery Mobile -->
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