<?php

use app\session\SessionManager;
use app\template\PageFooterBuilder;
use app\template\PageHeaderBuilder;
use app\template\PageSidebarBuilder;
use app\user\linked\LinkedUser;
use app\user\linked\LinkedUserManager;
use app\user\UserManager;
use carbon\core\util\StringUtils;

// Include the page top
require_once('top.php');

// Make sure the user is logged in
if(SessionManager::isLoggedIn())
    requireLogin();

// Get the action parameter if set
$a = null;
if(isset($_GET['a']))
    $a = trim($_GET['a']);

// TODO: Make sure the user has permission to do certain things!

if(StringUtils::equals($a, 'add', false)) {
    if(!isset($_POST['linked-user-user'])) {
        ?>
        <div data-role="page" id="page-main">
            <?php PageHeaderBuilder::create(__('linkedUser', 'addLinkedUser'))->setShowActiveUser(false)->setBackButton('linkedusermanager.php')->build(); ?>

            <div data-role="main" class="ui-content">
                <p><?=__('linkedUser', 'fillInFieldsToLinkUser'); ?></p><br />

                <form method="POST" action="linkedusermanager.php?a=add&step=2">
                    <input name="linked-user-user" id="linked-user-name" value="" type="text" placeholder="<?=__('account', 'username'); ?>">
                    <input name="linked-user-password" id="linked-user-password" value="" type="password" placeholder="<?=__('account', 'password'); ?>">
                    <br />

                    <input type="submit" value="<?= __('linkedUser', 'addLinkedUser'); ?>" class="ui-btn ui-icon-lock ui-btn-icon-right" />
                </form>
            </div>

            <?php
            // Build the footer and sidebar
            PageFooterBuilder::create()->build();
            PageSidebarBuilder::create()->build();
            ?>
        </div>
        <?php

    } else {
        // Get the values
        $linkedUserName = trim($_POST['linked-user-user']);
        $linkedUserPassword = trim($_POST['linked-user-password']);

        // Validate the user credentials, and show an error message if the credentials are invalid
        if(($linkedUser = UserManager::validateLogin($linkedUserName, $linkedUserPassword)) === null)
            showErrorPage(__('login', 'usernameOrPasswordIncorrect'));

        // Make sure a different user is added
        if($linkedUser->getId() === SessionManager::getLoggedInUser()->getId())
            showErrorPage(__('linkedUser', 'canNotLinkYourOwnAccount'));

        // Make sure a different user is added
        if(LinkedUserManager::hasLinkedUser(SessionManager::getLoggedInUser(), $linkedUser))
            showErrorPage(__('linkedUser', 'accountAlreadyLinked'));

        // Add the linked user
        LinkedUserManager::createLinkedUser(SessionManager::getLoggedInUser(), $linkedUser);

        ?>
        <div data-role="page" id="page-register" data-unload="false">
            <?php PageHeaderBuilder::create(__('linkedUser', 'addLinkedUser'))->setShowActiveUser(false)->build(); ?>

            <div data-role="main" class="ui-content">
                <p>
                    <?= __('linkedUser', 'addedLinkedUserSuccessfully'); ?>
                </p><br />

                <fieldset data-role="controlgroup" data-type="vertical">
                    <a href="index.php" data-ajax="false" class="ui-btn ui-icon-home ui-btn-icon-left" data-direction="reverse"><?=__('navigation', 'goToFrontPage'); ?></a>
                </fieldset>
            </div>

            <?php
            // Build the footer and sidebar
            PageFooterBuilder::create()->build();
            PageSidebarBuilder::create()->build();
            ?>
        </div>
        <?php
    }

} else if(StringUtils::equals($a, 'delete', false)) {
    // Make sure the linked user ID is set
    if(!isset($_GET['linked_user_id']))
        showErrorPage();

    // Get the linked user ID and make sure it's valid
    $linkedUserId = $_GET['linked_user_id'];
    if(!LinkedUserManager::isLinkedUserWithId($linkedUserId))
        showErrorPage();

    // Get the linked user instance
    $linkedUser = new LinkedUser($linkedUserId);

    // Make sure the user has permission to manage this linked user
    if(!$linkedUser->isOwner(SessionManager::getLoggedInUser()) && !$linkedUser->isUser(SessionManager::getLoggedInUser()))
        showErrorPage();

    // Check whether this account is linked by a different user
    $linkedByOther = $linkedUser->getOwner()->getId() !== SessionManager::getLoggedInUser()->getId();

    if(!isset($_POST['agree'])) {
        ?>
        <div data-role="page" id="page-register" data-unload="false">
            <?php PageHeaderBuilder::create(__('linkedUser', 'unlinkLinkedUser'))->setShowActiveUser(false)->setBackButton('index.php')->build(); ?>

            <div data-role="main" class="ui-content">
                <p><?=__('linkedUser', 'aboutToUnlinkLinkedUserNotReversible'); ?></p><br />

                <form method="POST" action="linkedusermanager.php?a=delete&linked_user_id=<?=$linkedUserId; ?>&step=2">
                    <center>
                        <table class="ui-responsive">
                            <?php
                            if($linkedByOther) {
                                ?>
                                <tr>
                                    <td><?=__('linkedUser', 'linkedBy'); ?></td>
                                    <td><?=$linkedUser->getOwner()->getFullName(); ?></td>
                                </tr>
                                <?php
                            }
                            ?>
                            <tr>
                                <td><?=__('account', 'user'); ?></td>
                                <td><?=$linkedUser->getUser()->getFullName(); ?></td>
                            </tr>
                        </table>
                    </center>
                    <br />

                    <label for="agree"><?= __('linkedUser', 'youSureUnlinkLinkedUser'); ?></label>
                    <select id="agree" name="agree" data-role="slider">
                        <option value="0"><?= __('general', 'acceptanceNo'); ?></option>
                        <option value="1"><?= __('general', 'acceptanceYes'); ?></option>
                    </select>

                    <input type="submit" value="<?= __('linkedUser', 'unlinkLinkedUser'); ?>"
                           class="ui-btn ui-icon-lock ui-btn-icon-right" />
                </form>
            </div>

            <?php
            // Build the footer and sidebar
            PageFooterBuilder::create()->build();
            PageSidebarBuilder::create()->build();
            ?>
        </div>
        <?php

    } else {
        // Make sure the user agree's to change the mail address
        $agree = $_POST['agree'];
        if($agree != 1)
            showErrorPage(__('linkedUser', 'mustAgreeToRemoveLinkedUser'));

        // Delete the linked user
        $linkedUser->delete();
        $linkedUser = null;

        ?>
        <div data-role="page" id="page-register" data-unload="false">
            <?php PageHeaderBuilder::create(__('linkedUser', 'unlinkLinkedUser'))->setShowActiveUser(false)->build(); ?>

            <div data-role="main" class="ui-content">
                <p>
                    <?= __('linkedUser', 'unlinkedLinkedUserSuccessfully'); ?>
                </p><br />

                <fieldset data-role="controlgroup" data-type="vertical">
                    <a href="index.php" data-ajax="false" class="ui-btn ui-icon-home ui-btn-icon-left" data-direction="reverse"><?=__('navigation', 'goToFrontPage'); ?></a>
                </fieldset>
            </div>

            <?php
            // Build the footer and sidebar
            PageFooterBuilder::create()->build();
            PageSidebarBuilder::create()->build();
            ?>
        </div>
        <?php
    }

} else if(StringUtils::equals($a, 'set', false)) {
    // Make sure the linked user ID is set
    if(!isset($_GET['linked_user_id']))
        showErrorPage();

    // Get the linked user ID
    $linkedUserId = $_GET['linked_user_id'];

    // Check whether or not to set or reset the active user
    if(!StringUtils::equals($linkedUserId, 'null', false)) {
        // Make sure the linked user is valid
        if(!LinkedUserManager::isLinkedUserWithId($linkedUserId))
            showErrorPage();

        // Get the linked user instance
        $linkedUser = new LinkedUser($linkedUserId);

        // Make sure the current logged in user is the same as the owner of the linked user
        if(!$linkedUser->isOwner(SessionManager::getLoggedInUser()))
            showErrorPage();

        // TODO: Make sure the user is allowed to use his account as the specified user

        SessionManager::setActiveUser($linkedUser->getUser(), true);

        ?>
        <div data-role="page" id="page-register" data-unload="false">
            <?php PageHeaderBuilder::create(__('linkedUser', 'activeUser'))->build(); ?>

            <div data-role="main" class="ui-content">
                <p>
                    <?= __('linkedUser', 'activeUserSetSuccessfully'); ?>
                </p><br />

                <center>
                    <table class="ui-responsive">
                        <tr>
                            <td><?=__('linkedUser', 'activeUser'); ?></td>
                            <td><?=$linkedUser->getUser()->getFullName(); ?></td>
                        </tr>
                    </table>
                </center>
                <br />

                <fieldset data-role="controlgroup" data-type="vertical">
                    <a href="index.php" data-ajax="false" class="ui-btn ui-icon-home ui-btn-icon-left" data-direction="reverse"><?=__('navigation', 'goToFrontPage'); ?></a>
                </fieldset>
            </div>

            <?php
            // Build the footer and sidebar
            PageFooterBuilder::create()->build();
            PageSidebarBuilder::create()->build();
            ?>
        </div>
        <?php

    } else {
        // Reset the active user
        SessionManager::setActiveUser(null, true);

        ?>
        <div data-role="page" id="page-register" data-unload="false">
            <?php PageHeaderBuilder::create(__('linkedUser', 'activeUser'))->build(); ?>

            <div data-role="main" class="ui-content">
                <p>
                    <?= __('linkedUser', 'activeUserRevertedSuccessfully'); ?>
                </p><br />

                <fieldset data-role="controlgroup" data-type="vertical">
                    <a href="index.php" data-ajax="false" class="ui-btn ui-icon-home ui-btn-icon-left" data-direction="reverse"><?=__('navigation', 'goToFrontPage'); ?></a>
                </fieldset>
            </div>

            <?php
            // Build the footer and sidebar
            PageFooterBuilder::create()->build();
            PageSidebarBuilder::create()->build();
            ?>
        </div>
        <?php
    }

} else if(isset($_GET['linked_user_id'])) {
    // Get the linked user ID
    $linkedUserId = trim($_GET['linked_user_id']);

    // Make sure the linked user ID
    if(!LinkedUserManager::isLinkedUserWithId($linkedUserId))
        showErrorPage();

    // Get the linked user instance
    $linkedUser = new LinkedUser($linkedUserId);

    // Make sure the user has permission to manage this linked user
    if(!$linkedUser->isOwner(SessionManager::getLoggedInUser()) && !$linkedUser->isUser(SessionManager::getLoggedInUser()))
        showErrorPage();

    // Check whether this account is linked by a different user
    $linkedByOther = $linkedUser->getOwner()->getId() !== SessionManager::getLoggedInUser()->getId();

    ?>
    <div data-role="page" id="page-main">
        <?php PageHeaderBuilder::create(__('linkedUser', 'manageLinkedUser'))->setShowActiveUser(false)->setBackButton('linkedusermanager.php')->build(); ?>

        <div data-role="main" class="ui-content">
            <center>
                <table class="ui-responsive">
                    <?php
                    if($linkedByOther) {
                        ?>
                        <tr>
                            <td><?=__('linkedUser', 'linkedBy'); ?></td>
                            <td><?=$linkedUser->getOwner()->getFullName(); ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                    <tr>
                        <td><?=__('account', 'user'); ?></td>
                        <td><?=$linkedUser->getUser()->getFullName(); ?></td>
                    </tr>
                    <tr>
                        <td><?=__('dateTime', 'linkDate'); ?></td>
                        <td><?=$linkedUser->getCreationDateTime()->toString(); ?></td>
                    </tr>
                    <?php
                    // Print the last used date time if set
                    if($linkedUser->getLastUsageDateTimeRaw() !== null) {
                        echo '<tr>';
                        echo '<td>' . __('dateTime', 'lastUsedDate') . '</td>';
                        echo '<td>' . $linkedUser->getLastUsageDateTime()->toString() . '</td>';
                        echo '</tr>';
                    }
                    ?>
                </table>
            </center>
            <br />

            <p>
                <?=__('linkedUser', 'pressButtonToActivateOrDelete'); ?>
            </p><br />

            <?php if(SessionManager::getActiveUser()->getId() !== $linkedUserId): ?>
                <fieldset data-role="controlgroup" data-type="vertical">
                    <?php if(!$linkedByOther && $linkedUser->getUser()->getId() !== SessionManager::getActiveUser()->getId()): ?>
                        <a href="linkedusermanager.php?linked_user_id=<?=$linkedUserId; ?>&a=set" class="ui-btn ui-icon-user ui-btn-icon-left"><?=__('linkedUser', 'useThisAccount'); ?></a>
                    <?php else: ?>
                        <a href="linkedusermanager.php?linked_user_id=<?=$linkedUserId; ?>&a=set" class="ui-btn ui-icon-user ui-btn-icon-left ui-state-disabled"><?=__('linkedUser', 'useThisAccount'); ?></a>
                    <?php endif; ?>
                </fieldset>
            <?php endif; ?>

            <fieldset data-role="controlgroup" data-type="vertical">
                <a href="linkedusermanager.php?linked_user_id=<?=$linkedUserId; ?>&a=delete" class="ui-btn ui-icon-delete ui-btn-icon-left"><?=__('linkedUser', 'unlinkLinkedUser'); ?></a>
            </fieldset>
        </div>

        <?php
        // Build the footer and sidebar
        PageFooterBuilder::create()->build();
        PageSidebarBuilder::create()->build();
        ?>
    </div>
    <?php

} else {
    ?>
    <div data-role="page" id="page-main">
        <?php PageHeaderBuilder::create(__('linkedUser', 'linkedUsers'))->setShowActiveUser(false)->setBackButton('index.php')->build(); ?>

        <div data-role="main" class="ui-content">
            <?php
            // Determine whether the user has a different active user
            $differentActiveUser = SessionManager::getLoggedInUser()->getId() !== SessionManager::getActiveUser()->getId();

            // Show option to switch back to the default user
            if($differentActiveUser) {
                ?>
                <fieldset data-role="controlgroup" data-type="vertical">
                    <a href="linkedusermanager.php?a=set&linked_user_id=null" class="ui-btn ui-icon-back ui-btn-icon-left ui-shadow"><?=__('account', 'useMyOwnAccount'); ?></a>
                </fieldset>
                <?php
            }
            ?>

            <p>
                <?=$differentActiveUser ? '<br />' : ''; ?>
                <?= __('linkedUser', 'clickOnLinkedUserToViewOrRemove'); ?>
            </p><br />

            <?php

            // Get all linked users
            $linkedUsers = LinkedUserManager::getLinkedUsersForOwner();
            $linkedUsersOther = LinkedUserManager::getLinkedUsersForUser();

            // Print the list top
            echo '<ul class="ui-listview" data-role="listview" id="listview-stations-last-occupied" data-inset="true">';

            // Print the list of users that the user linked
            if(sizeof($linkedUsers) > 0) {
                ?>
                <li data-role="list-divider"><?= __('linkedUser', 'myLinkedUsers'); ?></li>
                <?php
                // Put all linked users in the list
                foreach($linkedUsers as $linkedUser) {
                    // Validate the instance
                    if(!($linkedUser instanceof LinkedUser))
                        continue;

                    // Get the linked user user
                    $linkedUserUser = $linkedUser->getUser();

                    // Determine whether to add an emblem
                    $emblem = '';
                    if(SessionManager::getActiveUser()->getId() === $linkedUserUser->getId())
                        $emblem = '<span class="ui-li-count">' . __('general', 'active') . '</span>';

                    // Print the item
                    echo '<li><a class="ui-btn ui-btn-icon-right ui-icon-carat-r" href="linkedusermanager.php?linked_user_id=' .
                        $linkedUser->getId() . '">' . $linkedUserUser->getFullName() . $emblem . '</a></li>';
                }
            }

            // Print the list of users that linked the user
            if(sizeof($linkedUsersOther) > 0) {
                ?>
                <li data-role="list-divider"><?= __('linkedUser', 'usersLinkedMe'); ?></li>
                <?php
                // Put all linked users in the list
                foreach($linkedUsersOther as $linkedUser) {
                    // Validate the instance
                    if(!($linkedUser instanceof LinkedUser))
                        continue;

                    // Print the item
                    echo '<li><a class="ui-btn ui-btn-icon-right ui-icon-carat-r" href="linkedusermanager.php?linked_user_id=' .
                        $linkedUser->getId() . '">' . $linkedUser->getOwner()->getFullName() . '</a></li>';
                }
            }

            // There are no linked users yet, show a status message
            if((sizeof($linkedUsers) + sizeof($linkedUsersOther)) <= 0)
                echo '<li><i>' . __('linkedUser', 'youDoNotHaveLinkedUsers') . '..</i></li>';

            // Print the list bottom
            echo '</ul>';

            ?>

            <fieldset data-role="controlgroup" data-type="vertical">
                <a href="linkedusermanager.php?a=add" class="ui-btn ui-icon-plus ui-btn-icon-left"><?= __('linkedUser', 'addLinkedUser'); ?></a>
            </fieldset>
        </div>

        <?php
        // Build the footer and sidebar
        PageFooterBuilder::create()->build();
        PageSidebarBuilder::create()->build();
        ?>
    </div>
    <?php
}

// Include the page bottom
require_once('bottom.php');
