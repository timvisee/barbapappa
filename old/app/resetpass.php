<?php

use app\session\SessionManager;
use app\template\PageFooterBuilder;
use app\template\PageHeaderBuilder;
use app\template\PageSidebarBuilder;

// Include the page top
require_once('top.php');

// Make sure the user isn't logged in
if(SessionManager::isLoggedIn()) {
    //header('Location: index.php');
    //die();
}

// Check whether the user has send a password reset request
if(!isset($_POST['reset_user'])) {
    // Get the default user
    $userValue = '';
    if(isset($_GET['user']))
        $userValue = trim($_GET['user']);

    ?>
    <div data-role="page" id="page-login">
        <?php PageHeaderBuilder::create(__('account', 'resetPassword'))->setBackButton($showBackButton ? 'index.php' : null)->build(); ?>
        <div data-role="main" class="ui-content">
            <p><?= __('login', 'enterUsernameToResetPassword'); ?></p><br />
            <form method="POST" action="resetpass.php?a=resetpass">
                <input type="text" name="reset_user" value="<?=$userValue; ?>" placeholder="<?= __('account', 'username'); ?>" />
                <br />

                <fieldset data-role="controlgroup" data-type="vertical" class="ui-shadow ui-corner-all">
                    <input type="submit" value="<?= __('account', 'login'); ?>" class="ui-btn ui-icon-lock ui-btn-icon-right" />
                </fieldset>

                <fieldset data-role="controlgroup" data-type="vertical" class="ui-shadow ui-corner-all"">
                    <a href="register.php" class="ui-btn ui-shadow"><?= __('account', 'register'); ?></a>
                    <a href="#" class="ui-btn ui-shadow"><?= __('account', 'forgotPassword'); ?></a>
                </fieldset>
            </form>
        </div>
        <?php

        // Build the footer and sidebar
        PageFooterBuilder::create()->build();
        PageSidebarBuilder::create()->build();

        ?>
    </div>
    <?php

} else {
    // Get the user to reset
    $resetUser = trim($_POST['reset_user']);

    // TODO: Make sure this user exists

    // TODO: Start the password reset process

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
        <?php

        // Build the footer and sidebar
        PageFooterBuilder::create()->build();
        PageSidebarBuilder::create()->build();
        ?>
    </div>
    <?php
}

// Include the page bottom
require_once('bottom.php');
