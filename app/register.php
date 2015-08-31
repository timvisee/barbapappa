<?php

use app\mail\MailManager;
use app\template\PageFooterBuilder;
use app\template\PageHeaderBuilder;
use app\user\UserManager;
use app\util\AccountUtils;
use carbon\core\util\StringUtils;

// Include the page top
require_once('top.php');

// TODO: Make sure this user isn't logged in already!

// Get the register step
$registerStep = 1;
if(isset($_GET['reg_step'])) {
    // Get the value
    $registerStepValue = $_GET['reg_step'];

    // Make sure the value is an integer, or show an error page instead
    if(!is_numeric($registerStepValue))
        showErrorPage();

    // Set the register step
    $registerStep = (int) $registerStepValue;
}

if($registerStep == 1):
    ?>
    <div data-role="page" id="page-register" data-unload="false">
        <?php PageHeaderBuilder::create(__('account', 'register'))->setBackButton('index.php')->build(); ?>

        <div data-role="main" class="ui-content">
            <p>
                <?= __('register', 'enterFullNameToStart'); ?><br /><br />
                <i><?=__('general', 'note'); ?>: <?=__('register', 'mustEnterRealName'); ?></i>
            </p><br />

            <form method="GET" action="register.php?reg_step=2">
                <input type="text" name="reg_full_name" value="" placeholder="<?= __('account', 'fullName'); ?>" />

                <input type="submit" value="<?= __('navigation', 'continue'); ?>" class="ui-btn ui-icon-lock ui-btn-icon-right" />
            </form>
        </div>

        <?php PageFooterBuilder::create()->build(); ?>
    </div>
    <?php

elseif($registerStep == 2):

    // Get the name
    // TODO: Should we convert the entities here?
    $fullName = htmlentities(trim($_GET['reg_full_name']));

    // Make sure the full name is valid
    if(!AccountUtils::isValidFullName($fullName))
        showErrorPage(__('register', 'invalidFullName'));

    // Get a username suggestion
    $usernameSuggestion = UserManager::getUsernameSuggestionByName(html_entity_decode($fullName));

    ?>
    <div data-role="page" id="page-register" data-unload="false">
        <?php PageHeaderBuilder::create(__('account', 'register'))->setBackButton('index.php')->build(); ?>

        <div data-role="main" class="ui-content">
            <p><?=__('general', 'hello'); ?> <?=$_GET['reg_full_name']; ?>!</p><br />

            <p><?= __('register', 'chooseUsernameForLogin'); ?></p><br />

            <form method="GET" action="register.php?reg_step=3">
                <input type="hidden" name="reg_full_name" value="<?=$fullName; ?>" />

                <input type="text" name="reg_username" value="<?=$usernameSuggestion; ?>" placeholder="<?= __('account', 'username'); ?>" />

                <input type="submit" value="<?= __('navigation', 'continue'); ?>" class="ui-btn ui-icon-lock ui-btn-icon-right" />
            </form>
        </div>

        <?php PageFooterBuilder::create()->build(); ?>
    </div>
    <?php

elseif($registerStep == 3):

    // Get the name and username
    $fullName = $_GET['reg_full_name'];
    $username = trim($_GET['reg_username']);

    // Make sure the username is valid
    if(!AccountUtils::isValidUsername($username))
        showErrorPage(__('register', 'invalidUsername'));

    // Make sure the username is available
    if(UserManager::isUserWithUsername($username))
        showErrorPage(__('register', 'usernameAlreadyInUseChooseDifferentOne'));

    ?>
    <div data-role="page" id="page-register" data-unload="false">
        <?php PageHeaderBuilder::create(__('account', 'register'))->setBackButton('index.php')->build(); ?>

        <div data-role="main" class="ui-content">
            <p><?= __('register', 'enterMail'); ?></p><br />

            <form method="GET" action="register.php?reg_step=4">
                <input type="hidden" name="reg_full_name" value="<?=$fullName; ?>" />
                <input type="hidden" name="reg_username" value="<?=$username; ?>" />

                <input type="text" name="reg_mail" value="" placeholder="<?= __('account', 'mail'); ?>" />

                <input type="submit" value="<?= __('navigation', 'continue'); ?>" class="ui-btn ui-icon-lock ui-btn-icon-right" />
            </form>
        </div>

        <?php PageFooterBuilder::create()->build(); ?>
    </div>
    <?php

elseif($registerStep == 4):

    // Get the name and username
    $fullName = $_GET['reg_full_name'];
    $username = $_GET['reg_username'];
    $mail = trim($_GET['reg_mail']);

    // Make sure the mail is valid
    if(!AccountUtils::isValidMail($mail))
        showErrorPage(__('register', 'invalidMail'));

    // Make sure the mail isn't in use already
    if(MailManager::isMailWithMail($mail))
        showErrorPage(__('register', 'mailAlreadyInUse'));

    ?>
    <div data-role="page" id="page-register" data-unload="false">
        <?php PageHeaderBuilder::create(__('account', 'register'))->setBackButton('index.php')->build(); ?>

        <div data-role="main" class="ui-content">
<!--            <p>--><?//= __('register', 'checkCredentialsBeforeContinue'); ?><!--</p><br />-->

            <table class="ui-responsive">
                <tr>
                    <td><?=__('account', 'fullName'); ?></td>
                    <td><?=$fullName; ?></td>
                </tr>
                <tr>
                    <td><?=__('account', 'username'); ?></td>
                    <td><?=$username; ?></td>
                </tr>
                <tr>
                    <td><?=__('account', 'mail'); ?></td>
                    <td><?=$mail; ?></td>
                </tr>
            </table>
            <br />

            <p><?= __('register', 'enterPasswordIfCredentialsAreCorrect'); ?></p><br />

            <form method="POST" action="register.php?reg_step=5">
                <input type="hidden" name="reg_full_name" value="<?=$fullName; ?>" />
                <input type="hidden" name="reg_username" value="<?=$username; ?>" />
                <input type="hidden" name="reg_mail" value="<?=$mail; ?>" />

                <input type="password" name="reg_password" value="" placeholder="<?= __('account', 'password'); ?>" />
                <input type="password" name="reg_password_verification" value="" placeholder="<?= __('account', 'passwordVerification'); ?>" />

                <input type="submit" value="<?= __('account', 'register'); ?>" class="ui-btn ui-icon-lock ui-btn-icon-right" />
            </form>
        </div>

        <?php PageFooterBuilder::create()->build(); ?>
    </div>
    <?php

elseif($registerStep == 5):

    // Get the name and username
    $fullName = $_POST['reg_full_name'];
    $username = $_POST['reg_username'];
    $mail = $_POST['reg_mail'];
    $password = $_POST['reg_password'];
    $passwordVerification = $_POST['reg_password_verification'];

    // Make sure the full name is valid
    if(!AccountUtils::isValidFullName($fullName))
        showErrorPage();

    // Make sure the username is valid
    if(!AccountUtils::isValidUsername($username))
        showErrorPage();

    // Make sure the username is available
    if(UserManager::isUserWithUsername($username))
        showErrorPage();

    // Make sure the mail is valid
    if(!AccountUtils::isValidMail($mail))
        showErrorPage();

    // Make sure the mail isn't in use already
    if(MailManager::isMailWithMail($mail))
        showErrorPage();

    // Make sure the password is valid
    if(!AccountUtils::isValidPassword($password))
        showErrorPage(__('register', 'invalidPassword'));

    // Make sure the passwords are equal
    if(!StringUtils::equals($password, $passwordVerification, true, false))
        showErrorPage(__('register', 'passwordsNotEqual'));

    // Create the user
    UserManager::createUser($username, $password, $mail, $fullName);

    ?>
    <div data-role="page" id="page-register" data-unload="false">
        <?php PageHeaderBuilder::create(__('account', 'register'))->setBackButton('index.php')->build(); ?>

        <div data-role="main" class="ui-content">
            <p>
                <?=__('general', 'welcome'); ?> <?=$fullName; ?>!<br />
                <br />
                <?=__('register', 'registeredSuccessfullyVerifyMail'); // TODO: Show a note, that the mail address must be activated within a specific period! ?>
            </p><br />

            <fieldset data-role="controlgroup" data-type="vertical">
                <a href="index.php" data-ajax="false" class="ui-btn ui-icon-home ui-btn-icon-left" data-direction="reverse"><?=__('navigation', 'goToFrontPage'); ?></a>
            </fieldset>
        </div>

        <?php PageFooterBuilder::create()->build(); ?>
    </div>
    <?php

endif;

// Include the page bottom
require_once('bottom.php');