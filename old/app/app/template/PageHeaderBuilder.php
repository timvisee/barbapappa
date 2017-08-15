<?php

namespace app\template;

use app\language\LanguageManager;
use app\session\SessionManager;
use app\user\User;
use app\util\ColorUtils;
use carbon\core\util\StringUtils;

// Prevent direct requests to this file due to security reasons
defined('APP_INIT') or die('Access denied!');

class PageHeaderBuilder {

    /** The default header title. */
    const HEADER_TITLE_DEFAULT = '<img src="style/image/logo/logo_header_big.png" style="height: 21px;" />';

    /** @var string|null The title of the header, or null to use the default title. */
    private $title = null;
    /** @var string|null The URL of the back button, null otherwise. */
    private $backButtonUrl = null;
    /** @var bool True to show the menu button, false otherwise. */
    private $menuBtn = false;
    /** @var bool True to show the close button, false otherwise. */
    private $closeBtn = false;
    /** @var bool True if the header is fixed, false if not. */
    private $fixed = false;
    /** @var bool True to show the active user if set. */
    private $showActiveUser = true;
    /** @var string|null An optional header prefix, null to ignore this option. */
    private $prefix = null;
    /** @var string|null An optional header suffix, null to ignore this option. */
    private $suffix = null;

    /**
     * Constructor.
     *
     * @param string|null $title [optional] The title, or null to use the default title.
     */
    public function __construct($title = null) {
        $this->setTitle($title);
    }

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
     * Get the title if set.
     *
     * @return string|null The title, or null if it hasn't been set.
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Check whether a title has been set.
     *
     * @return bool True if a title has been set, false otherwise.
     */
    public function hasTitle() {
        return $this->getTitle() != null;
    }

    /**
     * Set the title of the header.
     *
     * @param string|null $title [optional] The title as a string, or null to use the default header title.
     *
     * @return self Return the current instance to allow method chaining.
     */
    public function setTitle($title = null) {
        $this->title = $title;
        return $this;
    }

    /**
     * Get the back button URL if a back button has been set.
     *
     * @return string|null The back button URL, or null if no button has been set.
     */
    public function getBackButton() {
        return $this->backButtonUrl;
    }

    /**
     * Check whether this header has a back button.
     *
     * @return bool True if this header has a back button.
     */
    public function hasBackButton() {
        return $this->getBackButton() != null;
    }

    /**
     * Set whether to add a back button.
     *
     * @param string|null $backButtonUrl The URL of the back button if a back button needs to be added, null otherwise.
     *
     * @return self Return the current instance to allow method chaining.
     */
    public function setBackButton($backButtonUrl) {
        $this->backButtonUrl = $backButtonUrl;
        return $this;
    }

    /**
     * Check whether the menu button should be shown.
     *
     * @return bool True if the menu button should be shown, false if not.
     */
    public function hasMenuButton() {
        return $this->menuBtn;
    }

    /**
     * Set whether the menu button should be shown.
     *
     * @param bool $visible True to show the menu button, false otherwise.
     *
     * @return self Return the current instance to allow method chaining.
     */
    public function setMenuButton($visible) {
        $this->menuBtn = $visible;
        return $this;
    }

    /**
     * Check whether the close button should be shown.
     *
     * @return bool True if the close button should be shown, false if not.
     */
    public function hasCloseButton() {
        return $this->closeBtn;
    }

    /**
     * Set whether the close button should be shown.
     *
     * @param bool $visible True to show the close button, false otherwise.
     *
     * @return self Return the current instance to allow method chaining.
     */
    public function setCloseButton($visible) {
        $this->closeBtn = $visible;
        return $this;
    }

    /**
     * Check whether the header is fixed.
     *
     * @return bool True if the header is fixed, false if not.
     */
    public function isFixed() {
        // TODO: Properly render fixed headers, before reverting the code bellow
        return false; // $this->fixed
    }

    /**
     * Set whether the header is fixed
     *
     * @param bool $fixed True if the header is fixed, false if not.
     *
     * @return self Return the current instance to allow method chaining.
     */
    public function setFixed($fixed) {
        $this->fixed = $fixed;
        return $this;
    }

    /**
     * Check whether to show the active user if set.
     *
     * @return bool True if the active user should be shown, false otherwise.
     */
    public function getShowActiveUser() {
        return $this->showActiveUser;
    }

    /**
     * Set whether to show the active user if set.
     *
     * @param bool $showActiveUser True to show the active user, false if not.
     *
     * @return self Return the current instance to allow method chaining.
     */
    public function setShowActiveUser($showActiveUser) {
        $this->showActiveUser = $showActiveUser;
        return $this;
    }

    /**
     * Get the current prefix.
     *
     * @return string|null The prefix, or null if not prefix has been set.
     */
    public function getPrefix() {
        return $this->prefix;
    }

    /**
     * Set the current prefix.
     *
     * @param string|null $prefix [optional] The prefix, or null to ignore this option.
     *
     * @return self The current instance to allow method chaining.
     */
    public function setPrefix($prefix = null) {
        $this->prefix = $prefix;
        return $this;
    }

    /**
     * Check whether this header has a prefix set.
     *
     * @return bool True if a prefix has been set, false otherwise.
     */
    public function hasPrefix() {
        return $this->prefix != null;
    }

    /**
     * Get the current suffix.
     *
     * @return string|null The suffix, or null if not suffix has been set.
     */
    public function getSuffix() {
        return $this->suffix;
    }

    /**
     * Set the current suffix.
     *
     * @param string|null $suffix [optional] The suffix, or null to ignore this option.
     *
     * @return self The current instance to allow method chaining.
     */
    public function setSuffix($suffix = null) {
        $this->suffix = $suffix;
        return $this;
    }

    /**
     * Check whether this header has a suffix set.
     *
     * @return bool True if a suffix has been set, false otherwise.
     */
    public function hasSuffix() {
        return $this->suffix != null;
    }

    /**
     * Build and print the header.
     */
    public function build() {
        // TODO: Remove coloring from OVRally

        // Define the header background
        $teamColor = null;
        $headerDivStyle = '';
        /*if(SessionManager::isLoggedIn() && SessionManager::getLoggedInTeam()->hasColorHex()) {
            $teamColor = SessionManager::getLoggedInTeam()->getColorHex();
            $headerDivStyle .= 'background: #' . $teamColor . ';';
        }*/

        // Determine the header text color
        $headerColor = '333333';
        $headerColorShadow = 'EEEEEE';
        $headerShadow = 'CCCCCC';
        if($teamColor != null)
            $headerShadow = ColorUtils::adjustHexBrightness($teamColor, -35);
        if($teamColor != null && ColorUtils::getHexBrightness($teamColor) < 150) {
            $headerColor = 'F8F8F8';
            $headerColorShadow = '333333';
        }

        // Remove the border from the top of the header and set the header shadow/border
        $headerDivStyle .= 'border-top: none;';
        $headerDivStyle .= 'border-bottom: 1px solid #' . $headerShadow . ';';

        // Print div opening tag, of the header
        echo '<div id="header" data-role="header" style="' . $headerDivStyle . '"' . ($this->isFixed() ? ' data-position="fixed"' : '') . '>';

        // Show a back button if set
        if($this->hasBackButton())
            echo '<a href="' . $this->getBackButton() . '" data-rel="back" class="ui-btn ui-corner-all ui-icon-back ui-btn-icon-left" data-direction="reverse">' . __('navigation', 'back') . '</a>';

        // Show the menu button if set
        if($this->hasMenuButton()) {
            echo '<a id="menu-button" href="#main-panel" class="ui-btn ui-corner-all ui-btn-icon-notext ui-icon-bars">' . __('general', 'menu') . '</a>';

            // TODO: This is temporary
            echo '<a id="start-tutorial" href="#" class="ui-btn ui-corner-all ui-btn-icon-notext ui-icon-info">Start test tutorial</a>';
        }

        // Show the menu button if set
        if($this->hasCloseButton())
            echo '<a href="" class="ui-btn ui-corner-all ui-btn-icon-notext ui-icon-delete" data-rel="back">' . __('general', 'menu') . '</a>';

        // Print the prefix
        if($this->hasPrefix())
            echo $this->getPrefix();

        // Determine and print the title
        $headerTitle = static::HEADER_TITLE_DEFAULT;
        if($this->hasTitle())
            $headerTitle = $this->getTitle();
        echo '<h1 style="color: #' . $headerColor . '; text-shadow: 0 1px 0 #' . $headerColorShadow . ';"><a href="index.php" data-ajax="false" title="Refresh app">' . $headerTitle . '</a></h1>';

        // Print the suffix
        if($this->hasSuffix())
            echo $this->getSuffix();

        // Print div closing tag
        echo '</div>';

        // Print the active user if set
        if($this->getShowActiveUser() && SessionManager::isLoggedIn() && SessionManager::getLoggedInUser()->getId() != SessionManager::getActiveUser()->getId()) {
            // Ge the user
            $activeUser = SessionManager::getActiveUser();

            echo '<div data-role="header" style="' . $headerDivStyle . '">';
            echo '<p class="header-message"><a href="linkedusermanager.php">' .  __('account', 'usingAccountAs') . ' ' . $activeUser->getFullName() . '</a></p>';
            echo '</div>';
        }
    }
}