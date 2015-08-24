<?php

use app\picture\Picture;
use app\picture\PictureManager;
use app\session\SessionManager;
use app\team\Team;
use app\template\PageFooterBuilder;
use app\template\PageHeaderBuilder;

// Include the page top
require_once('top.php');

// Require the user to be logged in
requireLogin();

if(isset($_GET['picture_id'])) {
    // Get the picture id
    $pictureId = $_GET['picture_id'];

    // Validate the picture ID
    if(!PictureManager::isPictureWithId($pictureId))
        // Show an error page
        showErrorPage();

    // Get the picture instance
    $picture = new Picture($pictureId);

    // Get the picture station and team
    $station = $picture->getStation();
    $team = $picture->getTeam();

    // Get the team color
    $teamColor = null;
    if($team !== null)
        $teamColor = $team->getColorHex();

    // Check whether the picture is approved, accepted and/or rejected
    $approved = $picture->isApproved();
    $accepted = $picture->isAccepted();
    $rejected = $picture->isRejected();

    // Check whether the picture has been approved
    ?>
    <!--suppress HtmlDeprecatedTag -->
    <div data-role="page" id="page-main">
        <?php PageHeaderBuilder::create($station->getNameMiddle())->setBackButton('approval.php')->build(); ?>

        <div data-role="main" class="ui-content">
            <?php

            // Create the occupation text
            $approvalTeamStr = '<i>Unknown team</i>';

            // Check whether the picture has a source team
            if($team instanceof Team)
                $approvalTeamStr = $team->getDisplayName(true, false);

            ?>
            <center>
                <table class="ui-responsive">
                    <tr>
                        <td>Station</td>
                        <td><?=$station->getName(); ?></td>
                    </tr>
                    <tr>
                        <td>Team</td>
                        <td><?=$approvalTeamStr; ?></td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td>
                            <?php

                            // Check whether the picture is approved
                            if($accepted)
                                echo '<span style="color: green;">Accepted</span>';

                            elseif($rejected)
                                echo '<span style="color: red;">Rejected</span>';

                            else
                                echo '<i>Waiting for approval</i>';
                            ?>
                        </td>
                    </tr>
                    <?php

                    if($approved) {
                        // Get the team that approved the user
                        $approvalTeam = $picture->getApprovalTeam();

                        // Determine the approval team
                        $approvalTeamStr = '<i>An Administrator</i>';

                        // Check whether an approval team has been specified
                        if($approvalTeam !== null) {
                            // Reset the occupation text
                            $approvalTeamStr = '';

                            // Get the color of the valuation team
                            $approvalTeamColor = $approvalTeam->getColorHex();

                            // Add an icon if a team color is determined
                            if($approvalTeamColor !== null)
                                $approvalTeamStr .= '<img src="style/image/icon/16/transparent.png" class="ui-li-icon group-icon-text" style="background: #' . $approvalTeamColor . '" />';

                            // Add the team name
                            $approvalTeamStr .= ucfirst($approvalTeam->getName());
                        }

                        ?>
                        <tr>
                            <td>Approved by</span></td>
                            <td><?=$approvalTeamStr; ?></td>
                        </tr>
                        <tr>
                            <td>Approved at</span></td>
                            <td><?=$picture->getApprovalTime()->toString(); ?></td>
                        </tr>
                        <?php
                    }

                    ?>
                    <tr>
                        <td>Time taken</td>
                        <td><?=$picture->getTime()->toString(); ?></td>
                    </tr>
                </table>
            </center>
            <br />

            <center>
                <img src="<?=$picture->getResolutionUrl(512, 1024); ?>" class="app-picture" />
            </center>
            <br />

            <fieldset data-role="controlgroup" data-type="vertical">
                <a href="stations.php?station_id=<?=$station->getId(); ?>" class="ui-btn ui-icon-location ui-btn-icon-left">View station</a>
                <a href="<?=$picture->getUrl(); ?>" class="ui-btn ui-icon-eye ui-btn-icon-left" target="_blank">View full resolution</a>
            </fieldset>

            <?php

            if(!$approved) {
                echo '<fieldset data-role="controlgroup" data-type="vertical">';
                if(SessionManager::isAdmin()) {
                    echo '<a href="#" class="ui-btn ui-icon-check ui-btn-icon-left" onclick="setPictureApprovalStatusOnPage(' . $pictureId . ', 1);">Accept picture</a >';
                    echo '<a href="#" class="ui-btn ui-icon-delete ui-btn-icon-left" onclick="setPictureApprovalStatusOnPage(' . $pictureId . ', 2);">Reject picture</a >';
                }

                if(SessionManager::isAdmin() || $picture->getTeam()->getId() == SessionManager::getLoggedInTeam()->getId())
                    echo '<a href="approval.php?picture_id=' . $pictureId . '&delete=1" class="ui-btn ui-icon-delete ui-btn-icon-left" >Delete picture</a >';

                echo '</fieldset>';
            } else {
                echo '<fieldset data-role="controlgroup" data-type="vertical">';
                if(SessionManager::isAdmin()) {
                    echo '<div data-role="collapsible">';
                    echo '<h4>Change approval status</h4>';
                    if($accepted)
                        echo '<a href="#" class="ui-btn ui-icon-delete ui-btn-icon-left" onclick="setPictureApprovalStatusOnPage(' .
                            $pictureId . ', 2);">Reject picture</a >';
                    else
                        echo '<a href="#" class="ui-btn ui-icon-check ui-btn-icon-left" onclick="setPictureApprovalStatusOnPage(' .
                            $pictureId . ', 1);">Accept picture</a >';

                    echo '<a href="#" class="ui-btn ui-icon-delete ui-btn-icon-left" onclick="setPictureApprovalStatusOnPage(' .
                        $pictureId . ', 0);">Reset approval</a >';

                    echo '<a href="approval.php?picture_id=' . $pictureId .
                        '&delete=1" class="ui-btn ui-icon-delete ui-btn-icon-left" >Delete picture</a >';
                    echo '</div>';

                } elseif($picture->getTeam()->getId() == SessionManager::getLoggedInTeam()->getId()) {
                    echo '<a href="approval.php?picture_id=' . $pictureId .
                        '&delete=1" class="ui-btn ui-icon-delete ui-btn-icon-left" >Delete picture</a >';
                }
                echo '</fieldset>';
            }
            ?>

        </div>

        <?php PageFooterBuilder::create()->build(); ?>
    </div>
    <?php

} else if(isset($_GET['picture_status'])) {

    // Get the picture status
    $pictureStatus = $_GET['picture_status'];

    // Make sure the status is valid
    if(!is_numeric($pictureStatus) || $pictureStatus < 0 || $pictureStatus > 3)
        showErrorPage();

    // Check whether a team is specified
    $team = null;
    if(isset($_GET['team_id']))
        $team = new Team($_GET['team_id']);

    ?>
    <div data-role="page" id="page-about">
        <?php PageHeaderBuilder::create('Pictures')->setBackButton('index.php')->build(); ?>

        <div data-role="main" class="ui-content">
            <ul class="ui-listview" data-role="listview" id="listview-approval" data-inset="true">
                <?php
                // Get the number of approved pictures
                $approvalCount = PictureManager::getApprovedPictureCount($pictureStatus, $team);

                $typeName = 'some';
                if($pictureStatus == '1')
                    $typeName = 'accepted';
                else if($pictureStatus == '2')
                    $typeName = 'rejected';

                if($team !== null && $team->getId() == SessionManager::getLoggedInTeam()->getId())
                    echo '<li data-role="list-divider">Our ' . $typeName . ' pictures</li>';
                else if($team !== null)
                    echo '<li data-role="list-divider">' . ucfirst($typeName) . ' pictures for ' . $team->getDisplayName(false) . '</li>';
                else
                    echo '<li data-role="list-divider">' . ucfirst($typeName) . ' pictures of all teams</li>';

                if($approvalCount > 0) {
                    // Get the pictures waiting for approval
                    $approving = PictureManager::getLastApproved(999, $pictureStatus, $team);

                    // Put each picture in the list
                    foreach($approving as $picture) {
                        // Verify the instance
                        if(!($picture instanceof Picture))
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
                        echo '<h2>' . $picture->getStation()->getNameMiddle() . '</h2>';
                        echo '<p>' . $approvalTeamStr . '</p>';
                        echo '</a></li>';
                    }

                } else
                    echo '<li><i>You don\'t have any accepted pictures yet, go claim a station!</i></li>';
                ?>
            </ul>
        </div>

        <?php PageFooterBuilder::create()->build(); ?>
    </div>
    <?php

} else {
    ?>
    <div data-role="page" id="page-about">
        <?php PageHeaderBuilder::create('Pictures')->setBackButton('index.php')->build(); ?>

        <div data-role="main" class="ui-content">
            <?php

            // Get the number of pictures waiting for approval for the current team
            $approvalQueueSize = PictureManager::getApprovalQueueSize(SessionManager::getLoggedInTeam());

            if($approvalQueueSize > 0):
                ?>
                <ul class="ui-listview" data-role="listview" id="listview-pictures" data-inset="true">
                    <?php
                    echo '<li data-role="list-divider">Our pictures waiting for approval: ' . $approvalQueueSize .
                        '</li>';

                    // Get the pictures waiting for approval
                    $approving = PictureManager::getApprovalQueue(SessionManager::getLoggedInTeam());

                    // Put each picture in the list
                    foreach($approving as $picture) {
                        // Verify the instance
                        if(!($picture instanceof Picture))
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
                        echo '<h2>' . $picture->getStation()->getNameMiddle() . '</h2>';
                        echo '<p>' . $approvalTeamStr . '</p>';
                        echo '</a></li>';
                    }
                    ?>
                </ul>
            <?php endif; ?>

            <ul class="ui-listview" data-role="listview" id="listview-approval" data-inset="true">
                <?php
                // Get the number of approved pictures
                $approvalCount = PictureManager::getApprovedPictureCount(1, SessionManager::getLoggedInTeam());

                echo '<li data-role="list-divider">Our accepted pictures</li>';

                if($approvalCount > 0) {
                    // Get the pictures waiting for approval
                    $approving = PictureManager::getLastApproved(5, 1, SessionManager::getLoggedInTeam());

                    // Put each picture in the list
                    foreach($approving as $picture) {
                        // Verify the instance
                        if(!($picture instanceof Picture))
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
                        echo '<h2>' . $picture->getStation()->getNameMiddle() . '</h2>';
                        echo '<p>' . $approvalTeamStr . '</p>';
                        echo '</a></li>';
                    }

                    echo '<li><a href="pictures.php?picture_status=1&team_id=' . SessionManager::getLoggedInTeam()->getId() . '">View all...</a></li>';

                } else
                    echo '<li><i>You don\'t have any accepted pictures yet, go claim a station!</i></li>';

                // Get the number of approval pictures
                $approvalCount = PictureManager::getApprovedPictureCount(2, SessionManager::getLoggedInTeam());

                echo '<li data-role="list-divider">Our rejected pictures</li>';

                if($approvalCount > 0) {
                    // Get the pictures waiting for approval
                    $approving = PictureManager::getLastApproved(5, 2, SessionManager::getLoggedInTeam());

                    // Put each picture in the list
                    foreach($approving as $picture) {
                        // Verify the instance
                        if(!($picture instanceof Picture))
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
                        echo '<h2>' . $picture->getStation()->getNameMiddle() . '</h2>';
                        echo '<p>' . $approvalTeamStr . '</p>';
                        echo '</a></li>';
                    }

                    echo '<li><a href="pictures.php?picture_status=2&team_id=' . SessionManager::getLoggedInTeam()->getId() . '">View all...</a></li>';

                } else
                    echo '<li><i>You don\'t have any rejected pictures yet, keep it that way!</i></li>';

                ?>
            </ul>

            <ul class="ui-listview" data-role="listview" id="listview-approval" data-inset="true">
                <?php
                // Get the number of approved pictures
                $approvalCount = PictureManager::getApprovedPictureCount(1);

                echo '<li data-role="list-divider">Last accepted for all teams</li>';

                if($approvalCount > 0) {
                    // Get the pictures waiting for approval
                    $approving = PictureManager::getLastApproved(5, 1);

                    // Put each picture in the list
                    foreach($approving as $picture) {
                        // Verify the instance
                        if(!($picture instanceof Picture))
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
                        echo '<h2>' . $picture->getStation()->getNameMiddle() . '</h2>';
                        echo '<p>' . $approvalTeamStr . '</p>';
                        echo '</a></li>';
                    }

                    echo '<li><a href="pictures.php?picture_status=1">View all...</a></li>';

                } else
                    echo '<li><i>No picture has been accepted yet</i></li>';

                // Get the number of approval pictures
                $approvalCount = PictureManager::getApprovedPictureCount(2);

                echo '<li data-role="list-divider">Last rejected for all teams</li>';

                if($approvalCount > 0) {
                    // Get the pictures waiting for approval
                    $approving = PictureManager::getLastApproved(5, 2);

                    // Put each picture in the list
                    foreach($approving as $picture) {
                        // Verify the instance
                        if(!($picture instanceof Picture))
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
                        echo '<h2>' . $picture->getStation()->getNameMiddle() . '</h2>';
                        echo '<p>' . $approvalTeamStr . '</p>';
                        echo '</a></li>';
                    }

                    echo '<li><a href="pictures.php?picture_status=2">View all...</a></li>';

                } else
                    echo '<li><i>No picture has been rejected yet</i></li>';

                ?>
            </ul>
        </div>

        <?php PageFooterBuilder::create()->build(); ?>
    </div>
    <?php
}

// Include the page bottom
require_once('bottom.php');
