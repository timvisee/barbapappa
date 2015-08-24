<?php

use app\occupation\OccupationManager;
use app\picture\PictureManager;
use app\session\SessionManager;
use app\station\StationManager;
use app\team\TeamManager;
use app\template\PageFooterBuilder;
use app\template\PageHeaderBuilder;

// Include the page top
require_once('top.php');

?>

<div data-role="page" id="page-about" data-unload="false">
    <?php PageHeaderBuilder::create('Over')->setBackButton('index.php')->build(); ?>

    <div data-role="main" class="ui-content" align="center">
        <p><b><?=APP_NAME; ?> v<?=APP_VERSION_NAME; ?> <sup>(<?=APP_VERSION_CODE; ?>)</sup></b></p>
        <br />
        <p>De BarApp word ontwikkeld en bijgewerkt door <a href="http://timvisee.com/" target="_blank" title="About Tim Vis&eacute;e">Tim Vis&eacute;e</a>.</p>
        <br />
        <p>De broncode van deze applicatie is beschikbaar op <a href="http://github.com/timvisee/BarApp" target="_blank" title="Visit GitHub page">GitHub</a>.</p>
        <br />
        <fieldset data-role="controlgroup" data-type="vertical">
            <a href="status.php" class="ui-btn ui-icon-info ui-btn-icon-left">Applicatie Status</a>
        </fieldset>
        <br />
        <br />
        <p>Proudly powered by <a href="http://carboncms.nl/" target="_blank" title="Visit Carbon CMS">Carbon CMS</a>.</p>
        <br />
        <p>Licenced under MIT license.</p>
        <br />
        <p>Copyright &copy; Tim Vis&eacute;e <?=date('Y'); ?>.<br />All rights reserved.</p>
    </div>

    <?php PageFooterBuilder::create()->build(); ?>
</div>

<?php

// Include the page bottom
require_once('bottom.php');