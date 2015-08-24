<?php

use app\station\Station;
use app\station\StationManager;

// Initialize the app
require_once('../app/init.php');



/**
 * Return an error message.
 *
 * @param string $errorMsg Error message to return.
 */
function showError($errorMsg) {
    die(json_encode(Array('error_msg' => $errorMsg)));
}



// Make sure the proper GET parameter is set
if(!isset($_GET['filter']))
    showError('No filter has been set');

// Get the filter
$filter = $_GET['filter'];

try {
    // Check whether claimed stations should be showed only
    $claimedOnly = (isset($_GET['claimed_only']) && $_GET['claimed_only'] == '1');

    // Get the list of stations
    $stations = StationManager::getStations($filter, $claimedOnly);
    $arr = Array();

    // Split the columns parameter
    $columns = Array();
    if(isset($_GET['columns']))
        $columns = explode('|', $_GET['columns']);

    // Loop through all stations to put them in the array
    foreach($stations as $station) {
        // Validate the instance
        if(!($station instanceof Station))
            continue;

        // Build an array of station data to put in the list that is returned
        $stationArr = Array(
            'station_id' => utf8_encode($station->getId()),
            'station_name' => utf8_encode($station->getName()),
            'station_points' => utf8_encode($station->getCachedPoints())
        );

        // Add a station color based on the current occupation if there's any
        $stationColor = $station->getStationColor();
        if($stationColor !== null)
            $stationArr['station_color'] = utf8_encode($stationColor);

        // Add the coordinates
        if(in_array('station_lat', $columns))
            $stationArr['station_lat'] = utf8_encode($station->getGeoLatitude());
        if(in_array('station_long', $columns))
            $stationArr['station_long'] = utf8_encode($station->getGeoLongitude());
        if(in_array('station_group', $columns))
            $stationArr['station_group'] = utf8_encode($station->getOccupation()->getTeam()->getDisplayName(true, true));

        // Add the station data array to the main array
        array_push($arr, $stationArr);
    }

    // Encode and echo the array as JSON
    echo json_encode($arr);

} catch(Exception $e) {
    showError('An error occurred while searching');
}