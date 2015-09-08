<?php

use app\product\Product;
use app\product\ProductManager;
use app\session\SessionManager;
use app\template\PageHeaderBuilder;

// Print the top of the page
require_once('top.php');

// Make sure the product ID is set
if(!isset($_GET['product_id']))
    showErrorPage();

// Get the product ID and make sure it's valid
$productId = trim($_GET['product_id']);
if(!ProductManager::isProductWithId($productId))
    showErrorPage();

// Get the product instance
$product = new Product($productId);

?>

<div data-role="page" class="dialog-slide-top" data-dialog="true">
    <?php PageHeaderBuilder::create(__('quickBuy', 'quickBuy'))->setCloseButton(true)->build(); ?>

    <div role="main" class="ui-content">
        <p><?=__('quickBuy', 'youSureYouWantToBuyThisProduct'); ?></p><br />

        <center>
            <table class="ui-responsive">
                <tr>
                    <td><?=__('product', 'product'); ?></td>
                    <td><?=$product->getNameTranslated(); ?></td>
                </tr>
                <tr>
                    <td><?=__('product', 'price'); ?></td>
                    <td><?=$product->getPrice()->getFormatted(); ?></td>
                </tr>
                <tr>
                    <td><?=__('general', 'myBalance'); ?></td>
                    <td><?=SessionManager::getLoggedInUser()->getBalanceTotal()->getFormatted(true, true); ?></td>
                </tr>
            </table>
        </center>
        <br />

        <fieldset data-role="controlgroup" data-type="vertical">
            <a href="index.php" data-rel="back" data-direction="reverse" class="ui-btn ui-shadow ui-corner-all ui-btn-a"><?=__('quickBuy', 'buyProduct'); ?></a>
            <a href="index.php" data-rel="back" data-direction="reverse" class="ui-btn ui-shadow ui-corner-all ui-btn-a"><?=__('navigation', 'cancel'); ?></a>
        </fieldset>
    </div>
</div>

<?php

// Print the bottom of the page
require_once('bottom.php');