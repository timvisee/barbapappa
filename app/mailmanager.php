<?php

use app\mail\Mail;
use app\mail\MailManager;
use app\mail\verification\MailVerification;
use app\mail\verification\MailVerificationManager;
use app\session\SessionManager;
use app\template\PageFooterBuilder;
use app\template\PageHeaderBuilder;
use app\util\AccountUtils;
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

if(StringUtils::equals($a, 'add', false)) {

    // TODO: Make sure this user can add another email address (and that he doesn't reach his maximum count)

    if(!isset($_POST['mail'])) {
        ?>
        <div data-role="page" id="page-register" data-unload="false">
            <?php PageHeaderBuilder::create(__('mail', 'addMail'))->setBackButton('index.php')->build(); ?>

            <div data-role="main" class="ui-content">
                <p><?= __('mail', 'fillInMailBellowWillSendVerification'); ?></p><br />

                <form method="POST" action="mailmanager.php?a=add&step=2">
                    <input type="text" name="mail" value="" placeholder="<?= __('account', 'mail'); ?>" />

                    <input type="submit" value="<?= __('mail', 'addMailAddress'); ?>"
                           class="ui-btn ui-icon-lock ui-btn-icon-right" />
                </form>
            </div>

            <?php PageFooterBuilder::create()->build(); ?>
        </div>
        <?php

    } else {
        // Get the mail address
        $mailAddress = trim($_POST['mail']);

        // Validate the mail address
        if(!AccountUtils::isValidMail($mailAddress))
            showErrorPage(__('register', 'invalidMail'));

        // Make sure the mail isn't in use already
        if(MailManager::isMailWithMail($mailAddress))
            showErrorPage(__('register', 'mailAlreadyInUse'));

        // Get all mail verifications for this mail address
        $mailVerifications = MailVerificationManager::getMailVerificationsWithMail($mailAddress);
        foreach($mailVerifications as $mailVerification) {
            // Validate the instance
            if(!($mailVerification instanceof MailVerification))
                continue;

            // Make sure this mail address isn't registered for the current user
            if($mailVerification->getUser()->getId() == $user->getId())
                showErrorPage(__('mail', 'youHaveAlreadyAddedThisMail'));
        }

        // Create the mail verification and send a verification message
        MailVerificationManager::createMailVerification($user, $mailAddress, null);

        ?>
        <div data-role="page" id="page-register" data-unload="false">
            <?php PageHeaderBuilder::create(__('mail', 'addMail'))->build(); ?>

            <div data-role="main" class="ui-content">
                <p>
                    <?= __('mail', 'addedMailSuccessfullyMustVerify'); ?>
                </p><br />

                <fieldset data-role="controlgroup" data-type="vertical">
                    <a href="index.php" data-ajax="false" class="ui-btn ui-icon-home ui-btn-icon-left" data-direction="reverse"><?=__('navigation', 'goToFrontPage'); ?></a>
                </fieldset>
            </div>

            <?php PageFooterBuilder::create()->build(); ?>
        </div>
        <?php
    }

} elseif(StringUtils::equals($a, 'change', false)) {

    // Get the mail or mail verification
    $oldMail = null;
    $oldMailAddress = '';
    $nextFormParams = '';
    if(isset($_GET['mail_id'])) {
        // Get the mail ID
        $mailId = $_GET['mail_id'];

        // Verify the mail ID
        if(!MailManager::isMailWithId($mailId))
            showErrorPage();

        // Get the mail as an object
        $oldMail = new Mail($mailId);

        // Get the old mail address
        $oldMailAddress = $oldMail->getMail();

        // Set the next form params
        $nextFormParams = 'mail_id=' . $mailId;

    } else if(isset($_GET['mail_verification_id'])) {
        // Get the mail verification ID
        $mailVerificationId = $_GET['mail_verification_id'];

        // Verify the mail verification ID
        if(!MailVerificationManager::isMailVerificationWithId($mailVerificationId))
            showErrorPage();

        // Get the mail verification as an object
        $oldMail = new MailVerification($mailVerificationId);

        // Get the old mail address
        $oldMailAddress = $oldMail->getMail();

        // Set the next form params
        $nextFormParams = 'mail_verification_id=' . $mailVerificationId;

    } else
        showErrorPage();

    // Make sure the old mail user is the same as the user of the current session
    if($oldMailUser->getId() != $user->getId())
        showErrorPage();

    if(!isset($_POST['mail'])) {
        ?>
        <div data-role="page" id="page-register" data-unload="false">
            <?php PageHeaderBuilder::create(__('mail', 'changeMail'))->setBackButton('index.php')->build(); ?>

            <div data-role="main" class="ui-content">
                <p><?= __('mail', 'fillInMailBellowToChange'); ?></p><br />

                <center>
                    <table class="ui-responsive">
                        <tr>
                            <td><?= __('general', 'current'); ?></td>
                            <td><?= $oldMailAddress; ?></td>
                        </tr>
                    </table>
                </center>

                <form method="POST" action="mailmanager.php?a=change&step=2&<?= $nextFormParams; ?>">
                    <input type="text" name="mail" value="" placeholder="<?= __('account', 'mail'); ?>" />

                    <input type="submit" value="<?= __('navigation', 'continue'); ?>"
                           class="ui-btn ui-icon-lock ui-btn-icon-right" />
                </form>
            </div>

            <?php PageFooterBuilder::create()->build(); ?>
        </div>
        <?php

    } elseif(!isset($_POST['agree'])) {
        // Get the mail address
        $mailAddress = trim($_POST['mail']);

        // Validate the mail address
        if(!AccountUtils::isValidMail($mailAddress))
            showErrorPage(__('register', 'invalidMail'));

        // Make sure the mail isn't in use already
        if(MailManager::isMailWithMail($mailAddress))
            showErrorPage(__('register', 'mailAlreadyInUse'));

        // Get all mail verifications for this mail address
        $mailVerifications = MailVerificationManager::getMailVerificationsWithMail($mailAddress);
        foreach($mailVerifications as $mailVerification) {
            // Validate the instance
            if(!($mailVerification instanceof MailVerification))
                continue;

            // Make sure this mail address isn't registered for the current user
            if($mailVerification->getUser()->getId() == $user->getId())
                showErrorPage(__('mail', 'youHaveAlreadyAddedThisMail'));
        }

        ?>
        <div data-role="page" id="page-register" data-unload="false">
            <?php PageHeaderBuilder::create(__('mail', 'changeMail'))->setBackButton('index.php')->build(); ?>

            <div data-role="main" class="ui-content">
                <center>
                    <table class="ui-responsive">
                        <tr>
                            <td><?= __('general', 'current'); ?></td>
                            <td><?= $oldMailAddress; ?></td>
                        </tr>
                        <tr>
                            <td><?= __('general', 'new'); ?></td>
                            <td><?= $mailAddress; ?></td>
                        </tr>
                    </table>
                </center>
                <br />

                <form method="POST" action="mailmanager.php?a=change&step=3&<?= $nextFormParams; ?>">
                    <input type="hidden" name="mail" value="<?= $mailAddress; ?>" />

                    <label for="agree"><?= __('mail', 'youSureRemoveOldMailReplaceWithNew'); ?></label>
                    <select id="agree" name="agree" data-role="slider">
                        <option value="0"><?= __('general', 'acceptanceNo'); ?></option>
                        <option value="1"><?= __('general', 'acceptanceYes'); ?></option>
                    </select>

                    <input type="submit" value="<?= __('mail', 'changeMailAddress'); ?>"
                           class="ui-btn ui-icon-lock ui-btn-icon-right" />
                </form>
            </div>

            <?php PageFooterBuilder::create()->build(); ?>
        </div>
        <?php

    } else {
        // Get the mail address
        $mailAddress = trim($_POST['mail']);

        // Validate the mail address
        if(!AccountUtils::isValidMail($mailAddress))
            showErrorPage(__('register', 'invalidMail'));

        // Make sure the mail isn't in use already
        if(MailManager::isMailWithMail($mailAddress))
            showErrorPage(__('register', 'mailAlreadyInUse'));

        // Get all mail verifications for this mail address
        $mailVerifications = MailVerificationManager::getMailVerificationsWithMail($mailAddress);
        foreach($mailVerifications as $mailVerification) {
            // Validate the instance
            if(!($mailVerification instanceof MailVerification))
                continue;

            // Make sure this mail address isn't registered for the current user
            if($mailVerification->getUser()->getId() == $user->getId())
                showErrorPage(__('mail', 'youHaveAlreadyAddedThisMail'));
        }

        // Make sure the user agree's to change the mail address
        $agree = $_POST['agree'];
        if($agree != 1)
            showErrorPage(__('mail', 'mustAgreeToChangeMail'));

        // Determine whether to use a previous address when creating the new mail verification
        $previousAddress = null;
        if($oldMail instanceof Mail)
            $previousAddress = $oldMail;

        // Delete the old mail address
        if($oldMail instanceof Mail) {
            $oldMail->delete();
        } elseif($oldMail instanceof MailVerification)
            $oldMail->delete();
        else
            showErrorPage();

        // Clear the mail instance
        $oldMail = null;

        // Create the mail verification and send a verification message
        MailVerificationManager::createMailVerification($user, $mailAddress, $previousAddress);

        ?>
        <div data-role="page" id="page-register" data-unload="false">
            <?php PageHeaderBuilder::create(__('mail', 'changeMail'))->build(); ?>

            <div data-role="main" class="ui-content">
                <p>
                    <?= __('mail', 'changedMailSuccessfullyMustVerify'); ?>
                </p><br />

                <fieldset data-role="controlgroup" data-type="vertical">
                    <a href="index.php" data-ajax="false" class="ui-btn ui-icon-home ui-btn-icon-left"
                       data-direction="reverse"><?= __('navigation', 'goToFrontPage'); ?></a>
                </fieldset>
            </div>

            <?php PageFooterBuilder::create()->build(); ?>
        </div>
        <?php
    }

} elseif(StringUtils::equals($a, 'delete', false)) {

    // Get the mail or mail verification
    $oldMail = null;
    $oldMailAddress = '';
    $oldMailUser = null;
    $nextFormParams = '';
    if(isset($_GET['mail_id'])) {
        // Get the mail ID
        $mailId = $_GET['mail_id'];

        // Verify the mail ID
        if(!MailManager::isMailWithId($mailId))
            showErrorPage();

        // Get the mail as an object
        $oldMail = new Mail($mailId);

        // Get the old mail address
        $oldMailAddress = $oldMail->getMail();

        // Get the old mail user
        $oldMailUser = $oldMail->getUser();

        // Set the next form params
        $nextFormParams = 'mail_id=' . $mailId;

    } else if(isset($_GET['mail_verification_id'])) {
        // Get the mail verification ID
        $mailVerificationId = $_GET['mail_verification_id'];

        // Verify the mail verification ID
        if(!MailVerificationManager::isMailVerificationWithId($mailVerificationId))
            showErrorPage();

        // Get the mail verification as an object
        $oldMail = new MailVerification($mailVerificationId);

        // Get the old mail address
        $oldMailAddress = $oldMail->getMail();

        // Get the old mail user
        $oldMailUser = $oldMail->getUser();

        // Set the next form params
        $nextFormParams = 'mail_verification_id=' . $mailVerificationId;

    } else
        showErrorPage();

    // Make sure the old mail user is the same as the user of the current session
    if($oldMailUser->getId() != $user->getId())
        showErrorPage();

    if(!isset($_POST['agree'])) {
        ?>
        <div data-role="page" id="page-register" data-unload="false">
            <?php PageHeaderBuilder::create(__('mail', 'deleteMail'))->setBackButton('index.php')->build(); ?>

            <div data-role="main" class="ui-content">
                <p><?=__('mail', 'aboutToRemoveMailNotReversible'); ?></p><br />

                <form method="POST" action="mailmanager.php?a=delete&step=2&<?= $nextFormParams; ?>">
                    <center>
                        <table class="ui-responsive">
                            <tr>
                                <td><?= __('account', 'mail'); ?></td>
                                <td><?= $oldMailAddress; ?></td>
                            </tr>
                        </table>
                    </center>
                    <br />

                    <label for="agree"><?= __('mail', 'youSureRemoveEmail'); ?></label>
                    <select id="agree" name="agree" data-role="slider">
                        <option value="0"><?= __('general', 'acceptanceNo'); ?></option>
                        <option value="1"><?= __('general', 'acceptanceYes'); ?></option>
                    </select>

                    <input type="submit" value="<?= __('mail', 'deleteMailAddress'); ?>"
                           class="ui-btn ui-icon-lock ui-btn-icon-right" />
                </form>
            </div>

            <?php PageFooterBuilder::create()->build(); ?>
        </div>
        <?php

    } else {
        // Make sure the user agree's to change the mail address
        $agree = $_POST['agree'];
        if($agree != 1)
            showErrorPage(__('mail', 'mustAgreeToRemoveMail'));

        // Determine whether to use a previous address when creating the new mail verification
        $previousAddress = null;
        if($oldMail instanceof Mail)
            $previousAddress = $oldMail;

        // Delete the old mail address
        if($oldMail instanceof Mail) {
            $oldMail->delete();
        } elseif($oldMail instanceof MailVerification)
            $oldMail->delete();
        else
            showErrorPage();

        // Clear the mail instance
        $oldMail = null;

        ?>
        <div data-role="page" id="page-register" data-unload="false">
            <?php PageHeaderBuilder::create(__('mail', 'deleteMail'))->build(); ?>

            <div data-role="main" class="ui-content">
                <p>
                    <?= __('mail', 'removedMailSuccessfully'); ?>
                </p><br />

                <fieldset data-role="controlgroup" data-type="vertical">
                    <a href="index.php" data-ajax="false" class="ui-btn ui-icon-home ui-btn-icon-left"
                       data-direction="reverse"><?= __('navigation', 'goToFrontPage'); ?></a>
                </fieldset>
            </div>

            <?php PageFooterBuilder::create()->build(); ?>
        </div>
        <?php
    }

} elseif(isset($_GET['mail_id'])) {
    // Get the mail ID
    $mailId = $_GET['mail_id'];

    // Make sure the ID is valid
    if(!MailManager::isMailWithId($mailId))
        showErrorPage();

    // Get the mail object
    $oldMail = new Mail($mailId);

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
                    <td><?=$oldMail->getMail(); ?></td>
                </tr>
                <tr>
                    <td><?=__('account', 'verified'); ?></td>
                    <td><span style="color: green;"><?=__('general', 'acceptanceYes'); ?>!</span></td>
                </tr>
                <tr>
                    <td><?=__('mail', 'verifiedAt'); ?></td>
                    <td><?=$oldMail->getVerificationDateTime(); ?></td>
                </tr>
            </table>
            <br />

            <p>
                <?=__('mail', 'pressButtonToChangeOrDelete'); ?>
            </p><br />

            <fieldset data-role="controlgroup" data-type="vertical">
                <a href="mailmanager.php?mail_id=<?=$mailId; ?>&a=change" class="ui-btn ui-icon-edit ui-btn-icon-left"><?=__('mail', 'changeMailAddress'); ?></a>
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
                <a href="mailmanager.php?mail_verification_id=<?=$mailVerificationId; ?>&a=change" class="ui-btn ui-icon-edit ui-btn-icon-left"><?=__('mail', 'changeMailAddress'); ?></a>
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

                        echo '<li><a class="ui-btn ui-btn-icon-right ui-icon-carat-r" href="mailmanager.php?mail_id=' . $mailVerification->getId() . '">' . $mailVerification->getMail() . '<span class="ui-li-count">Primary</span></a></li>';
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
