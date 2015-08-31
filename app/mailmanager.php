<?php

use app\mail\Mail;
use app\mail\MailManager;
use app\mail\verification\MailVerification;
use app\mail\verification\MailVerificationManager;
use app\session\SessionManager;
use app\template\PageFooterBuilder;
use app\template\PageHeaderBuilder;
use carbon\core\util\StringUtils;

// Include the page top
require_once('top.php');

// Make sure the user is logged in
if(!SessionManager::isLoggedIn())
    showErrorPage(__('login', 'mustBeLoggedInToViewThisPage'));

// Get the user
$user = SessionManager::getLoggedInUser();

// Get the action parameter if set
$a = null;
if(isset($_GET['a']))
    $a = trim($_GET['a']);

if(StringUtils::equals($a, 'edit', false)) {
    ?>
    <div data-role="page" id="page-main">
        <?php PageHeaderBuilder::create(__('mail', 'changeMail'))->setBackButton('index.php')->build(); ?>

        <div data-role="main" class="ui-content">
            <p>TODO: Edit mail here</p>
        </div>

        <?php PageFooterBuilder::create()->build(); ?>
    </div>
    <?php

} elseif(StringUtils::equals($a, 'delete', false)) {
    ?>
    <div data-role="page" id="page-main">
        <?php PageHeaderBuilder::create(__('mail', 'deleteMail'))->setBackButton('index.php')->build(); ?>

        <div data-role="main" class="ui-content">
            <p>TODO: Delete mail here</p>
        </div>

        <?php PageFooterBuilder::create()->build(); ?>
    </div>
    <?php

} elseif(isset($_GET['mail_id'])) {
    // Get the mail ID
    $mailId = $_GET['mail_id'];

    // Make sure the ID is valid
    if(!MailManager::isMailWithId($mailId))
        showErrorPage();

    // Get the mail object
    $mail = new Mail($mailId);

    // Make sure the mail waiting for verification is from the current user
    if($mailVerification->getUser()->getId() != $user->getId())
        showErrorPage();

    ?>
    <div data-role="page" id="page-main">
        <?php PageHeaderBuilder::create(__('mail', 'manageMail'))->setBackButton('mailmanager.php')->build(); ?>

        <div data-role="main" class="ui-content">
            <table class="ui-responsive">
                <tr>
                    <td><?=__('mail', 'address'); ?></td>
                    <td><?=$mail->getMail(); ?></td>
                </tr>
                <tr>
                    <td><?=__('account', 'verified'); ?></td>
                    <td><span style="color: green;"><?=__('general', 'acceptanceYes'); ?>!</span></td>
                </tr>
                <tr>
                    <td><?=__('mail', 'verifiedAt'); ?></td>
                    <td><?=$mail->getVerificationDateTime(); ?></td>
                </tr>
            </table>
            <br />

            <p>
                <?=__('mail', 'pressButtonToChangeOrDelete'); ?>
            </p><br />

            <fieldset data-role="controlgroup" data-type="vertical">
                <a href="mailmanager.php?mail_id=<?=$mailId; ?>&a=edit" class="ui-btn ui-icon-edit ui-btn-icon-left"><?=__('mail', 'changeMailAddress'); ?></a>
                <a href="mailmanager.php?mail_id=<?=$mailId; ?>&a=delete" class="ui-btn ui-icon-delete ui-btn-icon-left"><?=__('mail', 'deleteMailAddress'); ?></a>
            </fieldset>
        </div>

        <?php PageFooterBuilder::create()->build(); ?>
    </div>
    <?php

} elseif(isset($_GET['mail_verification_id'])) {
    // Get the mail ID
    $mailVerificationId = $_GET['mail_verification_id'];

    // Make sure the ID is valid
    if(!MailVerificationManager::isMailVerificationWithId($mailVerificationId))
        showErrorPage();

    // Get the mail verification object
    $mailVerification = new MailVerification($mailVerificationId);

    // Make sure the mail waiting for verification is from the current user
    if($mailVerification->getUser()->getId() != $user->getId())
        showErrorPage();

    ?>
    <div data-role="page" id="page-main">
        <?php PageHeaderBuilder::create(__('mail', 'manageMail'))->setBackButton('mailmanager.php')->build(); ?>

        <div data-role="main" class="ui-content">
            <table class="ui-responsive">
                <tr>
                    <td><?=__('mail', 'address'); ?></td>
                    <td><?=$mailVerification->getMail(); ?></td>
                </tr>
                <tr>
                    <td><?=__('account', 'verified'); ?></td>
                    <td><span style="color: red;"><?=__('general', 'acceptanceNo'); ?>!</span></td>
                </tr>
                <tr>
                    <td><?=__('mail', 'addedAt'); ?></td>
                    <td><?=$mailVerification->getCreationDateTime(); ?></td>
                </tr>
                <tr>
                    <td><?=__('mail', 'expireAt'); ?></td>
                    <td><?=$mailVerification->getExpirationDateTime(); ?></td>
                </tr>
            </table>
            <br />

            <p>
                <?=__('mail', 'pressButtonToVerifyChangeOrDelete'); ?>
            </p><br />

            <fieldset data-role="controlgroup" data-type="vertical">
                <a href="mailverification.php?a=resend&mail_verification_id=<?=$mailVerificationId; ?>" class="ui-btn ui-icon-mail ui-btn-icon-left"><?=__('mail', 'resendVerification'); ?></a>
            </fieldset>

            <fieldset data-role="controlgroup" data-type="vertical">
                <a href="mailmanager.php?mail_verification_id=&a=edit" class="ui-btn ui-icon-edit ui-btn-icon-left"><?=__('mail', 'changeMailAddress'); ?></a>
                <a href="mailmanager.php?mail_verification_id=<?=$mailVerificationId; ?>&a=delete" class="ui-btn ui-icon-delete ui-btn-icon-left"><?=__('mail', 'deleteMailAddress'); ?></a>
            </fieldset>
        </div>

        <?php PageFooterBuilder::create()->build(); ?>
    </div>
    <?php

} elseif($a === null) {
    ?>
    <div data-role="page" id="page-main">
        <?php PageHeaderBuilder::create(__('mail', 'manageMail'))->setBackButton('index.php')->build(); ?>

        <div data-role="main" class="ui-content">
            <p>
                <?=__('mail', 'clickOnMailToManageOrAdd'); ?>
            </p><br />

            <?php

            // Get all verified and non-verified mail addresses for this user
            $mails = MailManager::getMailsFromUser($user);
            $mailsVerification = MailVerificationManager::getMailVerificationsFromUser($user);

            // Show the mail addresses if the user has any
            if(sizeof($mails) > 0):
                ?>
                <ul class="ui-listview" data-role="listview" id="listview-stations-last-occupied" data-inset="true">
                    <li data-role="list-divider"><?=__('mail', 'mails'); ?></li>
                    <?php
                    // Put all mail addresses in the list
                    foreach($mails as $mailVerification) {
                        // Validate the instance
                        if(!($mailVerification instanceof Mail))
                            continue;

                        echo '<li><a class="ui-btn ui-btn-icon-right ui-icon-carat-r" href="mailmanager.php?mail_id=' . $mailVerification->getId() . '">' . $mailVerification->getMail() . '</a></li>';
                    }
                    ?>
                </ul>
                <?php
            endif;
            if(sizeof($mailsVerification) > 0):
                ?>
                <ul class="ui-listview" data-role="listview" id="listview-stations-last-occupied" data-inset="true">
                    <li data-role="list-divider"><?=__('mail', 'unverifiedMails'); ?></li>
                    <?php
                    // Put all mail addresses in the list
                    foreach($mailsVerification as $mailVerification) {
                        // Validate the instance
                        if(!($mailVerification instanceof MailVerification))
                            continue;

                        echo '<li><a class="ui-btn ui-btn-icon-right ui-icon-carat-r" href="mailmanager.php?mail_verification_id=' . $mailVerification->getId() . '">' . $mailVerification->getMail() . '</a></li>';
                    }
                    ?>
                </ul>
            <?php endif; ?>

            <fieldset data-role="controlgroup" data-type="vertical">
                <a href="mailmanager.php?a=add" class="ui-btn ui-icon-plus ui-btn-icon-left"><?=__('mail', 'addMailAddress'); ?></a>
            </fieldset>
        </div>

        <?php PageFooterBuilder::create()->build(); ?>
    </div>
    <?php

} elseif(StringUtils::equals($a, 'add', false)) {
    ?>
    <div data-role="page" id="page-main">
        <?php PageHeaderBuilder::create(__('mail', 'addMail'))->setBackButton('index.php')->build(); ?>

        <div data-role="main" class="ui-content">
            <p>TODO: Add mail here</p>
        </div>

        <?php PageFooterBuilder::create()->build(); ?>
    </div>
    <?php

} else
    showErrorPage();


// Include the page bottom
require_once('bottom.php');
