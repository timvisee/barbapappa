<?php

use app\picture\Picture;
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

if(isset($_GET['view_all'])) {
    ?>
    <!--suppress HtmlDeprecatedTag -->
    <div data-role="page" id="page-stations">
        <?php
        PageHeaderBuilder::create('Our Occupations')->setBackButton('index.php')->
        setSuffix('<a href="map.php" class="ui-btn ui-corner-all ui-shadow ui-icon-location ui-btn-icon-right" data-transition="flow">Map</a>')->
        build();
        ?>

        <div data-role="main" class="ui-content">

            <ul class="ui-listview" data-role="listview" id="listview-stations-owned" data-inset="true">
                <?php
                // Get the last occupied stations
                $stations = StationManager::getLastOccupiedStations(99999, SessionManager::getLoggedInTeam());

                echo '<li data-role="list-divider">Our occupations: ' . sizeof($stations) . '</li>';

                // Make sure there is at least one station in the list
                if(sizeof($stations) <= 0)
                    echo '<li><i>You don\'t have any occupied stations yet, go claim a station!</i></li>';

                // Make sure there is at least one station in the list
                else
                    foreach($stations as $station) {
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
        </div>

        <?php PageFooterBuilder::create()->build(); ?>
    </div>
    <?php

} else if(!isset($_GET['station_id'])):

    ?>
    <div data-role="page" id="page-stations">
        <?php
        PageHeaderBuilder::create('Stations')->setBackButton('index.php')->
                setSuffix('<a href="map.php" class="ui-btn ui-corner-all ui-shadow ui-icon-location ui-btn-icon-right" data-transition="flow">Map</a>')->
                build();
        ?>

        <div data-role="main" class="ui-content">

            <input id="listview-stations-search" name="listview-stations-search" value="" type="search" placeholder="Search for stations..." />
            <ul id="listview-stations" class="ui-listview" data-role="listview" data-inset="true"></ul>

            <script>
                // Set up the stations list view search widget on page load
                $(document).on('pagecreate', function() {
                    createStationSearch($('#listview-stations'), $('#listview-stations-search'));
                });
            </script>

            <ul class="ui-listview" data-role="listview" id="listview-stations-last-occupied" data-inset="true">
                <li data-role="list-divider">Last occupations</li>
                <?php
                        // Get the last occupied stations
                        $stations = StationManager::getLastOccupiedStations(5);

                        // Make sure the returned list is valid
                        if($stations === null)
                            echo '<li><i>Failed to load last occupied stations.</i></li>';

                        // Make sure there is at least one station in the list
                        else if(sizeof($stations) <= 0)
                            echo '<li><i>There aren\'t any occupied stations yet.</i></li>';

                        // Make sure there is at least one station in the list
                        else
                            foreach($stations as $station) {
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

            <ul class="ui-listview" data-role="listview" id="listview-stations-owned" data-inset="true">
                <li data-role="list-divider">Our occupations</li>
                <?php
                // Get the last occupied stations
                $stations = StationManager::getLastOccupiedStations(5, SessionManager::getLoggedInTeam());

                // Make sure there is at least one station in the list
                if(sizeof($stations) <= 0)
                    echo '<li><i>You don\'t have any occupied stations yet, go claim a station!</i></li>';

                // Make sure there is at least one station in the list
                else {
                    foreach($stations as $station) {
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

                    echo '<li><a class="ui-btn ui-btn-icon-right ui-icon-carat-r" href="stations.php?view_all=1">View all...</a></li>';
                }

                ?>
            </ul>

            <ul class="ui-listview" data-role="listview" id="listview-stations-owned" data-inset="true">
                <li data-role="list-divider">High value stations</li>
                <?php
                // Get the last occupied stations
                $stations = StationManager::getHighValueStations(5);

                // Make sure there is at least one station in the list
                if(sizeof($stations) <= 0)
                    echo '<li><i>You don\'t have any occupied stations yet, go claim a station!</i></li>';

                // Make sure there is at least one station in the list
                else
                    foreach($stations as $station) {
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
        </div>

        <?php PageFooterBuilder::create()->build(); ?>
    </div>
    <?php

else:

    // Get the station ID
    $stationId = $_GET['station_id'];

    // Get the station and occupation
    $station = new Station($stationId);
    $occupation = $station->getOccupation();

    // Get the occupation team if available
    $team = null;
    if($occupation !== null)
        $team = $occupation->getTeam();

    // Get the occupation team color if available
    $teamColor = null;
    if($team !== null)
        $teamColor = $team->getColorHex();

    ?>
    <div data-role="page" id="page-stations">
        <?php PageHeaderBuilder::create($station->getNameMiddle())->setBackButton('stations.php')->build(); ?>

        <div data-role="main" class="ui-content">
            <?php

            // Create the occupation text
            $occupationText = '<i>Not yet occupied</i>';

            // Check whether the station is occupied
            if($occupation !== null) {
                // Reset the occupation text
                $occupationText = '';

                // Add an icon if a team color is determined
                if($teamColor !== null)
                    $occupationText .= '<img src="style/image/icon/16/transparent.png" class="ui-li-icon group-icon-text" style="background: #' . $teamColor . '" />';

                // Add the team name
                $occupationText .= 'Team ' . ucfirst($team->getName());
            }

            // Get the occupation count
            $occupationCount = $station->getOccupationCount();

            ?>
            <center>
                <table class="ui-responsive">
                    <tr>
                        <td>Station</td>
                        <td><?=$station->getName(); ?></td>
                    </tr>
                    <tr>
                        <td>Points</td>
                        <td><?=$station->getCachedPoints(); ?> points</td>
                    </tr>
                    <tr>
                        <td>Occupation</td>
                        <td><?=$occupationText; ?></td>
                    </tr>
                    <?php if($station->isOccupied()):  ?>
                    <tr>
                        <td>Occupied #</td>
                        <td><?=$occupationCount; ?> time<?=($occupationCount !== 1) ? 's' : ''; ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if(!$station->isReclaimable()):  ?>
                    <tr>
                        <td>Reclaimable at</td>
                        <td><?=$station->getReclaimableTime()->toString(); ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </center>

            <?php

            // Define the map pin color
            $mapPinColor = '333333';
            if($teamColor !== null)
                $mapPinColor = $teamColor;

            $longlat = $station->getGeoLongitude() . ',' . $station->getGeoLatitude();
            $staticMapUrl = 'https://api.mapbox.com/v4/timvisee.dd6f3e4f/pin-m-rail+' . $mapPinColor .'(' . $longlat . ')/' . $longlat . ',16/350x350.png?access_token=pk.eyJ1IjoidGltdmlzZWUiLCJhIjoiNzExOWJhZjExNzZlNmU1M2Y1NzFmNzU4NmUzMmIyNTYifQ.SiLLZI5JSqtBvEk_XOrPVg';

            ?>

            <br />
            <center>
                <img src="<?=$staticMapUrl; ?>" class="static-map" />
            </center>
            <br />

            <fieldset data-role="controlgroup" data-type="vertical">
                <a href="claim.php?station_id=<?=$station->getId(); ?>" class="ui-btn ui-icon-check ui-btn-icon-left<?php if(!$station->isReclaimable()) echo(' ui-state-disabled');  ?>">Claim station</a>
            </fieldset>

            <fieldset data-role="controlgroup" data-type="vertical">
                <a href="map.php?station_id=<?=$station->getId(); ?>" data-transition="flow" class="ui-btn ui-icon-location ui-btn-icon-left">View on map</a>
                <a href="geo:<?=$station->getGeoLatitude(); ?>,<?=$station->getGeoLongitude(); ?>?z=15" class="ui-btn ui-icon-navigation ui-btn-icon-left">View with app</a>
            </fieldset>

            <?php if($station->isOccupied()): ?>
            <ul class="ui-listview" data-role="listview" id="listview-approval" data-inset="true">
                <?php
                // Get the number of approved pictures
                $acceptedCount = PictureManager::getApprovedPictureCount(1, null, $station);

                echo '<li data-role="list-divider">Current occupation</li>';

                $occupationPicture = $station->getOccupation()->getPicture();

                // Get the team
                $team = $occupationPicture->getTeam();
                $teamColor = $team->getColorHex();

                // Build the team text
                $approvalTeamStr = 'Team ' . ucfirst($team->getName());

                // Add an icon if a team color is determined
                if($teamColor !== null)
                    $approvalTeamStr =
                        '<img src="style/image/icon/16/transparent.png" class="ui-li-icon group-icon-list-small" style="background: #' .
                        $teamColor . '" /> ' . $approvalTeamStr;

                // Print the list item
                echo '<li><a href="pictures.php?picture_id=' . $occupationPicture->getId() . '">';
                echo '<img src="' . $occupationPicture->getThumbnailUrl(80) . '">';
                echo '<h2>' . $station->getNameMiddle() . '</h2>';
                echo '<p>' . $approvalTeamStr . '</p>';
                echo '</a></li>';

                ?>

                <?php
                // Get the number of approved pictures
                $acceptedCount = PictureManager::getApprovedPictureCount(1, null, $station);

                // Make sure any accepted picture is available
                if($acceptedCount > 1) {
                    echo '<li data-role="list-divider">Other pictures for ' . $station->getNameMiddle() . '</li>';

                    // Get the pictures waiting for approval
                    $accepted = PictureManager::getLastApproved(99999, 1, null, $station);

                    // Define the picture index
                    $index = $acceptedCount + 1;

                    // Put each picture in the list
                    foreach($accepted as $picture) {
                        // Verify the instance
                        if(!($picture instanceof Picture))
                            continue;

                        // Decrease the picture index
                        $index--;

                        // Skip the occupation picture
                        if($picture->getId() == $occupationPicture->getId())
                            continue;

                        // Get the team
                        $team = $picture->getTeam();
                        $teamColor = $team->getColorHex();

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
                        echo '<h2>Picture ' . $index . '</h2>';
                        echo '<p>' . $approvalTeamStr . '</p>';
                        echo '</a></li>';
                    }
                }

                ?>
            </ul>
            <?php endif; ?>

            <div data-role="collapsible">
                <h4>Detailed information</h4>
                <center>
                    <table class="ui-responsive">
                        <tr>
                            <td>ID</td>
                            <td><?=$station->getId(); ?></td>
                        </tr>
                        <tr>
                            <td>Code</td>
                            <td><?=$station->getCode(); ?></td>
                        </tr>
                        <tr>
                            <td>UIC</td>
                            <td><?=$station->getUIC(); ?></td>
                        </tr>
                        <tr>
                            <td>Middle</td>
                            <td><?=$station->getNameMiddle(); ?></td>
                        </tr>
                        <tr>
                            <td>Short</td>
                            <td><?=$station->getNameShort(); ?></td>
                        </tr>
                        <tr>
                            <td>RDT URL</td>
                            <td><?=$station->getRdtUrl(); ?></td>
                        </tr>
                        <tr>
                            <td>Type (NL)</td>
                            <td><?=$station->getType(); ?></td>
                        </tr>
                        <tr>
                            <td>Latitude</td>
                            <td><?=$station->getGeoLatitude(); ?></td>
                        </tr>
                        <tr>
                            <td>Longitude</td>
                            <td><?=$station->getGeoLongitude(); ?></td>
                        </tr>
                    </table>
                </center>
            </div>

            <div data-role="popup" id="feature-unavailable">
                <p>This feature is currently unavailable.</p>
            </div>
        </div>

        <?php PageFooterBuilder::create()->build(); ?>
    </div>
    <?php

endif;

// Include the page bottom
require_once('bottom.php');