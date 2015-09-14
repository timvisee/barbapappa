<?php

use app\language\LanguageManager;
use app\inventory\Inventory;
use app\inventory\InventoryManager;
use app\session\SessionManager;
use app\template\PageFooterBuilder;
use app\template\PageHeaderBuilder;
use app\template\PageSidebarBuilder;
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
    if(!isset($_POST['inventory-name'])) {
        ?>
        <div data-role="page" id="page-main">
            <?php PageHeaderBuilder::create(__('inventory', 'addInventory'))->setBackButton('inventorymanager.php')->build(); ?>

            <div data-role="main" class="ui-content">
                <p><?=__('inventory', 'fillInFieldsToAddInventory'); ?></p><br />

                <form method="POST" action="inventorymanager.php?a=add&step=2">
                    <ul data-role="listview" data-inset="true">
                        <li data-role="list-divider"><?=__('inventory', 'inventorySpecifications'); ?></li>
                        <li class="ui-field-contain">
                            <label for="inventory-name"><?=__('inventory', 'inventoryName'); ?>:</label>
                            <input name="inventory-name" id="inventory-name" value="" type="text">
                        </li>
                    </ul>
                    <br />

                    <input type="submit" value="<?= __('inventory', 'addInventory'); ?>" class="ui-btn ui-icon-lock ui-btn-icon-right" />
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
        $inventoryName = trim($_POST['inventory-name']);

        // Validate the inventory name
        if(strlen($inventoryName) == 0)
            showErrorPage(__('inventory', 'invalidInventoryName'));

        // Add the inventory
        $inventory = InventoryManager::createInventory($inventoryName);

        ?>
        <div data-role="page" id="page-register" data-unload="false">
            <?php PageHeaderBuilder::create(__('inventory', 'addInventory'))->build(); ?>

            <div data-role="main" class="ui-content">
                <p>
                    <?= __('inventory', 'addedInventorySuccessfully'); ?>
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
    // Make sure the inventory ID is set
    if(!isset($_GET['inventory_id']))
        showErrorPage();

    // Get the inventory ID and make sure it's valid
    $inventoryId = $_GET['inventory_id'];
    if(!InventoryManager::isInventoryWithId($inventoryId))
        showErrorPage();

    // Get the inventory instance
    $inventory = new Inventory($inventoryId);

    if(!isset($_POST['inventory-name'])) {
        ?>
        <div data-role="page" id="page-main">
            <?php PageHeaderBuilder::create(__('inventory', 'changeInventory'))->setBackButton('inventorymanager.php')->build(); ?>

            <div data-role="main" class="ui-content">
                <p><?=__('inventory', 'fillInFieldsToChangeInventory'); ?></p><br />

                <form method="POST" action="inventorymanager.php?a=change&inventory_id=<?=$inventoryId; ?>&step=2">
                    <ul data-role="listview" data-inset="true">
                        <li data-role="list-divider"><?=__('inventory', 'inventorySpecifications'); ?></li>
                        <li class="ui-field-contain">
                            <label for="inventory-name"><?=__('inventory', 'inventoryName'); ?>:</label>
                            <input name="inventory-name" id="inventory-name" value="<?=$inventory->getName(); ?>" type="text">
                        </li>
                    </ul>
                    <br />

                    <input type="submit" value="<?= __('inventory', 'changeInventory'); ?>" class="ui-btn ui-icon-lock ui-btn-icon-right" />
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
        $inventoryName = trim($_POST['inventory-name']);

        // Validate the inventory name
        if(strlen($inventoryName) == 0)
            showErrorPage(__('inventory', 'invalidInventoryName'));

        // Set the name if it's different
        if(!StringUtils::equals($inventory->getName(), $inventoryName, true))
            $inventory->setName($inventoryName);

        ?>
        <div data-role="page" id="page-register" data-unload="false">
            <?php PageHeaderBuilder::create(__('inventory', 'changeInventory'))->build(); ?>

            <div data-role="main" class="ui-content">
                <p>
                    <?= __('inventory', 'changedInventorySuccessfully'); ?>
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
    // Make sure the inventory ID is set
    if(!isset($_GET['inventory_id']))
        showErrorPage();

    // Get the inventory ID and make sure it's valid
    $inventoryId = $_GET['inventory_id'];
    if(!InventoryManager::isInventoryWithId($inventoryId))
        showErrorPage();

    // Get the inventory instance
    $inventory = new Inventory($inventoryId);

    if(!isset($_POST['agree'])) {
        ?>
        <div data-role="page" id="page-register" data-unload="false">
            <?php PageHeaderBuilder::create(__('inventory', 'deleteInventory'))->setBackButton('index.php')->build(); ?>

            <div data-role="main" class="ui-content">
                <p><?=__('inventory', 'aboutToRemoveInventoryNotReversible'); ?></p><br />

                <form method="POST" action="inventorymanager.php?a=delete&inventory_id=<?=$inventoryId; ?>&step=2">
                    <center>
                        <table class="ui-responsive">
                            <tr>
                                <td><?=__('inventory', 'inventoryName'); ?></td>
                                <td><?=$inventory->getName(); ?></td>
                            </tr>
                        </table>
                    </center>
                    <br />

                    <label for="agree"><?= __('inventory', 'youSureRemoveInventory'); ?></label>
                    <select id="agree" name="agree" data-role="slider">
                        <option value="0"><?= __('general', 'acceptanceNo'); ?></option>
                        <option value="1"><?= __('general', 'acceptanceYes'); ?></option>
                    </select>

                    <input type="submit" value="<?= __('inventory', 'deleteInventory'); ?>"
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
            showErrorPage(__('inventory', 'mustAgreeToRemoveInventory'));

        // Delete the inventory
        $inventory->delete();
        $inventory = null;

        ?>
        <div data-role="page" id="page-register" data-unload="false">
            <?php PageHeaderBuilder::create(__('inventory', 'deleteInventory'))->build(); ?>

            <div data-role="main" class="ui-content">
                <p>
                    <?= __('inventory', 'removedInventorySuccessfully'); ?>
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

} else if(isset($_GET['inventory_id'])) {
    // Get the inventory ID
    $inventoryId = trim($_GET['inventory_id']);

    // Make sure the inventory ID
    if(!InventoryManager::isInventoryWithId($inventoryId))
        showErrorPage();

    // Get the inventory instance
    $inventory = new Inventory($inventoryId);

    ?>
    <div data-role="page" id="page-main">
        <?php PageHeaderBuilder::create(__('inventory', 'manageInventory'))->setBackButton('inventorymanager.php')->build(); ?>

        <div data-role="main" class="ui-content">
            <center>
                <table class="ui-responsive">
                    <tr>
                        <td><?=__('inventory', 'inventoryName'); ?></td>
                        <td><?=$inventory->getName(); ?></td>
                    </tr>
                    <tr>
                        <td><?=__('dateTime', 'creationDate'); ?></td>
                        <td><?=$inventory->getCreationDateTime()->toString(); ?></td>
                    </tr>
                    <?php
                    // Print the modification date time if set
                    if($inventory->getModificationDateTimeRaw() !== null) {
                        echo '<tr>';
                        echo '<td>' . __('dateTime', 'modificationDate') . '</td>';
                        echo '<td>' . $inventory->getModificationDateTime()->toString() . '</td>';
                        echo '</tr>';
                    }
                    ?>
                </table>
            </center>
            <br />

            <p>
                <?=__('inventory', 'pressButtonToChangeOrDelete'); ?>
            </p><br />

            <?php if($category !== null): ?>
                <fieldset data-role="controlgroup" data-type="vertical">
                    <a href="inventorycategorymanager.php?category_id=<?=$category->getId(); ?>" class="ui-btn ui-icon-shop ui-btn-icon-left"><?=__('inventoryCategory', 'viewInventoryCategory'); ?></a>
                </fieldset>
            <?php endif; ?>

            <fieldset data-role="controlgroup" data-type="vertical">
                <a href="inventorymanager.php?inventory_id=<?=$inventoryId; ?>&a=change" class="ui-btn ui-icon-edit ui-btn-icon-left"><?=__('inventory', 'changeInventory'); ?></a>
                <a href="inventorymanager.php?inventory_id=<?=$inventoryId; ?>&a=delete" class="ui-btn ui-icon-delete ui-btn-icon-left"><?=__('inventory', 'deleteInventory'); ?></a>
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
        <?php PageHeaderBuilder::create(__('inventory', 'manageInventories'))->setBackButton('index.php')->build(); ?>

        <div data-role="main" class="ui-content">
            <p>
                <?= __('inventory', 'clickOnInventoryToManageOrAdd'); ?>
            </p><br />

            <?php

            // Get all inventories
            $inventories = InventoryManager::getInventories();

            // Print the list top
            echo '<ul class="ui-listview" data-role="listview" id="listview-stations-last-occupied" data-inset="true">';

            // Print the actual list of inventories
            if(sizeof($inventories) > 0):
                ?>
                <li data-role="list-divider"><?= __('inventory', 'inventories'); ?></li>
                <?php
                // Put all inventories in the list
                foreach($inventories as $inventory) {
                    // Validate the instance
                    if(!($inventory instanceof Inventory))
                        continue;

                    // Print the item
                    echo '<li><a class="ui-btn ui-btn-icon-right ui-icon-carat-r" href="inventorymanager.php?inventory_id=' .
                        $inventory->getId() . '">' . $inventory->getName() . '</a></li>';
                }
            else:
                // There are no inventories yet, show a status message
                echo '<li><i>' . __('inventory', 'thereAreNoInventories') . '..</i></li>';
            endif;

            // Print the list bottom
            echo '</ul>';

            ?>

            <fieldset data-role="controlgroup" data-type="vertical">
                <a href="inventorymanager.php?a=add" class="ui-btn ui-icon-plus ui-btn-icon-left"><?= __('inventory', 'addInventory'); ?></a>
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

