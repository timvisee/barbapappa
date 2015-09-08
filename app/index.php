<?php

use app\language\LanguageManager;
use app\product\Product;
use app\product\ProductManager;
use app\session\SessionManager;
use app\template\PageFooterBuilder;
use app\template\PageHeaderBuilder;
use carbon\core\util\StringUtils;

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
            <br />
        <?php endif; ?>

        <p><?=__('general', 'welcome'); ?><?php
            if(SessionManager::isLoggedIn())
                echo ' ' . SessionManager::getLoggedInUser()->getFullName();
            ?>!</p><br />

        <?php

        // Get all products
        $products = ProductManager::getProducts();

        // Print the list top
        echo '<ul data-role="listview" data-split-icon="bars"  data-inset="true">';

        // Print the actual list of products
        if(sizeof($products) > 0):
            ?>
            <li data-role="list-divider"><span style="float: left;">Quick Buy</span><span style="color: gray; float: right;">Advanced &#8628;</span></li>
            <?php
            // Put all products in the list
            foreach($products as $product) {
                // Validate the instance
                if(!($product instanceof Product))
                    continue;

                // Print the item
                echo '<li>';
                echo '<a href="#">' . $product->getNameTranslated() . '<span class="ui-li-count">' . $product->getPrice()->getFormatted() . '</span></a>';
                echo '<a href="" data-position-to="window" data-transition="pop">Buy options...</a>';
                echo '</li>';
            }
        endif;

        // Print the list bottom
        echo '</ul><br />';

        if(!SessionManager::isLoggedIn()): ?>
        <fieldset data-role="controlgroup" data-type="vertical">
            <a href="login.php" class="ui-btn ui-icon-user ui-btn-icon-left"><?= __('account', 'login'); ?></a>
            <a href="register.php" class="ui-btn ui-icon-user ui-btn-icon-left"><?= __('account', 'register'); ?></a>
        </fieldset>
        <?php else: ?>
        <fieldset data-role="controlgroup" data-type="vertical">
            <a href="productmanager.php" class="ui-btn ui-icon-shop ui-btn-icon-left">[ALPHA] <?=__('product', 'manageProducts'); ?></a>
            <a href="mailmanager.php" class="ui-btn ui-icon-mail ui-btn-icon-left"><?=__('mail', 'manageMailAddresses'); ?></a>
        </fieldset>
        <fieldset data-role="controlgroup" data-type="vertical">
            <a href="logout.php" class="ui-btn ui-icon-delete ui-btn-icon-left" data-direction="reverse"><?=__('account', 'logout'); ?></a>
        </fieldset>
        <?php endif; ?>
    </div>

    <?php PageFooterBuilder::create()->build(); ?>

    <div data-role="panel" id="main-panel" data-position="left" data-display="reveal" data-theme="a">
        <h3>Sidebar menu</h3>
        <p>This is the sidebar menu panel.</p><br />
        <a href="#demo-links" data-rel="close" class="ui-btn ui-shadow ui-corner-all ui-btn-a ui-icon-delete ui-btn-icon-left ui-btn-inline">Close panel</a>
        <?php
        if(!StringUtils::equals(LanguageManager::getPreferredLanguage()->getTag(), 'nl-NL'))
            echo '<a href="language.php?lang_tag=nl-NL" class="ui-btn ui-shadow ui-corner-all ui-btn-a"><img src="style/image/flag/nl.png" /></a>';
        else
            echo '<a href="language.php?lang_tag=en-US" class="ui-btn ui-corner-all ui-shadow"><img src="style/image/flag/gb.png" /></a>';
        ?>
    </div>
</div>

<?php

// Include the page bottom
require_once('bottom.php');
