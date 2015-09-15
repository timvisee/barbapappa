<?php

namespace app\template;

use app\language\LanguageManager;
use app\session\SessionManager;
use app\user\linked\LinkedUser;
use app\user\linked\LinkedUserManager;
use carbon\core\util\StringUtils;

// Prevent direct requests to this file due to security reasons
defined('APP_INIT') or die('Access denied!');

class PageSidebarBuilder {

    /**
     * Constructor.
     */
    public function __construct() { }

    /**
     * Alternate constructor.
     * This constructor allows method chaining.
     *
     * @param string|null $title [optional] The title, or null to use the default title.
     *
     * @return PageHeaderBuilder The instance.
     */
    public static function create($title = null) {
        return new self($title);
    }

    /**
     * Build and print the sidebar.
     */
    public function build() {
        ?>
        <div data-role="panel" id="main-panel" data-position="left" data-display="reveal" data-theme="a">
            <?php
            // Make sure the user is logged in
            if(SessionManager::isLoggedIn()):
                $user = SessionManager::getLoggedInUser();
                $fullName = $user->getFullName();
                $mail = $user->getPrimaryMail()->getMail();
                $mailHash = md5(strtolower($mail));

                // Determine whether the user has a different active user
                $differentActiveUser = SessionManager::getLoggedInUser()->getId() !== SessionManager::getActiveUser()->getId();

                // Determine the user selector header text
                $userSelectorText = $mail;
                if($differentActiveUser)
                    $userSelectorText = SessionManager::getActiveUser()->getFullName();

                ?>
                <div id="header-account" data-role="header">
                    <img class="account-img" src="http://gravatar.com/avatar/<?=$mailHash; ?>" />
                    <h1><?=$fullName; ?></h1>
                </div>
                <div id="account-selector" data-role="collapsible" data-collapsed-icon="carat-d" data-expanded-icon="carat-u"<?=$differentActiveUser ? ' data-collapsed="false"' : ''; ?>>
                    <h4><?=$userSelectorText; ?></h4>
                    <ul data-role="listview">
                        <?php
                        // Show option to switch back to the default user
                        if($differentActiveUser)
                            echo '<li><a href="linkedusermanager.php?a=set&linked_user_id=null" class="ui-btn ui-icon-back ui-btn-icon-left">' . __('account', 'useMyOwnAccount') . '</a></li>';

                        // Get all linked users for the current user
                        $linkedUsers = LinkedUserManager::getLinkedUsersForOwner();

                        // Print all the linked users
                        foreach($linkedUsers as $linkedUser) {
                            // Validate the instance
                            if(!($linkedUser instanceof LinkedUser))
                                continue;

                            // Hide the current active user in the list
                            if($linkedUser->getUser()->getId() === SessionManager::getActiveUser()->getId())
                                continue;

                            // Print the linked user
                            echo '<li><a href="linkedusermanager.php?a=set&linked_user_id=' . $linkedUser->getId() . '" class="ui-btn ui-icon-user ui-btn-icon-left">' . __('account', 'useAs') . ' ' . $linkedUser->getUser()->getFullName() . '</a></li>';
                        }
                        ?>
                        <li><a href="linkedusermanager.php" class="ui-btn ui-icon-edit ui-btn-icon-left"><?=__('linkedUser', 'manageLinkedUsers'); ?></a></li>
                    </ul>
                </div>
                <?php
            endif;
            ?>

            <p>This is the sidebar menu panel.</p><br />
            <a href="#" data-rel="close" class="ui-btn ui-shadow ui-corner-all ui-btn-a ui-icon-delete ui-btn-icon-left ui-btn-inline">Close panel</a>
            <?php
            if(!StringUtils::equals(LanguageManager::getPreferredLanguage()->getTag(), 'nl-NL'))
                echo '<a id="language-button" href="language.php?lang_tag=nl-NL" class="ui-btn ui-shadow ui-corner-all ui-btn-a"><img src="style/image/flag/nl.png" /></a>';
            else
                echo '<a id="language-button" href="language.php?lang_tag=en-US" class="ui-btn ui-corner-all ui-shadow"><img src="style/image/flag/gb.png" /></a>';
            ?>
        </div>
        <?php
    }
}