<?php

use app\session\SessionManager;
use app\team\Team;
use app\team\TeamManager;
use app\template\PageFooterBuilder;
use app\template\PageHeaderBuilder;

// Include the page top
require_once('top.php');

$loginUser = null;
$loginPass = null;

// Check whether the user is trying to login, if not show the login form instead
if(!isset($_POST['login_user']) || !isset($_POST['login_pass'])) {
    ?>
    <div data-role="page" id="page-login">
        <?php PageHeaderBuilder::create(__('account', 'login'))->setBackButton('index.php')->build(); ?>

        <div data-role="main" class="ui-content">
            <p><?= __('login', 'enterUsernamePasswordToLogin'); ?></p><br />

            <form method="POST" action="login.php?a=login">
                <input type="text" name="login_user" value="" placeholder="<?= __('account', 'username'); ?>" />
                <input type="password" name="login_pass" value=""
                       placeholder="<?= __('account', 'password'); ?>" /><br />

                <input type="submit" value="<?= __('account', 'login'); ?>"
                       class="ui-btn ui-icon-lock ui-btn-icon-right" />
            </form>
        </div>

        <?php PageFooterBuilder::create()->build(); ?>
    </div>
    <?php

} else {

    // TODO: Temporarily show an error page
    showErrorPage();

    $loginUser = $_POST['login_user'];
    $loginPass = $_POST['login_pass'];

    // Make sure a team exists with this ID
    if(!TeamManager::isTeamWithId($loginUser))
        $error = true;

    else
        $team = new Team($loginUser);

    // Validate the password
    $passCorrect = $team->isPassword($loginPass);

    if(!$passCorrect):
        ?>
        <div data-role="page" id="page-main">
            <?php PageHeaderBuilder::create(__('error', 'oops'))->setBackButton('index.php')->build(); ?>

            <div data-role="main" class="ui-content">
                <p><?= __('login', 'usernameOrPasswordIncorrect'); ?><br /><?= __('error', 'goBackTryAgain'); ?></p>
                <br />

                <fieldset data-role="controlgroup" data-type="vertical">
                    <a href="index.php" data-rel="back" class="ui-btn ui-icon-back ui-btn-icon-left"
                       data-direction="reverse"><?= __('navigation', 'goBack'); ?></a>
                </fieldset>
            </div>

            <?php PageFooterBuilder::create()->build(); ?>
        </div>
        <?php
    else:

        // Create a session for the current user
        SessionManager::createSession($team);

        ?>
        <div data-role="page" id="page-main">
            <?php PageHeaderBuilder::create()->build(); ?>

            <div data-role="main" class="ui-content">
                <p>
                    <?= __('general', 'welcome'); ?>!<br /><?= __('login', 'loginSuccess'); ?>

                    <!-- TODO: Add some additional description here! -->
                </p>
                <br />

                <fieldset data-role="controlgroup" data-type="vertical">
                    <a href="index.php" data-ajax="false"
                       class="ui-btn ui-icon-carat-r ui-btn-icon-left"><?= __('navigation', 'continue'); ?></a>
                </fieldset>
            </div>

            <?php PageFooterBuilder::create()->build(); ?>
        </div>
        <?php

    endif;
}

// Include the page bottom
require_once('bottom.php');