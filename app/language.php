<?php

use app\language\LanguageManager;
use app\session\SessionManager;
use app\team\Team;
use app\team\TeamManager;
use app\template\PageFooterBuilder;
use app\template\PageHeaderBuilder;
use carbon\core\util\StringUtils;

// Include the page top
require_once('top.php');

// Make sure the required post fields are set
if(!isset($_GET['lang_tag']))
    showErrorPage();

// Get the language tag, and make sure it's valid
$langTag = trim($_GET['lang_tag']);
if(!LanguageManager::hasWithTag($langTag))
    showErrorPage();

// Set the user language
LanguageManager::setUserLanguageTag($langTag);
LanguageManager::setUserLanguageTagCookie($langTag);

// Redirect the user
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

    <?php PageFooterBuilder::create()->build(); ?>
</div>
<?php

// Include the page bottom
require_once('bottom.php');