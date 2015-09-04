<?php

use app\mail\MailManager;
use app\session\SessionManager;
use app\template\PageFooterBuilder;
use app\template\PageHeaderBuilder;
use app\user\User;
use app\user\UserManager;
use app\util\AccountUtils;

// Include the page top
require_once('top.php');

// Make sure the user isn't logged in
if(SessionManager::isLoggedIn()) {
    //showErrorPage(__('login', 'alreadyLoggedIn'));

    // Redirect the user to the front page
    header('Location: index.php');
    die();
}

// Check whether the user is trying to login, if not show the login form instead
if(!isset($_POST['login_user']) || !isset($_POST['login_password'])) {
    // Get the default user
    $userValue = '';
    if(isset($_GET['user']))
        $userValue = trim($_GET['user']);

    // Determine whether to show a back button
    $showBackButton = true;
    if(isset($_GET['back']))
        $showBackButton = $_GET['back'] == 1;

    ?>
    <div data-role="page" id="page-login">
        <?php PageHeaderBuilder::create(__('account', 'login'))->setBackButton($showBackButton ? 'index.php' : null)->build(); ?>

        <div data-role="main" class="ui-content">
            <p><?= __('login', 'enterUsernamePasswordToLogin'); ?></p><br />

            <form method="POST" action="login.php?a=login">
                <input type="text" name="login_user" value="<?=$userValue; ?>" placeholder="<?= __('account', 'username'); ?>" />
                <input type="password" name="login_password" value="" placeholder="<?= __('account', 'password'); ?>" /><br />

                <input type="submit" value="<?= __('account', 'login'); ?>" class="ui-btn ui-icon-lock ui-btn-icon-right" />
            </form>
        </div>

        <?php PageFooterBuilder::create()->build(); ?>
    </div>
    <?php

} else {
    // Get the username/email and password
    $loginUser = trim($_POST['login_user']);
    $loginPassword = $_POST['login_password'];

    // Define a variable to get the user
    $user = null;

    // Check whether a user exists with this username
    if(UserManager::isUserWithUsername($loginUser))
        $user = UserManager::getUserWithUsername($loginUser);

    elseif(AccountUtils::isValidMail($loginUser) && MailManager::isMailWithMail($loginUser)) {
        // Get the mail of the user
        $mail = MailManager::getMailWithMail($loginUser);

        // Get the corresponding user if valid
        if($mail !== null)
            $user = $mail->getUser();
    }

    // TODO: Select users with mails still needing to be verified (only if once user is returned)

    // Make sure a user is found
    if(!($user instanceof User))
        showErrorPage(__('login', 'usernameOrPasswordIncorrect'));

    // Validate the password
    if(!$user->isPassword($loginPassword))
        showErrorPage(__('login', 'usernameOrPasswordIncorrect'));

    // Create a session for the user
    if(!SessionManager::createSession($user))
        showErrorPage();

    ?>
    <div data-role="page" id="page-main">
        <?php PageHeaderBuilder::create()->build(); ?>

        <div data-role="main" class="ui-content">
            <p>
                <?= __('general', 'welcome'); ?> <?=$user->getFullName(); ?>!<br />
                <br />
                <?= __('login', 'loginSuccess'); ?>
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
}

// Include the page bottom
require_once('bottom.php');