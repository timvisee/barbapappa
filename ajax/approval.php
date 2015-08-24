<?php

use app\occupation\OccupationManager;
use app\picture\Picture;
use app\picture\PictureManager;
use app\session\SessionManager;

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

/**
 * Return a success message.
 *
 * @param string $successMsg Success message to return.
 */
function showSuccess($successMsg) {
    die(json_encode(Array('status_msg' => $successMsg)));
}




// Make sure a picture ID is set
if(!isset($_GET['picture_id']))
    showError('The parameter picture_id has not been set');

// Get the picture ID
$pictureId = $_GET['picture_id'];

// Make sure the picture ID is valid
if(!PictureManager::isPictureWithId($pictureId))
    showError('There is no picture with the ID \'' . $pictureId . '\'');

// Make sure a set_approval parameter is set
if(!isset($_GET['set_approval']))
    showError('The parameter set_approval has not been set');

// Get the picture
$picture = new Picture($pictureId);

// Get the new approval status
$approvalStatus = $_GET['set_approval'];

// Delete a picture
if($approvalStatus == '-1') {
    // Make sure the user is allowed to remove this picture
    if(!SessionManager::isAdmin() && $picture->getTeam()->getId() != SessionManager::getLoggedInTeam()->getId())
        showError('Invalid session');

    // Delete the corresponding occupation
    OccupationManager::removeOccupationOfPicture($picture);

    // Delete the picture
    $picture->delete();

    // Show a success message
    showSuccess('The picture has been deleted!');
}

// Make sure the user is logged in
if(!SessionManager::isAdmin())
    showError('Invalid session');

// Make sure the picture ID is valid
if(!is_numeric($approvalStatus) || $approvalStatus < 0 || $approvalStatus > 2)
    showError('The new approval status \'' . $approvalStatus . '\' is invalid');

// Update approval status
$picture->setApproval($approvalStatus);

// Show a success message
showSuccess('The approval status of this picture has been updated');
