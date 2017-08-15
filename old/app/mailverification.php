<?php

use app\mail\verification\MailVerification;
use app\mail\verification\MailVerificationManager;
use app\session\SessionManager;
use app\template\PageFooterBuilder;
use app\template\PageHeaderBuilder;
use app\template\PageSidebarBuilder;
use carbon\core\util\StringUtils;

// Include the page top
require_once('top.php');

// Get the action parameter if set
$a = null;
if(isset($_GET['a']))
    $a = trim($_GET['a']);

// Resend verification mail action
if(StringUtils::equals($a, 'resend', false)) {
    // TODO: Use tokens here!

    // Make sure the user is logged in
    if(!SessionManager::isLoggedIn())
        showErrorPage(__('login', 'mustBeLoggedInToViewThisPage'));

    // Get the user
    $user = SessionManager::getLoggedInUser();

    // TODO: Make sure the user has any mails waiting for verification!

    // Get the mail address to resend the verification for
    $mailVerify = null;
    if($_GET['mail_verification_id']) {
        // Get the mail ID
        $mailId = trim($_GET['mail_verification_id']);

        // Get the mail verification object if the mail ID is valid
        if(!MailVerificationManager::isMailVerificationWithId($mailId))
            showErrorPage();
        $mailVerify = new MailVerification($mailId);

        // Make sure the mail waiting for verification is from the current user
        if($mailVerify->getUser()->getId() != $user->getId())
            showErrorPage();
    }

    // Get all mails waiting for verification
    $mails = MailVerificationManager::getMailVerificationsFromUser($user);

    // Make sure the user has any mails waiting for verification
    if(sizeof($mails) == 0) {
        ?>
        <div data-role="page" id="page-main">
            <?php PageHeaderBuilder::create(__('mail', 'mailVerification'))->setBackButton('index.php')->build(); ?>

            <div data-role="main" class="ui-content">
                <p>
                    <?=__('mail', 'noMailToVerifyAddWithManage'); ?>
                </p><br />

                <fieldset data-role="controlgroup" data-type="vertical">
                    <a href="mailmanager.php" class="ui-btn ui-icon-edit ui-btn-icon-left"><?=__('mail', 'manageMail'); ?></a>
                </fieldset>
            </div>

            <?php
            // Build the footer and sidebar
            PageFooterBuilder::create()->build();
            PageSidebarBuilder::create()->build();
            ?>
        </div>
        <?php

    } else {
        // Make the user select a mail waiting for verification if none has been selected yet
        if(!($mailVerify instanceof MailVerification)) {
            ?>
            <div data-role="page" id="page-login">
                <?php PageHeaderBuilder::create(__('mail', 'mailVerification'))->setBackButton('index.php')->build(); ?>

                <div data-role="main" class="ui-content">
                    <p><?=__('mail', 'selectMailToResendActivationFor'); ?></p><br />

                    <form method="GET" action="" enctype="multipart/form-data" data-ajax="true">
                        <input type="hidden" name="a" value="resend" />

                        <fieldset class="ui-controlgroup ui-controlgroup-vertical ui-corner-all" id="mail-verification-list"
                                  data-role="controlgroup" data-type="vertical">
                            <div class="ui-controlgroup-controls ">
                                <li class="ui-li ui-li-divider ui-btn ui-bar-a ui-corner-top ui-btn-up-undefined ui-first-child" data-role="list-divider">
                                    <?=__('mail', 'mails'); ?>
                                </li>
                                <?php
                                // Print each mail
                                foreach($mails as $mail) {
                                    // Validate the instance
                                    if(!($mail instanceof MailVerification))
                                        continue;

                                    // Print the entry
                                    echo '<div class="ui-radio">';
                                    echo '<label class="ui-btn ui-corner-all ui-btn-inherit ui-btn-icon-left ui-radio-off" for="mail_' . $mail->getId() . '">';
                                    echo $mail->getMail();
                                    echo '</label>';
                                    echo '<input name="mail_verification_id" id="mail_' . $mail->getId() . '" value="' . $mail->getId() . '" type="radio">';
                                    echo '</div>';
                                }
                                ?>
                            </div>
                        </fieldset>

                        <script>
                            // Set up the stations list view search widget on page load
                            $(document).on('pagecreate', function() {
                                // Get the input container and button
                                var inputContainer = $('#station-input-container');
                                var buttonContinue = $('input[type=submit]');

                                /**
                                 * Show the continue button.
                                 */
                                function showContinue() {
                                    // Show the input box
                                    inputContainer.stop().slideDown();

                                    // Enable the input boxes
                                    buttonContinue.removeClass('ui-state-disabled');
                                }

                                /**
                                 * Hide the continue button.
                                 */
                                function hideContinue() {
                                    // Hide the input box
                                    inputContainer.stop().slideUp();

                                    // Disable the input boxes
                                    buttonContinue.addClass('ui-state-disabled');
                                }

                                // Show or hide the input button if any is selected
                                if(!$("input[name='mail_verification_id']:checked").val())
                                    inputContainer.hide();
                                else
                                    inputContainer.show();

                                // Create an event handler for each radio button
                                $("input[name='mail_verification_id']").change(function() {
                                    // Show or hide the input button if any is selected
                                    if(!$("input[name='mail_verification_id']:checked").val())
                                        hideContinue();
                                    else
                                        showContinue();
                                });
                            });
                        </script>

                        <div id="station-input-container" style="padding-top: 1px; display: none;">
                            <input value="<?=__('mail', 'resendVerification'); ?>" class="ui-btn ui-icon-lock ui-btn-icon-right ui-state-disabled" type="submit">
                        </div>
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
            // Send the verification message
            $mailVerify->sendVerificationMessage();

            ?>
            <div data-role="page" id="page-login">
                <?php PageHeaderBuilder::create(__('mail', 'mailVerification'))->build(); ?>

                <div data-role="main" class="ui-content">
                    <p><?=__('mail', 'mailVerificationSend'); ?></p><br />

                    <fieldset data-role="controlgroup" data-type="vertical">
                        <a href="index.php" data-ajax="false" class="ui-btn ui-icon-home ui-btn-icon-left" data-direction="reverse"><?=__('navigation', 'goToFrontPage'); ?></a>
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
    }

} elseif(StringUtils::equals($a, 'verify', false)) {
    // Make sure the verification key is set
    if(!isset($_GET['key']))
        showErrorPage();

    // Get and trim the key
    $key = trim($_GET['key']);

    // Make sure the key is valid
    if(!MailVerificationManager::isMailVerificationWithKey($key))
        showErrorPage();

    // Get the mail verification
    $mailVerification = MailVerificationManager::getMailVerificationWithKey($key);
    if($mailVerification == null)
        showErrorPage();

    // Get the mail verification user
    $mailVerificationUser = $mailVerification->getUser();
    $mailVerificationAddress = $mailVerification->getMail();

    // Verify the mail address and clear the instance
    $mailVerification->verify();
    $mailVerification = null;

    ?>
    <div data-role="page" id="page-login">
        <?php PageHeaderBuilder::create()->build(); ?>

        <div data-role="main" class="ui-content">
            <p><?=__('general', 'welcomeBack'); ?> <?=$mailVerificationUser->getFullName(); ?>!<br />
            <br />
            <?=__('mail', 'mailVerified'); ?></p><br />

            <center>
                <table class="ui-responsive">
                    <tr>
                        <td><?=__('account', 'mail'); ?></td>
                        <td><?=$mailVerificationAddress; ?></td>
                    </tr>
                </table>
            </center>
            <br />

            <fieldset data-role="controlgroup" data-type="vertical">
                <?php if(!SessionManager::isLoggedIn()): ?>
                    <a href="login.php?user=<?=$mailVerificationAddress; ?>&back=0" data-ajax="false" class="ui-btn ui-icon-user ui-btn-icon-left"><?=__('account', 'login'); ?></a>
                    <a href="index.php" data-ajax="false" class="ui-btn ui-icon-home ui-btn-icon-left"><?=__('navigation', 'goToFrontPage'); ?></a>
                <?php else: ?>
                    <a href="index.php" data-ajax="false" class="ui-btn ui-icon-home ui-btn-icon-left"><?=__('navigation', 'goToMyAccount'); ?></a>
                <?php endif; ?>
            </fieldset>
        </div>

        <?php
        // Build the footer and sidebar
        PageFooterBuilder::create()->build();
        PageSidebarBuilder::create()->build();
        ?>
    </div>
    <?php

} else
    showErrorPage();

// Include the page bottom
require_once('bottom.php');
