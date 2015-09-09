<?php

use app\language\LanguageManager;
use app\template\PageFooterBuilder;
use app\template\PageHeaderBuilder;
use app\template\PageSidebarBuilder;

// Include the page top
require_once('top.php');

// Make sure the required post fields are set
if(!isset($_GET['lang_tag']))
    showErrorPage();

// Get the language tag, and make sure it's valid
$langTag = trim($_GET['lang_tag']);
if(!LanguageManager::isWithTag($langTag))
    showErrorPage();

// Set the preferred language
LanguageManager::setLanguageTag($langTag);

// TODO:Redirect the user?
//header('Location: index.php');

?>
<div data-role="page" id="page-main">
    <?php PageHeaderBuilder::create(__('pageLanguage', 'languageChanged'))->setBackButton('index.php')->build(); ?>

    <div data-role="main" class="ui-content">
        <p><?=__('pageLanguage', 'languageChangedSuccessfully'); ?><br /><br />

        <fieldset data-role="controlgroup" data-type="vertical">
            <a href="index.php" data-ajax="false" data-rel="back" class="ui-btn ui-icon-back ui-btn-icon-left" data-direction="reverse"><?=__('navigation', 'goBack'); ?></a>
        </fieldset>
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
