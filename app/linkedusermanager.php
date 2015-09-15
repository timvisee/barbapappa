<?php

use app\language\LanguageManager;
use app\session\SessionManager;
use app\template\PageFooterBuilder;
use app\template\PageHeaderBuilder;
use app\template\PageSidebarBuilder;
use app\user\linked\LinkedUser;
use app\user\linked\LinkedUserManager;
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

// Get all language manager languages
$languages = LanguageManager::getLanguages();

if(StringUtils::equals($a, 'add', false)) {
    if(!isset($_POST['linked-user-name'])) {
        ?>
        <div data-role="page" id="page-main">
            <?php PageHeaderBuilder::create(__('linkedUser', 'addLinkedUser'))->setBackButton('linkedUsermanager.php')->build(); ?>

            <div data-role="main" class="ui-content">
                <p><?=__('linkedUser', 'fillInFieldsToAddLinkedUser'); ?></p><br />

                <form method="POST" action="linkedusermanager.php?a=add&step=2">
                    <ul data-role="listview" data-inset="true">
                        <li data-role="list-divider"><?=__('linkedUser', 'linkedUserSpecifications'); ?></li>
                        <li class="ui-field-contain">
                            <label for="linked-user-name"><?=__('linkedUser', 'linkedUserName'); ?>:</label>
                            <input name="linked-user-name" id="linked-user-name" value="" type="text">
                        </li>
                    </ul>
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
        $linkedUserName = trim($_POST['linked-user-name']);

        // Validate the linked user name
        if(strlen($linkedUserName) == 0)
            showErrorPage(__('linkedUser', 'invalidLinkeduserName'));

        // Add the linkeduser
        $linkedUser = LinkedUserManager::createLinkeduser($linkedUserName);

        ?>
        <div data-role="page" id="page-register" data-unload="false">
            <?php PageHeaderBuilder::create(__('linkedUser', 'addLinkedUser'))->build(); ?>

            <div data-role="main" class="ui-content">
                <p>
                    <?= __('linkedUser', 'addedLinkeduserSuccessfully'); ?>
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

} else if(StringUtils::equals($a, 'change', false)) {
    // Make sure the linked user ID is set
    if(!isset($_GET['linked_user_id']))
        showErrorPage();

    // Get the linked user ID and make sure it's valid
    $linkedUserId = $_GET['linked_user_id'];
    if(!LinkedUserManager::isLinkeduserWithId($linkedUserId))
        showErrorPage();

    // Get the linked user instance
    $linkedUser = new Linkeduser($linkedUserId);

    if(!isset($_POST['linked-user-name'])) {
        ?>
        <div data-role="page" id="page-main">
            <?php PageHeaderBuilder::create(__('linkedUser', 'changeLinkedUser'))->setBackButton('linkedUsermanager.php')->build(); ?>

            <div data-role="main" class="ui-content">
                <p><?=__('linkedUser', 'fillInFieldsToChangeLinkedUser'); ?></p><br />

                <form method="POST" action="linkedusermanager.php?a=change&linked_user_id=<?=$linkedUserId; ?>&step=2">
                    <ul data-role="listview" data-inset="true">
                        <li data-role="list-divider"><?=__('linkedUser', 'linkedUserSpecifications'); ?></li>
                        <li class="ui-field-contain">
                            <label for="linked-user-name"><?=__('linkedUser', 'linkedUserName'); ?>:</label>
                            <input name="linked-user-name" id="linked-user-name" value="<?=$linkedUser->getName(); ?>" type="text">
                        </li>
                    </ul>
                    <br />

                    <input type="submit" value="<?= __('linkedUser', 'changeLinkedUser'); ?>" class="ui-btn ui-icon-lock ui-btn-icon-right" />
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
        $linkedUserName = trim($_POST['linked-user-name']);

        // Validate the linked user name
        if(strlen($linkedUserName) == 0)
            showErrorPage(__('linkedUser', 'invalidLinkeduserName'));

        // Set the name if it's different
        if(!StringUtils::equals($linkedUser->getName(), $linkedUserName, true))
            $linkedUser->setName($linkedUserName);

        ?>
        <div data-role="page" id="page-register" data-unload="false">
            <?php PageHeaderBuilder::create(__('linkedUser', 'changeLinkedUser'))->build(); ?>

            <div data-role="main" class="ui-content">
                <p>
                    <?= __('linkedUser', 'changedLinkeduserSuccessfully'); ?>
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
    if(!LinkedUserManager::isLinkeduserWithId($linkedUserId))
        showErrorPage();

    // Get the linked user instance
    $linkedUser = new Linkeduser($linkedUserId);

    if(!isset($_POST['agree'])) {
        ?>
        <div data-role="page" id="page-register" data-unload="false">
            <?php PageHeaderBuilder::create(__('linkedUser', 'deleteLinkedUser'))->setBackButton('index.php')->build(); ?>

            <div data-role="main" class="ui-content">
                <p><?=__('linkedUser', 'aboutToRemoveLinkeduserNotReversible'); ?></p><br />

                <form method="POST" action="linkedusermanager.php?a=delete&linked_user_id=<?=$linkedUserId; ?>&step=2">
                    <center>
                        <table class="ui-responsive">
                            <tr>
                                <td><?=__('linkedUser', 'linkedUserName'); ?></td>
                                <td><?=$linkedUser->getName(); ?></td>
                            </tr>
                        </table>
                    </center>
                    <br />

                    <label for="agree"><?= __('linkedUser', 'youSureRemoveLinkedUser'); ?></label>
                    <select id="agree" name="agree" data-role="slider">
                        <option value="0"><?= __('general', 'acceptanceNo'); ?></option>
                        <option value="1"><?= __('general', 'acceptanceYes'); ?></option>
                    </select>

                    <input type="submit" value="<?= __('linkedUser', 'deleteLinkedUser'); ?>"
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

        // Delete the linkeduser
        $linkedUser->delete();
        $linkedUser = null;

        ?>
        <div data-role="page" id="page-register" data-unload="false">
            <?php PageHeaderBuilder::create(__('linkedUser', 'deleteLinkedUser'))->build(); ?>

            <div data-role="main" class="ui-content">
                <p>
                    <?= __('linkedUser', 'removedLinkeduserSuccessfully'); ?>
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
        if(!LinkedUserManager::isLinkeduserWithId($linkedUserId))
            showErrorPage();

        // Get the linked user instance
        $linkedUser = new Linkeduser($linkedUserId);

        // Make sure the current logged in user is the same as the owner of the linked user
        if($linkedUser->getOwnerId() !== SessionManager::getLoggedInUser()->getId())
            showErrorPage();

        // TODO: Make sure the user is allowed to use his account as the specified user

        SessionManager::setActiveUser($linkedUser->getUser(), true);

        ?>
        <div data-role="page" id="page-register" data-unload="false">
            <?php PageHeaderBuilder::create(__('linkedUser', 'deleteLinkedUser'))->build(); ?>

            <div data-role="main" class="ui-content">
                <p>
                    <?= __('linkedUser', 'removedLinkeduserSuccessfully'); ?>
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

    } else {
        // Reset the active user
        SessionManager::setActiveUser(null, true);

        ?>
        <div data-role="page" id="page-register" data-unload="false">
            <?php PageHeaderBuilder::create(__('linkedUser', 'deleteLinkedUser'))->build(); ?>

            <div data-role="main" class="ui-content">
                <p>
                    <?= __('linkedUser', 'removedLinkeduserSuccessfully'); ?>
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
    if(!LinkedUserManager::isLinkeduserWithId($linkedUserId))
        showErrorPage();

    // Get the linked user instance
    $linkedUser = new Linkeduser($linkedUserId);

    ?>
    <div data-role="page" id="page-main">
        <?php PageHeaderBuilder::create(__('linkedUser', 'manageLinkedUser'))->setBackButton('linkedUsermanager.php')->build(); ?>

        <div data-role="main" class="ui-content">
            <center>
                <table class="ui-responsive">
                    <tr>
                        <td><?=__('linkedUser', 'linkedUserName'); ?></td>
                        <td><?=$linkedUser->getName(); ?></td>
                    </tr>
                    <tr>
                        <td><?=__('dateTime', 'creationDate'); ?></td>
                        <td><?=$linkedUser->getCreationDateTime()->toString(); ?></td>
                    </tr>
                    <?php
                    // Print the modification date time if set
                    if($linkedUser->getModificationDateTimeRaw() !== null) {
                        echo '<tr>';
                        echo '<td>' . __('dateTime', 'modificationDate') . '</td>';
                        echo '<td>' . $linkedUser->getModificationDateTime()->toString() . '</td>';
                        echo '</tr>';
                    }
                    ?>
                </table>
            </center>
            <br />

            <p>
                <?=__('linkedUser', 'pressButtonToChangeOrDelete'); ?>
            </p><br />

            <?php if($category !== null): ?>
                <fieldset data-role="controlgroup" data-type="vertical">
                    <a href="linkedusercategorymanager.php?category_id=<?=$category->getId(); ?>" class="ui-btn ui-icon-shop ui-btn-icon-left"><?=__('linkedUserCategory', 'viewLinkeduserCategory'); ?></a>
                </fieldset>
            <?php endif; ?>

            <fieldset data-role="controlgroup" data-type="vertical">
                <a href="linkedusermanager.php?linked_user_id=<?=$linkedUserId; ?>&a=change" class="ui-btn ui-icon-edit ui-btn-icon-left"><?=__('linkedUser', 'changeLinkedUser'); ?></a>
                <a href="linkedusermanager.php?linked_user_id=<?=$linkedUserId; ?>&a=delete" class="ui-btn ui-icon-delete ui-btn-icon-left"><?=__('linkedUser', 'deleteLinkedUser'); ?></a>
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
        <?php PageHeaderBuilder::create(__('linkedUser', 'manageInventories'))->setBackButton('index.php')->build(); ?>

        <div data-role="main" class="ui-content">
            <p>
                <?= __('linkedUser', 'clickOnLinkeduserToManageOrAdd'); ?>
            </p><br />

            <?php

            // Get all inventories
            $inventories = LinkedUserManager::getInventories();

            // Print the list top
            echo '<ul class="ui-listview" data-role="listview" id="listview-stations-last-occupied" data-inset="true">';

            // Print the actual list of inventories
            if(sizeof($inventories) > 0):
                ?>
                <li data-role="list-divider"><?= __('linkedUser', 'inventories'); ?></li>
                <?php
                // Put all inventories in the list
                foreach($inventories as $linkedUser) {
                    // Validate the instance
                    if(!($linkedUser instanceof Linkeduser))
                        continue;

                    // Print the item
                    echo '<li><a class="ui-btn ui-btn-icon-right ui-icon-carat-r" href="linkedusermanager.php?linked_user_id=' .
                        $linkedUser->getId() . '">' . $linkedUser->getName() . '</a></li>';
                }
            else:
                // There are no inventories yet, show a status message
                echo '<li><i>' . __('linkedUser', 'thereAreNoInventories') . '..</i></li>';
            endif;

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

