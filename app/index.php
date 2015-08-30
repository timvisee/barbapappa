<?php

use app\language\LanguageManager;
use app\session\SessionManager;
use app\template\PageFooterBuilder;
use app\template\PageHeaderBuilder;
use carbon\core\util\StringUtils;

// Include the page top
require_once('top.php');

if(!SessionManager::isLoggedIn()):
?>
    <div data-role="page" id="page-login">
        <?php PageHeaderBuilder::create()->setMenuButton(true)->build(); ?>

        <div data-role="main" class="ui-content">
            <p><?=__('general', 'welcomeByApp'); ?></p><br />

            <p>
                <?php
                if(SessionManager::isLoggedIn())
                    echo '<span style="color: green;">Logged in!</span>';
                else
                    echo '<span style="color: red;">Not logged in!</span>';
                ?>
            </p><br />

            <fieldset data-role="controlgroup" data-type="vertical">
                <a href="login.php" class="ui-btn ui-icon-user ui-btn-icon-left"><?= __('account', 'login'); ?></a>
                <a href="register.php" class="ui-btn ui-icon-user ui-btn-icon-left"><?= __('account', 'register'); ?></a>
            </fieldset>
        </div>

        <?php PageFooterBuilder::create()->build(); ?>

        <div data-role="panel" id="main-panel" data-position="left" data-display="reveal" data-theme="a">
            <h3>Sidebar menu</h3>
            <p>This is the sidebar menu panel.</p><br />
            <a href="#demo-links" data-rel="close" class="ui-btn ui-shadow ui-corner-all ui-btn-a ui-icon-delete ui-btn-icon-left ui-btn-inline">Close panel</a>
            <?php
            if(!StringUtils::equals(LanguageManager::getPreferredLanguage()->getTag(), 'nl-NL'))
                echo '<a href="language.php?lang_tag=nl-NL" class="ui-btn ui-shadow ui-corner-all ui-btn-a"><img src="style/image/flag/nl.png" /></a>';
            else
                echo '<a href="language.php?lang_tag=en-US" class="ui-btn ui-corner-all ui-shadow"><img src="style/image/flag/gb.png" /></a>';
            ?>
        </div>
    </div>

<?php else: ?>

    <div data-role="page" id="page-main">
        <?php PageHeaderBuilder::create()->build(); ?>

        <div data-role="main" class="ui-content">
            <?php

            // Get the team
            $team = SessionManager::getLoggedInTeam();

            // Get the last occupied stations and the count
            $occupations = StationManager::getLastOccupiedStations(99999, $team);
            $occupationCount = sizeof($occupations);

            // Sort the occupations
            usort($occupations, function(Station $a, Station $b) {
                return $b->getCachedPoints() - $a->getCachedPoints();
            });

            // Get the cached team points
            $teamPoints = $team->getCachedPoints();

            ?>

            <center>
                <table class="ui-responsive">
                    <tr>
                        <td>My team</td>
                        <td><?=$team->getDisplayName(); ?></td>
                    </tr>
                    <tr>
                        <td>Team score</td>
                        <td><?=$teamPoints; ?> point<?=($teamPoints != 1 ? 's' : ''); ?></td>
                    </tr>
                    <tr>
                        <td>Occupations</td>
                        <td>
                            <?php
                            if($occupationCount > 0)
                                echo $occupationCount . ' station' . ($occupationCount != 1 ? 's' : '');
                            else
                                echo '<i>None</i>';
                            ?>
                        </td>
                    </tr>
                </table>
            </center>

            <br />
            <?php

            // Get the number of pictures waiting for evaluation
            $evaluationQueueSize = PictureManager::getApprovalQueueSize();

            // Get the number of pictures waiting for approval for the current team
            $approvalQueueSize = PictureManager::getApprovalQueueSize(SessionManager::getLoggedInTeam());

            // Create a picture approval notification badge
            $pictureApprovalBadge = '';
            if($evaluationQueueSize > 0)
                $pictureApprovalBadge = ' <span class="ui-li-count">' . $evaluationQueueSize . '</span>';

            // Create a picture approval notification badge
            $picturesBadge = '';
            if($approvalQueueSize > 0)
                $picturesBadge = ' <span class="ui-li-count">' . $approvalQueueSize . '</span>';

            if(SessionManager::isAdmin()): ?>
                <fieldset data-role="controlgroup" data-type="vertical">
                    <a href="approval.php" class="ui-btn ui-icon-check ui-btn-icon-left">Picture Approval<?=$pictureApprovalBadge; ?></a>
                </fieldset>
            <?php endif; ?>
            <fieldset data-role="controlgroup" data-type="vertical">
                <a href="claim.php" class="ui-btn ui-icon-plus ui-btn-icon-left">Claim Station</a>
            </fieldset>
            <fieldset data-role="controlgroup" data-type="vertical">
                <a href="stations.php" class="ui-btn ui-icon-search ui-btn-icon-left">Stations</a>
                <a href="occupations.php" class="ui-btn ui-icon-star ui-btn-icon-left">Occupations</a>
                <a href="pictures.php" class="ui-btn ui-icon-camera ui-btn-icon-left">Pictures<?=$picturesBadge; ?></a>
                <a href="teams.php" class="ui-btn ui-icon-user ui-btn-icon-left">Teams</a>
                <a href="map.php" data-transition="flow" class="ui-btn ui-icon-location ui-btn-icon-left">Map</a>
            </fieldset>
            <fieldset data-role="controlgroup" data-type="vertical">
                <a href="rules.php" class="ui-btn ui-icon-info ui-btn-icon-left">Rules & Help</a>
            </fieldset>
            <fieldset data-role="controlgroup" data-type="vertical">
                <a href="logout.php" class="ui-btn ui-icon-delete ui-btn-icon-left" data-direction="reverse">Logout</a>
            </fieldset>
        </div>

        <?php PageFooterBuilder::create()->build(); ?>
    </div>

<?php endif;

// Include the page bottom
require_once('bottom.php');
