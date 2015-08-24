<?php

use app\picture\PictureManager;
use app\session\SessionManager;
use app\station\Station;
use app\station\StationManager;
use app\template\PageFooterBuilder;
use app\template\PageHeaderBuilder;

// Include the page top
require_once('top.php');

// Require the user to be logged in
requireLogin();

// Check whether all occupations should be shown
if(isset($_GET['view_all']) && $_GET['view_all'] == '1') {

    // Get all occupied stations
    $allOccupations = StationManager::getLastOccupiedStations(99999);

    ?>
    <!--suppress HtmlDeprecatedTag -->
    <div data-role="page" id="page-about">
        <?php

        // Print the page header
        PageHeaderBuilder::create('All Occupations')->setBackButton('index.php')->build();
        ?>

        <div data-role="main" class="ui-content">
            <ul class="ui-listview" data-role="listview" id="listview-stations-last-occupied" data-inset="true">
                <?php if(sizeof($allOccupations) > 0): ?>
                    <li data-role="list-divider">All occupations: <?=sizeof($allOccupations); ?></li>
                    <?php

                    // Make sure the returned list is valid
                    if($allOccupations === null)
                        echo '<li><i>Failed to load last occupied stations.</i></li>';

                    // Make sure there is at least one station in the list
                    else if(sizeof($allOccupations) <= 0)
                        echo '<li><i>There aren\'t any occupied stations yet.</i></li>';

                    // Make sure there is at least one station in the list
                    else
                        foreach($allOccupations as $station) {
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
                else:
                    echo '<li><i>There are no occupations yet.</i></li>';
                endif;
                ?>

            </ul>
        </div>

        <?php PageFooterBuilder::create()->build(); ?>
    </div>
    <?php

} else {
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

    // Get the number of approved pictures this team has
    $approvedCount = PictureManager::getApprovedPictureCount(null, $team);

    // Get all occupied stations
    $allOccupations = StationManager::getLastOccupiedStations(99999);

    ?>
    <!--suppress HtmlDeprecatedTag -->
    <div data-role="page" id="page-about">
        <?php

        // Print the page header
        PageHeaderBuilder::create('Occupations')->setBackButton('index.php')->build();
        ?>

        <div data-role="main" class="ui-content">
            <ul class="ui-listview" data-role="listview" id="listview-stations-owned" data-inset="true">
                <?php
                echo '<li data-role="list-divider">Our occupations: ' . $occupationCount . '</li>';

                // Make sure there is at least one station in the list
                if(sizeof($occupations) <= 0)
                    echo '<li><i>You don\'t have any occupied station yet, go claim a station!</i></li>';

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

            <ul class="ui-listview" data-role="listview" id="listview-stations-last-occupied" data-inset="true">
                <?php if(sizeof($allOccupations) > 0): ?>
                    <li data-role="list-divider">All occupations: <?=sizeof($allOccupations); ?></li>
                    <?php

                    // Get the last occupied stations
                    $occupations = StationManager::getLastOccupiedStations(5);

                    // Make sure the returned list is valid
                    if($occupations === null)
                        echo '<li><i>Failed to load last occupied stations.</i></li>';

                    // Make sure there is at least one station in the list
                    else if(sizeof($occupations) <= 0)
                        echo '<li><i>There aren\'t any occupied stations yet.</i></li>';

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

                    echo '<li><a class="ui-btn ui-btn-icon-right ui-icon-carat-r" href="occupations.php?view_all=1">View all...</a></li>';

                else:
                    echo '<li data-role="list-divider">All occupations</li>';
                    echo '<li><i>There are no occupations yet.</i></li>';
                endif;
                ?>

            </ul>
        </div>

        <?php PageFooterBuilder::create()->build(); ?>
    </div>
    <?php
}

// Include the page bottom
require_once('bottom.php');