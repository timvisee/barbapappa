<?php

use app\session\SessionManager;
use app\station\Station;
use app\station\StationManager;
use app\template\PageHeaderBuilder;

// Include the page top
require_once('top.php');

// Require the user to be logged in
requireLogin();

?>
<div data-role="page" id="page-map" data-unload="false">
    <?php

    // Print the page header
    PageHeaderBuilder::create('Map')->setBackButton('index.php')->build();

    // The default station
    $stationId = 214;

    // Get the last occupied station
    foreach(StationManager::getLastOccupiedStations(1, SessionManager::getLoggedInTeam()) as $station) {
        // Validate the instance
        if(!($station instanceof Station))
            continue;

        // Set the station ID
        $stationId = $station->getId();
    }

    // Get the station ID if set and make sure it's valid
    if(isset($_GET['station_id'])) {
        // Get the station ID
        $stationId = $_GET['station_id'];

        // Validate the station ID
        if(!StationManager::isStationWithId($stationId))
            showErrorPage();
    }

    // Get the station
    $station = new Station($stationId);

    ?>

    <div data-role="main" class="ui-content" style="padding: 0;">
        <div id="map" style="position:absolute; width: 100%; padding: 0; top: 44px; bottom: 0;"></div>
    </div>

    <script>
        // Initialize the map on page load
        $(document).on('pageshow', function() {
            if(getActivePageId() == 'page-map')
                initMap(<?=$station->getGeoLatitude(); ?>, <?=$station->getGeoLongitude(); ?>);
        });
    </script>
</div>
<?php

// Include the page bottom
require_once('bottom.php');