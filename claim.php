<?php

use app\config\Config;
use app\picture\PictureManager;
use app\session\SessionManager;
use app\station\Station;
use app\station\StationManager;
use app\template\PageFooterBuilder;
use app\template\PageHeaderBuilder;
use carbon\core\io\filesystem\directory\Directory;
use carbon\core\io\filesystem\file\File;
use carbon\core\util\StringUtils;

// Include the page top
require_once('top.php');

// Make sure the user is logged in
requireLogin();

if(!isset($_POST['claim_submit'])) {

    if(!isset($_GET['station_id'])) {
        ?>
        <!--suppress HtmlDeprecatedTag -->
        <div data-role="page" id="page-main">
            <?php PageHeaderBuilder::create('Claim Station')->setBackButton('index.php')->build(); ?>

            <div data-role="main" class="ui-content">
                <?php

                // Get the current team
                $team = SessionManager::getLoggedInTeam();

                ?>
                <p>
                    You're about to claim a new station.<br />
                    Please search for your current station bellow and select it, then press the <i>Continue</i> button.
                </p>
                <br />

                <form method="GET" action="claim.php" enctype="multipart/form-data" data-ajax="true">

                    <input id="claim-station-search" name="listview-stations-search" value="" type="search" placeholder="Search for stations..." />
                    <!--<ul id="listview-stations" class="ui-listview" data-role="listview" data-inset="true"></ul>-->
                    <fieldset id="claim-station-list" data-role="controlgroup" data-type="vertical"></fieldset>

                    <script>
                        // Set up the stations list view search widget on page load
                        $(document).on('pagecreate', function() {
                            createStationSearchSelectable($('#claim-station-list'), $('#claim-station-search'), function() {
                                // Hide the input container
                                hideContinue();

                                // Create an event handler for each radio button
                                $("input[name='station_id']").change(function() {
                                    // Show or hide the input button if any is selected
                                    if(!$("input[name='station_id']:checked").val())
                                        hideContinue();
                                    else
                                        showContinue();
                                });
                            });

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
                            if(!$("input[name='station_id']:checked").val())
                                inputContainer.hide();
                            else
                                inputContainer.show();
                        });
                    </script>

                    <div id="station-input-container" style="padding-top: 1px;">
                        <input type="submit" value="Continue" class="ui-btn ui-icon-lock ui-btn-icon-right" />
                    </div>
                </form>
            </div>

            <?php PageFooterBuilder::create()->build(); ?>
        </div>
        <?php

    } else {
        // Get the current team
        $team = SessionManager::getLoggedInTeam();

        // Get the station ID
        $stationId = $_GET['station_id'];

        // Get the station and make sure it's valid
        if(!StationManager::isStationWithId($stationId))
            showErrorPage();
        $station = new Station($stationId);

        // Make sure the station is currently claimable
        if(!$station->isReclaimable())
            showErrorPage('The station you\'re trying to claim has already be claimed recently. Please try to claim this station at a later time once it\'s reclaimable again.');

        ?>
        <div data-role="page" id="page-main">
            <?php PageHeaderBuilder::create('Claim Station')->setBackButton('index.php')->build(); ?>

            <div data-role="main" class="ui-content">
                <center>
                    <table class="ui-responsive">
                        <tr>
                            <td>Station</td>
                            <td><?=$station->getDisplayName(); ?></td>
                        </tr>
                        <tr>
                            <td>My team</td>
                            <td><?=$team->getDisplayName(); ?></td>
                        </tr>
                        <?php
                        $curPoints = $station->getCachedPoints();
                        $newPoints = $station->calculatePoints(1);

                        $pointsStr = $newPoints;
                        if($curPoints != $newPoints)
                            $pointsStr = '<span style="color: gray;">' . $curPoints . '&nbsp;&nbsp;&rArr;</span>&nbsp;&nbsp;' . $newPoints;
                        ?>
                        <tr>
                            <td>Station Points</td>
                            <td><?=$pointsStr; ?></td>
                        </tr>
                    </table>
                </center>
                <br />

                <p>
                    Please select and upload a picture of your group at this station.<br />
                    Make sure leaders approving this picture can easily verify you're actually at this station.
                </p>
                <br />

                <form method="POST" action="claim.php?upload=1" enctype="multipart/form-data" data-ajax="false">
                    <input type="hidden" name="claim_station_id" value="<?=$stationId; ?>" />

                    <label for="claim_picture">Picture</label>
                    <input type="hidden" name="MAX_FILE_SIZE" value="16000000" />
                    <input type="file" name="claim_picture" id="claim_picture" accept="image/jpeg,image/png,image/gif,capture=camera" />

                    <script>
                        // Set up the stations list view search widget on page load
                        $(document).on('pagecreate', function() {
                            $('#claim_picture').change(function() {
                                showContinue();
                            });

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

                            // Hide the container
                            inputContainer.hide();
                        });
                    </script>

                    <div id="station-input-container" style="padding-top: 1px;">
                        <input type="submit" name="claim_submit" value="Claim" class="ui-btn ui-icon-lock ui-btn-icon-right show-page-loading-msg" data-msgtext="Uploading picture..." />
                    </div>
                </form>
            </div>

            <?php PageFooterBuilder::create()->build(); ?>
        </div>
        <?php
    }

} else {
    // Make sure the data of the uploaded picture is available
    if(!isset($_FILES["claim_picture"]))
        showErrorPage();

    // Get the station ID
    $stationId = $_POST['claim_station_id'];

    // Get the station and make sure it's valid
    if(!StationManager::isStationWithId($stationId))
        showErrorPage();
    $station = new Station($stationId);

    // Make sure the station is currently claimable
    if(!$station->isReclaimable())
        showErrorPage('The station you\'re trying to claim has already be claimed recently. Please try to claim this station at a later time once it\'s reclaimable again.');

    // Get the picture data of the uploaded picture
    $pictureData = $_FILES["claim_picture"];

    // Fetch the file extension from the file name
    $fileNameParts = explode('.', $pictureData["name"]);
    $imageExtension = end($fileNameParts);

    // Make sure the file size is accepted
    if(!StringUtils::equals($imageExtension, Array('jpg', 'jpeg', 'png', 'gif'), false))
        showErrorPage('Whoops! This picture type is not supported.<br />Please go back and try it again with a different type of picture.');

    // Make sure the file size is accepted
    if($pictureData["size"] > 16000000 || $pictureData["size"] == 0)
        showErrorPage('Whoops! The image you\'re trying to upload is too big.<br />Please go back and try it again with a smaller image.');

    // Make sure a real image is uploaded (one that can be parsed)
    if(getimagesize($pictureData["tmp_name"]) === false)
        showErrorPage('Whoops! This picture type is not supported.<br />Please go back and try it again with a different type of picture.');

    // Get a new file name for the image
    $imageFileName = PictureManager::generateRandomPictureName($imageExtension);

    // Determine the target file and directory for the image
    $targetDir = new Directory(Config::getValue('app', 'pictures_dir', ''));
    $targetFile = new File($targetDir, $imageFileName);

    // Move the uploaded image and make sure it succeeds
    $uploadSucceed = move_uploaded_file($pictureData["tmp_name"], $targetFile);
    if(!$uploadSucceed)
        showErrorPage('Whoops! This picture type is not supported.<br />Please go back and try it again with a different type of picture.');

    try {
        // Try to rotate the image if it's JPEG
        if(!StringUtils::equals($imageExtension, Array('jpg', 'jpeg'), false)) {
            // Load the image
            if(($image = imagecreatefromjpeg($targetFile)) !== false) {
                // Read the exif data
                $exif = exif_read_data($targetFile);

                // Rotate the image if the orientation tag is available
                if(!empty($exif['Orientation'])) {
                    switch ($exif['Orientation']) {
                    case 3:
                        $image = imagerotate($image, 180, 0);
                        imagejpeg($image, $targetFile);
                        break;

                    case 6:
                        $image = imagerotate($image, -90, 0);
                        imagejpeg($image, $targetFile);
                        break;

                    case 8:
                        $image = imagerotate($image, 90, 0);
                        imagejpeg($image, $targetFile);
                        break;
                    }
                }
            }
        }
    } catch(Exception $e) { }

    // Add the picture to the database
    $picture = PictureManager::addPicture($imageFileName, null, $station);

    ?>
    <div data-role="page" id="page-main">
        <?php PageHeaderBuilder::create('Picture Uploaded')->build(); ?>

        <div data-role="main" class="ui-content">
            <?php

            // Get the current team
            $team = SessionManager::getLoggedInTeam();

            ?>
            <center>
                <table class="ui-responsive">
                    <tr>
                        <td>Station</td>
                        <td><?=$station->getDisplayName(); ?></td>
                    </tr>
                    <tr>
                        <td>My Team</td>
                        <td><?=$team->getDisplayName(); ?></td>
                    </tr>
                    <?php
                    $curPoints = $station->getCachedPoints();
                    $newPoints = $station->calculatePoints(1);

                    $pointsStr = $newPoints;
                    if($curPoints != $newPoints)
                        $pointsStr = '<span style="color: gray;">' . $curPoints . '&nbsp;&nbsp;&rArr;</span>&nbsp;&nbsp;' . $newPoints;
                    ?>
                    <tr>
                        <td>Station points</td>
                        <td><?=$pointsStr; ?></td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td><i>Waiting for approval</i></td>
                    </tr>
                    <tr>
                        <td>Time taken</td>
                        <td><?=$picture->getTime()->toString(); ?></td>
                    </tr>
                </table>
            </center>
            <br />

            <p>
                Your picture has been uploaded, and will be approved by a leader as soon as possible.
            </p>
            <br />

            <center>
                <img src="<?=$picture->getResolutionUrl(512, 1024); ?>" class="app-picture" />
            </center>
            <br />

            <fieldset data-role="controlgroup" data-type="vertical">
                <a href="index.php" class="ui-btn ui-icon-back ui-btn-icon-left" data-direction="reverse">Go Back</a>
            </fieldset>

            <div data-role="collapsible">
                <h4>More Options</h4>
                <fieldset data-role="controlgroup" data-type="vertical">
                    <a href="stations.php?station_id=<?=$station->getId(); ?>" class="ui-btn ui-icon-location ui-btn-icon-left">View Station</a>
                    <a href="<?=$picture->getUrl(); ?>" class="ui-btn ui-icon-eye ui-btn-icon-left" target="_blank">View Full Resolution</a>
                </fieldset>
            </div>
        </div>

        <?php PageFooterBuilder::create()->build(); ?>
    </div>
    <?php
}

// Include the page bottom
require_once('bottom.php');
