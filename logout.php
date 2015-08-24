<?php

use app\session\SessionManager;
use app\template\PageFooterBuilder;
use app\template\PageHeaderBuilder;

// Include the page top
require_once('top.php');

if(!SessionManager::isLoggedIn()):
    ?>
    <div data-role="page" id="page-main">
        <?php PageHeaderBuilder::create('Whoops!')->build(); ?>

        <div data-role="main" class="ui-content">
            <p>Whoops! You're logged out already.<br />Please go back to the front page if you would like to login again.</p><br />

            <fieldset data-role="controlgroup" data-type="vertical">
                <a href="index.php" data-ajax="false" class="ui-btn ui-icon-home ui-btn-icon-left" data-direction="reverse">Go to Front Page</a>
            </fieldset>
        </div>

        <?php PageFooterBuilder::create()->build(); ?>
    </div>
    <?php

else:

    // Reset the current session
    SessionManager::logoutSession();

    ?>
    <div data-role="page" id="page-main">
        <?php PageHeaderBuilder::create()->build(); ?>

        <div data-role="main" class="ui-content">
            <p>You've been logged out successfully.</p><br />

            <fieldset data-role="controlgroup" data-type="vertical">
                <a href="index.php" data-ajax="false" class="ui-btn ui-icon-home ui-btn-icon-left" data-direction="reverse">Go to Front Page</a>
            </fieldset>
        </div>

        <?php PageFooterBuilder::create()->build(); ?>
    </div>
    <?php

endif;

// Include the page bottom
require_once('bottom.php');