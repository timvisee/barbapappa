<?php

use app\product\Product;
use app\product\ProductManager;
use app\session\SessionManager;
use app\template\PageFooterBuilder;
use app\template\PageHeaderBuilder;
use app\template\PageSidebarBuilder;

// Include the page top
require_once('top.php');

// Make sure the user is logged in
if(SessionManager::isLoggedIn())
    requireLogin();

?>
<div data-role="page" id="page-login">
    <?php PageHeaderBuilder::create()->setMenuButton(true)->build(); ?>

    <div data-role="main" class="ui-content">

        <?php if(SessionManager::isLoggedIn()): ?>
            <center>
                <table class="ui-responsive">
                    <tr>
                        <td><?=__('general', 'myBalance'); ?></td>
                        <td><span style="font-size: 130%;"><?=SessionManager::getLoggedInUser()->getBalanceTotal()->getFormatted(); ?></span></td>
                    </tr>
                </table>
            </center>
            <?php

            // Get all products
            $products = ProductManager::getProducts();

            // Print the list top
            echo '<ul id="list-quick-buy" data-role="listview" data-split-icon="bars" data-inset="true">';

            // Print the actual list of products
            if(sizeof($products) > 0):
                ?>
                <li data-role="list-divider"><span style="float: left;"><?=__('quickBuy', 'quickBuy'); ?></span><span style="color: gray; float: right;"><?=__('quickBuy', 'advanced'); ?> &#8628;</span></li>
                <?php
                // Put all products in the list
                foreach($products as $product) {
                    // Validate the instance
                    if(!($product instanceof Product))
                        continue;

                    // Print the item
                    echo '<li>';
                    echo '<a href="quickbuy.php?product_id=' . $product->getId() . '" data-transition="slidedown">' . $product->getNameTranslated() . '<span class="ui-li-count">' . $product->getPrice()->getFormatted() . '</span></a>';
                    echo '<a href="productmanager.php?a=change&product_id=' . $product->getId() . '" data-position-to="window" data-transition="pop">' . __('quickBuy', 'advanced') . '...</a>';
                    echo '</li>';
                }
            endif;

            // Print the list bottom
            echo '</ul><br />';
        endif;

        if(!SessionManager::isLoggedIn()): ?>
        <fieldset data-role="controlgroup" data-type="vertical">
            <a href="login.php" class="ui-btn ui-icon-user ui-btn-icon-left"><?= __('account', 'login'); ?></a>
            <a href="register.php" class="ui-btn ui-icon-user ui-btn-icon-left"><?= __('account', 'register'); ?></a>
        </fieldset>
        <?php else: ?>
        <fieldset data-role="controlgroup" data-type="vertical">
            <a href="productmanager.php" class="ui-btn ui-icon-shop ui-btn-icon-left"><?=__('product', 'manageProducts'); ?></a>
            <a href="productcategorymanager.php" class="ui-btn ui-icon-shop ui-btn-icon-left"><?=__('productCategory', 'manageProductCategories'); ?></a>
            <a href="mailmanager.php" class="ui-btn ui-icon-mail ui-btn-icon-left"><?=__('mail', 'manageMailAddresses'); ?></a>
        </fieldset>
        <fieldset data-role="controlgroup" data-type="vertical">
            <a href="logout.php" class="ui-btn ui-icon-delete ui-btn-icon-left" data-direction="reverse"><?=__('account', 'logout'); ?></a>
        </fieldset>
        <?php endif; ?>
    </div>

    <?php
    // Build the footer and sidebar
    PageFooterBuilder::create()->build();
    PageSidebarBuilder::create()->build();
    ?>
</div>

<?php

// Include the page bottom
require_once('bottom.php');
