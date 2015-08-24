<?php

use app\picture\Picture;
use app\picture\PictureManager;
use app\session\SessionManager;
use app\team\Team;
use app\template\PageFooterBuilder;
use app\template\PageHeaderBuilder;

// Include the page top
require_once('top.php');

// Check whether a picture ID is set
if(isset($_GET['picture_id'])) {

    // Get the picture id
    $pictureId = $_GET['picture_id'];

    // Validate the picture ID
    if(!PictureManager::isPictureWithId($pictureId))
        // Show an error page
        showErrorPage();

    // Get the picture instance
    $picture = new Picture($pictureId);

    // Check whether the approval status of a picture must be changed
    if(isset($_GET['delete'])) {

        // Make sure the user has permission
        if($picture->getTeam()->getId() != SessionManager::getLoggedInTeam()->getId())
            requireAdmin();

        ?>
        <!--suppress HtmlDeprecatedTag -->
        <div data-role="page" id="page-main">
            <?php PageHeaderBuilder::create('Delete Picture')->setBackButton('approval.php')->build(); ?>

            <div data-role="main" class="ui-content">
                <p>Are you sure you want to permanently delete this picture, this action can't be reverted?</p>
                <br />

                <fieldset data-role="controlgroup" data-type="vertical">
                    <a href="index.php" data-rel="back" class="ui-btn ui-icon-back ui-btn-icon-left" data-direction="reverse">No, go back</a>
                    <a href="#" class="ui-btn ui-icon-delete ui-btn-icon-left" onclick="deletePicture(<?=$pictureId; ?>);">Yes, delete picture</a>
                </fieldset>
            </div>

            <?php PageFooterBuilder::create()->build(); ?>
        </div>
        <?php

        require('bottom.php');
        die();
    }

    // Make sure the user is administrator
    requireAdmin();

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
                            <td>Approved By</span></td>
                            <td><?=$approvalTeamStr; ?></td>
                        </tr>
                        <tr>
                            <td>Approved At</span></td>
                            <td><?=$picture->getApprovalTime()->toString(); ?></td>
                        </tr>
                        <?php
                    }

                    ?>
                    <tr>
                        <td>Time Taken</td>
                        <td><?=$picture->getTime()->toString(); ?></td>
                    </tr>
                </table>
            </center>
            <br />

            <center>
                <img src="<?=$picture->getResolutionUrl(512, 1024); ?>" class="app-picture" />
            </center>
            <br />

            <?php

            if(!$approved) {
                echo '<fieldset data-role="controlgroup" data-type="vertical">';
                echo '<a href="#" class="ui-btn ui-icon-check ui-btn-icon-left" onclick="setPictureApprovalStatusOnPage(' . $pictureId . ', 1);">Accept picture</a >';
                echo '<a href="#" class="ui-btn ui-icon-delete ui-btn-icon-left" onclick="setPictureApprovalStatusOnPage(' . $pictureId . ', 2);">Reject picture</a >';
                echo '<a href="approval.php?picture_id=' . $pictureId . '&delete=1" class="ui-btn ui-icon-delete ui-btn-icon-left" >Delete picture</a >';
                echo '</fieldset>';

            } else {
                echo '<div data-role="collapsible">';
                echo '<h4>Change approval status</h4>';
                echo '<fieldset data-role="controlgroup" data-type="vertical">';
                if($accepted)
                    echo '<a href="#" class="ui-btn ui-icon-delete ui-btn-icon-left" onclick="setPictureApprovalStatusOnPage(' . $pictureId . ', 2);">Reject picture</a >';
                else
                    echo '<a href="#" class="ui-btn ui-icon-check ui-btn-icon-left" onclick="setPictureApprovalStatusOnPage(' . $pictureId . ', 1);">Accept picture</a >';

                echo '<a href="#" class="ui-btn ui-icon-delete ui-btn-icon-left" onclick="setPictureApprovalStatusOnPage(' . $pictureId . ', 0);">Reset approval</a >';

                echo '<a href="approval.php?picture_id=' . $pictureId . '&delete=1" class="ui-btn ui-icon-delete ui-btn-icon-left" >Delete picture</a >';
                echo '</fieldset>';
                echo '</div>';
            }

            ?>

            <div data-role="collapsible">
                <h4>More options</h4>
                <fieldset data-role="controlgroup" data-type="vertical">
                    <a href="stations.php?station_id=<?=$station->getId(); ?>" class="ui-btn ui-icon-location ui-btn-icon-left">View station</a>
                    <a href="<?=$picture->getUrl(); ?>" class="ui-btn ui-icon-eye ui-btn-icon-left" target="_blank">View full resolution</a>
                </fieldset>
            </div>
        </div>

        <?php PageFooterBuilder::create()->build(); ?>
    </div>
    <?php

} else if(isset($_GET['wizard']) && ($wizardValue = $_GET['wizard']) == 'true') {

    // Make sure the user is administrator
    requireAdmin();

    // Make sure there's any picture in queue for approval
    $queueSize = PictureManager::getApprovalQueueSize();

    if($queueSize <= 0) {
        ?>
        <div data-role="page" id="page-main">
            <?php PageHeaderBuilder::create('Approval Wizard')->setBackButton('index.php')->build(); ?>

            <div data-role="main" class="ui-content">
                <p>There are no more pictures to approve.</p><br />

                <fieldset data-role="controlgroup" data-type="vertical">
                    <a href="index.php" data-rel="back" class="ui-btn ui-icon-back ui-btn-icon-left" data-direction="reverse">Go back</a>
                </fieldset>
            </div>

            <?php PageFooterBuilder::create()->build(); ?>
        </div>
        <?php

        // Print the bottom of the page
        require('bottom.php');
        die();

    } else {

        // Get the next item in the approval queue
        $picture = PictureManager::getApprovalQueueNext();
        $pictureId = $picture->getId();

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
        <div data-role="page" id="page-main">
            <?php PageHeaderBuilder::create('Approval Wizard')->setBackButton('approval.php')->build(); ?>

            <div data-role="main" class="ui-content">
                <?php

                // Create the occupation text
                $approvalTeamStr = '<i>Unknown team</i>';

                // Check whether the picture has a source team
                if($team !== null) {
                    // Reset the occupation text
                    $approvalTeamStr = '';

                    // Add an icon if a team color is determined
                    if($teamColor !== null)
                        $approvalTeamStr .= '<img src="style/image/icon/16/transparent.png" class="ui-li-icon group-icon-text" style="background: #' . $teamColor . '" />';

                    // Add the team name
                    $approvalTeamStr .= 'Team ' . ucfirst($team->getName());
                }

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
                                <td>Approved By</span></td>
                                <td><?=$approvalTeamStr; ?></td>
                            </tr>
                            <tr>
                                <td>Approved At</span></td>
                                <td><?=$picture->getApprovalTime()->toString(); ?></td>
                            </tr>
                            <?php
                        }

                        ?>
                        <tr>
                            <td>Time Taken</td>
                            <td><?=$picture->getTime()->toString(); ?></td>
                        </tr>
                    </table>
                </center>
                <br />

                <center>
                    <img src="<?=$picture->getResolutionUrl(512, 1024); ?>" class="app-picture" />
                </center>
                <br />

                <?php

                if(!$approved) {
                    echo '<fieldset data-role="controlgroup" data-type="vertical">';
                    echo '<a href="#" class="ui-btn ui-icon-check ui-btn-icon-left" onclick="setPictureApprovalStatusOnWizard(' . $pictureId . ', 1);">Accept picture</a >';
                    echo '<a href="#" class="ui-btn ui-icon-delete ui-btn-icon-left" onclick="setPictureApprovalStatusOnWizard(' . $pictureId . ', 2);">Reject picture</a >';
                    echo '<a href="approval.php?picture_id=' . $pictureId . '&delete=1" class="ui-btn ui-icon-delete ui-btn-icon-left" >Delete picture</a >';
                    echo '</fieldset>';

                } else {
                    echo '<div data-role="collapsible">';
                    echo '<h4>Change approval status</h4>';
                    echo '<fieldset data-role="controlgroup" data-type="vertical">';
                    if($accepted)
                        echo '<a href="#" class="ui-btn ui-icon-delete ui-btn-icon-left" onclick="setPictureApprovalStatusOnWizard(' . $pictureId . ', 2);">Reject picture</a >';
                    else
                        echo '<a href="#" class="ui-btn ui-icon-check ui-btn-icon-left" onclick="setPictureApprovalStatusOnWizard(' . $pictureId . ', 1);">Accept picture</a >';

                    echo '<a href="#" class="ui-btn ui-icon-delete ui-btn-icon-left" onclick="setPictureApprovalStatusOnWizard(' . $pictureId . ', 0);">Reset approval</a >';

                    echo '<a href="approval.php?picture_id=' . $pictureId . '&delete=1" class="ui-btn ui-icon-delete ui-btn-icon-left" >Delete picture</a >';
                    echo '</fieldset>';
                    echo '</div>';
                }

                ?>

                <div data-role="collapsible">
                    <h4>More options</h4>
                    <fieldset data-role="controlgroup" data-type="vertical">
                        <a href="stations.php?station_id=<?=$station->getId(); ?>" class="ui-btn ui-icon-location ui-btn-icon-left">View station</a>
                        <a href="<?=$picture->getUrl(); ?>" class="ui-btn ui-icon-eye ui-btn-icon-left" target="_blank">View full resolution</a>
                    </fieldset>
                </div>
            </div>

            <?php PageFooterBuilder::create()->build(); ?>
        </div>
        <?php

        // Print the bottom of the page
        require('bottom.php');
        die();
    }

} else {

    // Make sure the user is administrator
    requireAdmin();

    ?>
    <div data-role="page" id="page-about">
        <?php

        // Include page top
        PageHeaderBuilder::create('Picture Approval')->setBackButton('index.php')->build();

        // Get the number of pictures in the approval queue
        $approvalQueueSize = PictureManager::getApprovalQueueSize();

        ?>

        <div data-role="main" class="ui-content">
            <fieldset data-role="controlgroup" data-type="vertical">
                <a href="approval.php?wizard=true" class="ui-btn ui-icon-check ui-btn-icon-left">Start approval wizard</a>
            </fieldset>

            <ul class="ui-listview" data-role="listview" id="listview-approval" data-inset="true">
                <?php
                if ($approvalQueueSize > 0) {
                    // Print the delimiter
                    echo '<li data-role="list-divider">' . $approvalQueueSize . ' waiting for approval</li>';

                    // Get the pictures waiting for approval
                    $approved = PictureManager::getApprovalQueue();

                    // Put each picture in the list
                    foreach ($approved as $picture) {
                        // Verify the instance
                        if (!($picture instanceof Picture))
                            continue;

                        // Get the team
                        $team = $picture->getTeam();
                        $teamColor = $team->getColorHex();

                        // Build the team text
                        $approvalTeamStr = 'Team ' . ucfirst($team->getName());

                        // Add an icon if a team color is determined
                        if ($teamColor !== null)
                            $approvalTeamStr = '<img src="style/image/icon/16/transparent.png" class="ui-li-icon group-icon-list-small" style="background: #' . $teamColor . '" /> ' . $approvalTeamStr;

                        // Print the list item
                        echo '<li><a href="approval.php?picture_id=' . $picture->getId() . '">';
                        echo '<img src="' . $picture->getThumbnailUrl(80) . '">';
                        echo '<h2>' . $picture->getStation()->getNameMiddle() . '</h2>';
                        echo '<p>' . $approvalTeamStr . '</p>';
                        echo '</a></li>';
                    }
                } else {
                    echo '<li data-role="list-divider">Waiting for approval</li>';
                    echo '<li><i>No pictures waiting for approval</i></li>';
                }
                ?>
            </ul>

            <ul class="ui-listview" data-role="listview" id="listview-approval" data-inset="true">
                <?php
                // Get the number of approved pictures
                $approvalCount = PictureManager::getApprovedPictureCount(1);

                echo '<li data-role="list-divider">Last accepted</li>';

                if ($approvalCount > 0) {
                    // Get the pictures waiting for approval
                    $approved = PictureManager::getLastApproved(3, 1);

                    // Put each picture in the list
                    foreach ($approved as $picture) {
                        // Verify the instance
                        if (!($picture instanceof Picture))
                            continue;

                        // Get the team
                        $team = $picture->getTeam();
                        $teamColor = $team->getColorHex();

                        // Build the team text
                        $approvalTeamStr = 'Team ' . ucfirst($team->getName());

                        // Add an icon if a team color is determined
                        if ($teamColor !== null)
                            $approvalTeamStr = '<img src="style/image/icon/16/transparent.png" class="ui-li-icon group-icon-list-small" style="background: #' . $teamColor . '" /> ' . $approvalTeamStr;

                        // Print the list item
                        echo '<li><a href="approval.php?picture_id=' . $picture->getId() . '">';
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

                echo '<li data-role="list-divider">Last rejected</li>';

                if ($approvalCount > 0) {
                    // Get the pictures waiting for approval
                    $approved = PictureManager::getLastApproved(3, 2);

                    // Put each picture in the list
                    foreach ($approved as $picture) {
                        // Verify the instance
                        if (!($picture instanceof Picture))
                            continue;

                        // Get the team
                        $team = $picture->getTeam();
                        $teamColor = $team->getColorHex();

                        // Build the team text
                        $approvalTeamStr = 'Team ' . ucfirst($team->getName());

                        // Add an icon if a team color is determined
                        if ($teamColor !== null)
                            $approvalTeamStr = '<img src="style/image/icon/16/transparent.png" class="ui-li-icon group-icon-list-small" style="background: #' . $teamColor . '" /> ' . $approvalTeamStr;

                        // Print the list item
                        echo '<li><a href="approval.php?picture_id=' . $picture->getId() . '">';
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