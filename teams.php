<?php

use app\picture\Picture;
use app\picture\PictureManager;
use app\session\SessionManager;
use app\station\Station;
use app\station\StationManager;
use app\team\Team;
use app\team\TeamManager;
use app\template\PageFooterBuilder;
use app\template\PageHeaderBuilder;

// Include the page top
require_once('top.php');

// Require the user to be logged in
requireLogin();

if(isset($_GET['team_id'])) {

    // Get the team iD
    $teamId = $_GET['team_id'];

    // Make sure the team ID is valid
    if(!TeamManager::isTeamWithId($teamId))
        showErrorPage();

    // Get the team instance
    $team = new Team($teamId);

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
    <!--suppress HtmlDeprecatedTag -->
    <div data-role="page" id="page-about">
        <?php

        // Print the page header
        PageHeaderBuilder::create($team->getDisplayName(false, false))->setBackButton('index.php')->build();

        // Get the number of approved pictures this team has
        $approvedCount = PictureManager::getApprovedPictureCount(null, $team);

        ?>

        <div data-role="main" class="ui-content">
            <center>
                <table class="ui-responsive">
                    <tr>
                        <td>Team</td>
                        <td><?=$team->getDisplayName(); ?></td>
                    </tr>
                    <tr>
                        <td>Total score</td>
                        <td><?=$teamPoints; ?> point<?=($teamPoints != 1 ? 's' : ''); ?></td>
                    </tr>
                    <tr>
                        <td>Occupations</td>
                        <td><?=$occupationCount; ?> station<?=($occupationCount != 1 ? 's' : ''); ?></td>
                    </tr>
                    <tr>
                        <td>Approved pictures</td>
                        <td><?=$approvedCount; ?> picture<?=($approvedCount != 1 ? 's' : ''); ?></td>
                    </tr>
                </table>
            </center>

            <ul class="ui-listview" data-role="listview" id="listview-stations-owned" data-inset="true">
                <?php
                echo '<li data-role="list-divider">Occupations</li>';

                // Make sure there is at least one station in the list
                if(sizeof($occupations) <= 0)
                    echo '<li><i>This team doesn\'t have any occupied stations yet, go claim a station!</i></li>';

                // Make sure there is at least one station in the list
                else
                    foreach($occupations as $station) {
                        // Validate the instance
                        if(!($station instanceof Station))
                            continue;

                        // Get the occupation color for the station
                        $stationColor = $station->getStationColor();

                        // Build the occupation color style
                        $iconStyle = '';
                        if($stationColor !== null)
                            $iconStyle = ' style="background: #' . $stationColor . ';"';

                        echo '<li>';
                        echo '<a class="ui-btn ui-btn-icon-right ui-icon-carat-r" href="stations.php?station_id=' . $station->getId() . '">';
                        echo '<img src="style/image/icon/16/transparent.png" class="ui-li-icon group-icon-list"' . $iconStyle . ' />';
                        echo $station->getName();
                        echo ' <span class="ui-li-count">' . $station->getCachedPoints() . '</span>';
                        echo '</a></li>';
                    }
                ?>
            </ul>

            <?php

            // Get the approval queue size for the team
            $queueSize = PictureManager::getApprovalQueueSize($team);

            if($queueSize > 0): ?>
                <ul class="ui-listview" data-role="listview" data-inset="true">
                    <?php
                    // Get the queue
                    $queue = PictureManager::getApprovalQueue($team);

                    echo '<li data-role="list-divider">Pictures waiting for approval: ' . $queueSize . '</li>';

                    // Put each picture in the list
                    foreach($queue as $picture) {
                        // Verify the instance
                        if(!($picture instanceof Picture))
                            continue;

                        // Get the team
                        $teamColor = $picture->getTeam()->getColorHex();

                        // Build the team text
                        $approvalTeamStr = 'Team ' . ucfirst($team->getName());

                        // Add an icon if a team color is determined
                        if($teamColor !== null)
                            $approvalTeamStr =
                                '<img src="style/image/icon/16/transparent.png" class="ui-li-icon group-icon-list-small" style="background: #' .
                                $teamColor . '" /> ' . $approvalTeamStr;

                        // Print the list item
                        echo '<li><a href="pictures.php?picture_id=' . $picture->getId() . '">';
                        echo '<img src="' . $picture->getThumbnailUrl(80) . '">';
                        echo '<h2>' . $picture->getStation()->getNameMiddle() . '</h2>';
                        echo '<p>' . $approvalTeamStr . '</p>';
                        echo '</a></li>';
                    }
                    ?>
                </ul>
            <?php endif; ?>

            <ul class="ui-listview" data-role="listview" data-inset="true">
                <?php
                // Get the number of approved pictures
                $approvalCount = PictureManager::getApprovedPictureCount(1, $team);

                echo '<li data-role="list-divider">Accepted pictures</li>';

                if($approvalCount > 0) {
                    // Get the pictures waiting for approval
                    $approving = PictureManager::getLastApproved(5, 1, $team);

                    // Put each picture in the list
                    foreach($approving as $picture) {
                        // Verify the instance
                        if(!($picture instanceof Picture))
                            continue;

                        // Get the team
                        $teamColor = $picture->getTeam()->getColorHex();

                        // Build the team text
                        $approvalTeamStr = 'Team ' . ucfirst($team->getName());

                        // Add an icon if a team color is determined
                        if($teamColor !== null)
                            $approvalTeamStr =
                                '<img src="style/image/icon/16/transparent.png" class="ui-li-icon group-icon-list-small" style="background: #' .
                                $teamColor . '" /> ' . $approvalTeamStr;

                        // Print the list item
                        echo '<li><a href="pictures.php?picture_id=' . $picture->getId() . '">';
                        echo '<img src="' . $picture->getThumbnailUrl(80) . '">';
                        echo '<h2>' . $picture->getStation()->getNameMiddle() . '</h2>';
                        echo '<p>' . $approvalTeamStr . '</p>';
                        echo '</a></li>';
                    }

                    echo '<li><a href="pictures.php?picture_status=1&team_id=' . $teamId . '">View all...</a></li>';

                } else
                    echo '<li><i>This team doesn\'t have any accepted pictures yet, go claim a station!</i></li>';

                // Get the number of approval pictures
                $approvalCount = PictureManager::getApprovedPictureCount(2, $team);

                echo '<li data-role="list-divider">Rejected pictures</li>';

                if($approvalCount > 0) {
                    // Get the pictures waiting for approval
                    $approving = PictureManager::getLastApproved(5, 2, $team);

                    // Put each picture in the list
                    foreach($approving as $picture) {
                        // Verify the instance
                        if(!($picture instanceof Picture))
                            continue;

                        // Get the team
                        $teamColor = $picture->getTeam()->getColorHex();

                        // Build the team text
                        $approvalTeamStr = 'Team ' . ucfirst($picture->getTeam()->getName());

                        // Add an icon if a team color is determined
                        if($teamColor !== null)
                            $approvalTeamStr =
                                '<img src="style/image/icon/16/transparent.png" class="ui-li-icon group-icon-list-small" style="background: #' .
                                $teamColor . '" /> ' . $approvalTeamStr;

                        // Print the list item
                        echo '<li><a href="pictures.php?picture_id=' . $picture->getId() . '">';
                        echo '<img src="' . $picture->getThumbnailUrl(80) . '">';
                        echo '<h2>' . $picture->getStation()->getNameMiddle() . '</h2>';
                        echo '<p>' . $approvalTeamStr . '</p>';
                        echo '</a></li>';
                    }

                    echo '<li><a href="pictures.php?picture_status=2&team_id=' . $teamId . '">View all...</a></li>';

                } else
                    echo '<li><i>This team doesn\'t have any rejected pictures yet, keep it that way!</i></li>';

                ?>
            </ul>
        </div>

        <?php PageFooterBuilder::create()->build(); ?>
    </div>
    <?php

} else {

    ?>
    <div data-role="page" id="page-about">
        <?php PageHeaderBuilder::create('Teams')->setBackButton('index.php')->build(); ?>

        <div data-role="main" class="ui-content">
            <ul class="ui-listview" data-role="listview" id="listview-teams" data-inset="true">
                <li data-role="list-divider">My Team</li>

                <?php

                // Get the currently logged in team
                $myTeam = SessionManager::getLoggedInTeam();

                // Print the team item
                echo '<li>';
                echo '<a href="teams.php?team_id=' . $myTeam->getId() . '">';
                echo $myTeam->getDisplayName();
                echo ' <span class="ui-li-count">' . $myTeam->getCachedPoints() . '</span>';
                echo '</a></li>';

                // Print the other teams header
                echo '<li data-role="list-divider">Other Teams</li>';

                // Get the number of teams available
                $teamCount = TeamManager::getTeamCount();

                // Make sure enough teams are available
                if ($teamCount > 0) {
                    // Get the list of available teams
                    $teams = TeamManager::getTeams(true);

                    // Print all teams
                    foreach ($teams as $team) {
                        // Verify the instance
                        if (!($team instanceof Team))
                            continue;

                        // Skip the users team
                        if ($team->getId() == $myTeam->getId())
                            continue;

                        // Print the team name
                        echo '<li>';
                        echo '<a href="teams.php?team_id=' . $team->getId() . '">';
                        echo $team->getDisplayName();
                        echo ' <span class="ui-li-count">' . $team->getCachedPoints() . '</span>';
                        echo '</a></li>';
                    }

                } else
                    echo '<li><i>No teams available...</i></li>';

                ?>
            </ul>
        </div>

        <?php PageFooterBuilder::create()->build(); ?>
    </div>
    <?php
}

// Include the page bottom
require_once('bottom.php');