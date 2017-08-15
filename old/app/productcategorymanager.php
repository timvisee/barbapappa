<?php

use app\database\DatabaseValueTranslations;
use app\language\Language;
use app\language\LanguageManager;
use app\product\category\ProductCategory;
use app\product\category\ProductCategoryManager;
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
    if(!isset($_POST['category-name'])) {
        ?>
        <div data-role="page" id="page-main">
            <?php PageHeaderBuilder::create(__('productCategory', 'addProductCategory'))->setBackButton('productcategorymanager.php')->build(); ?>

            <div data-role="main" class="ui-content">
                <p><?=__('productCategory', 'fillInFieldsToAddProductCategory'); ?></p><br />

                <form method="POST" action="productcategorymanager.php?a=add&step=2">
                    <ul data-role="listview" data-inset="true">
                        <li data-role="list-divider"><?=__('productCategory', 'productCategorySpecifications'); ?></li>
                        <li class="ui-field-contain">
                            <label for="category-name"><?=__('productCategory', 'categoryName'); ?>:</label>
                            <input name="category-name" id="category-name" value="" type="text">

                            <label for="category-name-translations"></label>
                            <div for="category-name-translations" data-role="collapsible">
                                <h4><?=__('productCategory', 'nameTranslations'); ?></h4>
                                <?php
                                // Get all languages
                                $languages = LanguageManager::getLanguages();

                                // Create a field for all languages
                                foreach($languages as $language) {
                                    // Validate the instance
                                    if(!($language instanceof Language))
                                        continue;

                                    // Get the language tag
                                    $tag = $language->getTag();

                                    // Print the input field
                                    echo '<label for="category-name-' . $tag . '">' . $language->get('language', 'thisLanguage') . ':</label>';
                                    echo '<input name="category-name-' . $tag . '" id="category-name-' . $tag . '" value="" type="text" placeholder="' . __('general', 'optional') . '" data-clear-btn="true">';
                                }
                                ?>
                            </div>
                        </li>
                        <li class="ui-field-contain">
                            <label for="category-parent-id"><?=__('productCategory', 'parentCategory'); ?>:</label>
                            <select name="category-parent-id" id="category-parent-id">
                                <option value="" selected="selected"><?=__('productCategory', 'noParentCategory'); ?></option>
                                <?php
                                // Get the categories
                                $categories = ProductCategoryManager::getProductCategories();

                                // Print the items
                                foreach($categories as $category) {
                                    // Make sure the instance is valid
                                    if(!($category instanceof ProductCategory))
                                        continue;

                                    // Print the item
                                    echo '<option value="' . $category->getId() . '">' . $category->getNameTranslated() . '</option>';
                                }
                                ?>
                            </select>
                        </li>
                    </ul>
                    <br />

                    <input type="submit" value="<?= __('productCategory', 'addProductCategory'); ?>" class="ui-btn ui-icon-lock ui-btn-icon-right" />
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
        $categoryName = trim($_POST['category-name']);
        $parentCategoryId = trim($_POST['category-parent-id']);

        // Validate the product category name
        if(strlen($categoryName) == 0)
            showErrorPage();

        // Create a database value translations object to store translations in
        $nameTranslations = new DatabaseValueTranslations(null, $categoryName);

        // Create a field for all languages
        foreach($languages as $language) {
            // Validate the instance
            if(!($language instanceof Language))
                continue;

            // Get the language tag and POST name
            $tag = $language->getTag();
            $name = 'category-name-' . $tag;

            // Make sure a name is set for this language
            if(!isset($_POST[$name]) || strlen(trim($_POST[$name])) <= 0)
                continue;

            // Get and set the translation
            $nameTranslations->setTranslation($tag, trim($_POST[$name]));
        }

        // Get the parent product category instance
        $parentCategory = null;

        // Validate the parent product category ID
        if(strlen($parentCategoryId) > 0) {
            // Make sure the product category ID exists
            if(!ProductCategoryManager::isProductCategoryWithId($parentCategoryId))
                showErrorPage();

            // Get the product category instance
            $parentCategory = new ProductCategory($parentCategoryId);
        }

        // Add the product category and set the translations
        $productCategory = ProductCategoryManager::createProductCategory($parentCategory, $categoryName);
        $productCategory->setTranslations($nameTranslations);

        ?>
        <div data-role="page" id="page-register" data-unload="false">
            <?php PageHeaderBuilder::create(__('productCategory', 'addProductCategory'))->build(); ?>

            <div data-role="main" class="ui-content">
                <p>
                    <?= __('productCategory', 'addedProductCategorySuccessfully'); ?>
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
    // Make sure the product category ID is set
    if(!isset($_GET['category_id']))
        showErrorPage();

    // Get the product category ID and make sure it's valid
    $categoryId = $_GET['category_id'];
    if(!ProductCategoryManager::isProductCategoryWithId($categoryId))
        showErrorPage();

    // Get the product category instance
    $productCategory = new ProductCategory($categoryId);

    // Get all translations
    $translations = $productCategory->getTranslations();

    if(!isset($_POST['category-name'])) {
        ?>
        <div data-role="page" id="page-main">
            <?php PageHeaderBuilder::create(__('productCategory', 'changeProductCategory'))->setBackButton('productcategorymanager.php')->build(); ?>

            <div data-role="main" class="ui-content">
                <p><?=__('productCategory', 'fillInFieldsToChangeProductCategory'); ?></p><br />

                <form method="POST" action="productcategorymanager.php?a=change&category_id=<?=$categoryId; ?>&step=2">
                    <ul data-role="listview" data-inset="true">
                        <li data-role="list-divider"><?=__('productCategory', 'productCategorySpecifications'); ?></li>
                        <li class="ui-field-contain">
                            <label for="category-name"><?=__('productCategory', 'categoryName'); ?>:</label>
                            <input name="category-name" id="category-name" value="<?=$productCategory->getName(); ?>" type="text">

                            <label for="category-name-translations"></label>
                            <div for="category-name-translations" data-role="collapsible">
                                <h4><?=__('productCategory', 'nameTranslations'); ?></h4>
                                <?php
                                // Create a field for all languages
                                foreach($languages as $language) {
                                    // Validate the instance
                                    if(!($language instanceof Language))
                                        continue;

                                    // Get the language tag
                                    $tag = $language->getTag();

                                    // Determine the value
                                    $value = '';
                                    if($translations->hasTranslation($tag))
                                        $value = $translations->getValue($tag);
                                    // Print the input field
                                    echo '<label for="category-name-' . $tag . '">' . $language->get('language', 'thisLanguage') . ':</label>';
                                    echo '<input name="category-name-' . $tag . '" id="category-name-' . $tag . '" value="' . $value . '" type="text" placeholder="' . __('general', 'optional') . '" data-clear-btn="true">';
                                }
                                ?>
                            </div>
                        </li>
                        <li class="ui-field-contain">
                            <label for="category-parent-id"><?=__('productCategory', 'category'); ?>:</label>
                            <select name="category-parent-id" id="category-parent-id">
                                <option value=""><?=__('productCategory', 'noCategory'); ?></option>
                                <?php
                                // TODO: Do not show the current category!

                                // Get the categories
                                $categories = ProductCategoryManager::getProductCategories();
                                $currentParentCategory = $productCategory->getParentCategory();

                                // Print the items
                                foreach($categories as $category) {
                                    // Make sure the instance is valid
                                    if(!($category instanceof ProductCategory))
                                        continue;

                                    // Make sure the parent category isn't the current
                                    if($productCategory->getId() == $category->getId())
                                        continue;

                                    // Determine whether to add a selected tag
                                    $selected = '';
                                    if($currentParentCategory !== null && $currentParentCategory->getId() == $category->getId())
                                        $selected = 'selected="selected" ';

                                    // Print the item
                                    echo '<option value="' . $category->getId() . '" ' . $selected . '>' . $category->getNameTranslated() . '</option>';
                                }
                                ?>
                            </select>
                        </li>
                    </ul>
                    <br />

                    <input type="submit" value="<?= __('productCategory', 'changeProductCategory'); ?>" class="ui-btn ui-icon-lock ui-btn-icon-right" />
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
        $categoryName = trim($_POST['category-name']);
        $parentCategoryId = trim($_POST['category-parent-id']);

        // Validate the product category name
        if(strlen($categoryName) == 0)
            showErrorPage();

        // Create a database value translations object to store translations in
        $nameTranslations = new DatabaseValueTranslations(null, $categoryName);

        // Create a field for all languages
        foreach($languages as $language) {
            // Validate the instance
            if(!($language instanceof Language))
                continue;

            // Get the language tag and POST name
            $tag = $language->getTag();
            $name = 'category-name-' . $tag;

            // Make sure a name is set for this language
            if(!isset($_POST[$name]) || strlen(trim($_POST[$name])) <= 0)
                continue;

            // Get and set the translation
            $nameTranslations->setTranslation($tag, trim($_POST[$name]));
        }

        // Get the parent product category instance
        $productParentCategory = null;

        // Validate the product category ID
        if(strlen($parentCategoryId) > 0) {
            // Make sure the parent product category ID exists
            if(!ProductCategoryManager::isProductCategoryWithId($parentCategoryId))
                showErrorPage();

            // Get the parent product category instance
            $productParentCategory = new ProductCategory($parentCategoryId);
        }

        // Set the name if it's different
        if(!StringUtils::equals($productCategory->getName(), $categoryName, true))
            $productCategory->setName($categoryName);

        // Set the translations if they're different
        if(!StringUtils::equals($productCategory->getTranslations()->getValuesEncoded(), $nameTranslations->getValuesEncoded(), true))
            $productCategory->setTranslations($nameTranslations);

        // Set the parent product category if it's different
        if($productCategory->getParentCategoryId() != $parentCategoryId) {
            // Set the category
            $productCategory->setParentCategory($productParentCategory);

            // Make sure the parent category isn't parent of this category
            if($productParentCategory !== null && $productParentCategory->getParentCategoryId() == $productCategory->getId())
                $productParentCategory->setParentCategory(null);
        }

        ?>
        <div data-role="page" id="page-register" data-unload="false">
            <?php PageHeaderBuilder::create(__('productCategory', 'changeProductCategory'))->build(); ?>

            <div data-role="main" class="ui-content">
                <p>
                    <?= __('productCategory', 'changedProductCategorySuccessfully'); ?>
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
    // Make sure the product ID is set
    if(!isset($_GET['category_id']))
        showErrorPage();

    // Get the product ID and make sure it's valid
    $categoryId = $_GET['category_id'];
    if(!ProductCategoryManager::isProductCategoryWithId($categoryId))
        showErrorPage();

    // Get the product category instance
    $productCategory = new ProductCategory($categoryId);

    if(!isset($_POST['agree'])) {
        ?>
        <div data-role="page" id="page-register" data-unload="false">
            <?php PageHeaderBuilder::create(__('productCategory', 'deleteProductCategory'))->setBackButton('index.php')->build(); ?>

            <div data-role="main" class="ui-content">
                <p><?=__('productCategory', 'aboutToRemoveProductCategoryNotReversible'); ?></p><br />

                <form method="POST" action="productcategorymanager.php?a=delete&category_id=<?=$categoryId; ?>&step=2">
                    <center>
                        <table class="ui-responsive">
                            <tr>
                                <td><?=__('productCategory', 'categoryName'); ?></td>
                                <td><?=$productCategory->getName(); ?></td>
                            </tr>
                            <!-- TODO: Show parent category. -->
                        </table>
                    </center>
                    <br />

                    <label for="agree"><?= __('productCategory', 'youSureRemoveProductCategory'); ?></label>
                    <select id="agree" name="agree" data-role="slider">
                        <option value="0"><?= __('general', 'acceptanceNo'); ?></option>
                        <option value="1"><?= __('general', 'acceptanceYes'); ?></option>
                    </select>

                    <input type="submit" value="<?= __('productCategory', 'deleteProductCategory'); ?>"
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
            showErrorPage(__('productCategory', 'mustAgreeToRemoveProductCategory'));

        // Delete the product
        $productCategory->delete();
        $productCategory = null;

        ?>
        <div data-role="page" id="page-register" data-unload="false">
            <?php PageHeaderBuilder::create(__('productCategory', 'deleteProductCategory'))->build(); ?>

            <div data-role="main" class="ui-content">
                <p>
                    <?= __('productCategory', 'removedProductCategorySuccessfully'); ?>
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

} else if(isset($_GET['category_id'])) {
    // Get the product ID
    $categoryId = trim($_GET['category_id']);

    // Make sure the product ID
    if(!ProductCategoryManager::isProductCategoryWithId($categoryId))
        showErrorPage();

    // Get the product category instance
    $productCategory = new ProductCategory($categoryId);

    // Get the parent product category
    $productParentCategory = $productCategory->getParentCategory();

    ?>
    <div data-role="page" id="page-main">
        <?php PageHeaderBuilder::create(__('productCategory', 'manageProductCategory'))->setBackButton('productcategorymanager.php')->build(); ?>

        <div data-role="main" class="ui-content">
            <center>
                <table class="ui-responsive">
                    <tr>
                        <td><?=__('productCategory', 'categoryName'); ?></td>
                        <td><?=$productCategory->getName(); ?></td>
                    </tr>
                    <?php

                    // Get all product translations
                    $translations = $productCategory->getTranslations();
                    $languages = $translations->getLanguages();

                    // Print a row for each language
                    foreach($languages as $language) {
                        // Validate the instance
                        if(!($language instanceof Language))
                            continue;

                        // Print the row
                        echo '<tr>';
                        echo '<td><i style="font-weight: normal; color: gray;">' . $language->get('language', 'thisLanguage') . '</i></td>';
                        echo '<td>' . $translations->getValue($language) . '</td>';
                        echo '</tr>';
                    }

                    ?>
                    <tr>
                        <td><?=__('productCategory', 'parentCategory'); ?></td>
                        <td>
                            <?php
                            // Print the category if there is any
                            if($productParentCategory !== null)
                                echo $productParentCategory->getNameTranslated();
                            else
                                echo '<i style="color: gray;">' . __('general', 'textNone') . '</i>';
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?=__('dateTime', 'creationDate'); ?></td>
                        <td><?=$productCategory->getCreationDateTime()->toString(); ?></td>
                    </tr>
                    <!-- TODO: Make sure all variables are shown! -->
                </table>
            </center>
            <br />

            <p>
                <?=__('productCategory', 'pressButtonToChangeOrDelete'); ?>
            </p><br />

            <fieldset data-role="controlgroup" data-type="vertical">
                <a href="productcategorymanager.php?category_id=<?=$categoryId; ?>&a=change" class="ui-btn ui-icon-edit ui-btn-icon-left"><?=__('productCategory', 'changeProductCategory'); ?></a>
                <a href="productcategorymanager.php?category_id=<?=$categoryId; ?>&a=delete" class="ui-btn ui-icon-delete ui-btn-icon-left"><?=__('productCategory', 'deleteProductCategory'); ?></a>
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
        <?php PageHeaderBuilder::create(__('productCategory', 'manageProductCategories'))->setBackButton('index.php')->build(); ?>

        <div data-role="main" class="ui-content">
            <p>
                <?= __('productCategory', 'clickOnProductCategoryToManageOrAdd'); ?>
            </p><br />

            <ul data-role="listview" data-split-icon="bars" class="ui-listview-outer" data-inset="true">
                <li data-role="list-divider"><?= __('productCategory', 'productCategories'); ?></li>
                <?php
                function printCategoryTree($productCategory) {
                    // Get the children
                    $children = ProductCategoryManager::getProductCategoryChildren($productCategory);

                    if($productCategory !== null) {
                        // Get the name
                        $name = $productCategory->getNameTranslated();

                        echo '<li data-role="collapsible" data-iconpos="right" data-shadow="false" data-corners="false">';
                        echo '<h2>' . $name . '</h2>';
                        echo '<ul data-role="listview" data-shadow="false" data-inset="true" data-corners="false">';
                        echo '<li><a href="productcategorymanager.php?category_id=' . $productCategory->getId() . '"><i>' . __('general', 'change') . ' ' . $name . '</i><span class="ui-li-count">?</span></a></li>';
                    }

                    if($productCategory !== null && sizeof($children) > 0)
                        echo '<li data-role="list-divider">' . __('productCategory', 'subCategories') . '</li>';

                    foreach($children as $child) {
                        // Validate the instance
                        if(!($child instanceof ProductCategory))
                            continue;

                        printCategoryTree($child);
                    }

                    if($productCategory !== null)
                        echo '</ul></li>';
                }

                // Print the root categories
                printCategoryTree(null);
                ?>
            </ul>

            <fieldset data-role="controlgroup" data-type="vertical">
                <a href="productcategorymanager.php?a=add" class="ui-btn ui-icon-plus ui-btn-icon-left"><?= __('productCategory',
                        'addProductCategory'); ?></a>
                <a href="productmanager.php" class="ui-btn ui-icon-shop ui-btn-icon-left"><?=__('product', 'manageProducts'); ?></a>
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

