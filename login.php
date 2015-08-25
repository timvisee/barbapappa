<?php

use app\session\SessionManager;
use app\team\Team;
use app\team\TeamManager;
use app\template\PageFooterBuilder;
use app\template\PageHeaderBuilder;

// Include the page top
require_once('top.php');

$error = false;
$teamId = null;
$teamPass = null;

// Make sure the required post fields are set
if(!isset($_POST['team_id']) || !isset($_POST['team_pass']))
    $error = true;

else {
    $teamId = $_POST['team_id'];
    $teamPass = $_POST['team_pass'];

    // Make sure a team exists with this ID
    if(!TeamManager::isTeamWithId($teamId))
        $error = true;

    else
        $team = new Team($teamId);
}

// Show an error page if an error occurred
if($error):
    ?>
    <div data-role="page" id="page-main">
        <?php PageHeaderBuilder::create(__('error', 'oops'))->setBackButton('index.php')->build(); ?>

        <div data-role="main" class="ui-content">
            <p><?=__('error', 'errorOccurred'); ?><br /><?=__('error', 'goBackTryAgain'); ?></p><br />

            <fieldset data-role="controlgroup" data-type="vertical">
                <a href="index.php" data-ajax="false" data-rel="back" class="ui-btn ui-icon-back ui-btn-icon-left" data-direction="reverse"><?=__('navigation', 'goBack'); ?></a>
            </fieldset>
        </div>

        <?php PageFooterBuilder::create()->build(); ?>
    </div>
    <?php
else:

    // Validate the password
    $passCorrect = $team->isPassword($teamPass);

    if(!$passCorrect):
        ?>
        <div data-role="page" id="page-main">
            <?php PageHeaderBuilder::create(__('error', 'oops'))->setBackButton('index.php')->build(); ?>

            <div data-role="main" class="ui-content">
                <p><?=__('login', 'usernameOrPasswordIncorrect'); ?><br /><?=__('error', 'goBackTryAgain'); ?></p><br />

                <fieldset data-role="controlgroup" data-type="vertical">
                    <a href="index.php" data-rel="back" class="ui-btn ui-icon-back ui-btn-icon-left" data-direction="reverse"><?=__('navigation', 'goBack'); ?></a>
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
                    <?=__('general', 'welcome'); ?>!<br /><?=__('login', 'loginSuccess'); ?>

                    <!-- TODO: Add some additional description here! -->
                </p>
                <br />

                <fieldset data-role="controlgroup" data-type="vertical">
                    <a href="index.php" data-ajax="false" class="ui-btn ui-icon-carat-r ui-btn-icon-left"><?=__('navigation', 'continue'); ?></a>
                </fieldset>
            </div>

            <?php PageFooterBuilder::create()->build(); ?>
        </div>
        <?php

    endif;
endif;

// Include the page bottom
require_once('bottom.php');