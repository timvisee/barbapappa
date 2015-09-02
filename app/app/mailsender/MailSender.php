<?php

namespace app\mailsender;

use app\config\Config;
use app\language\LanguageManager;
use app\user\User;
use carbon\core\io\filesystem\file\File;
use carbon\core\io\filesystem\file\FileReader;
use Exception;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

class MailSender {

    /** @var string|null Preferred language tag. */
    private static $prefLangTag = null;

    /**
     * Set the preferred language tag.
     *
     * @param string|null $langTag Preferred language tag.
     */
    public static function setPreferredLanguageTag($langTag) {
        static::$prefLangTag = $langTag;
    }

    /**
     * Send a mail.
     *
     * @param string|User $to The destination address, or user.
     * @param string $subject The subject.
     * @param string $message The message.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function sendMail($to, $subject, $message) {
        // TODO: Send HTML email

        // Parse the to
        if($to instanceof User)
            $to = $to->getFullName() . ' <' . $to->getPrimaryMail()->getMail() . '>';

        // Determine the sender
        $sender = APP_NAME . ' <' . Config::getValue('mail', 'sender', '') . '>';

        // Email headers for HTML
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-Type: text/html; charset=iso-8859-1' . "\r\n";

        // Additional headers
        $headers .= 'From: ' . $sender . "\r\n";
        $headers .= 'Reply-To: ' . $sender . "\r\n";
        //$headers .= 'X-Mailer: ' . APP_NAME . ' (PHP, ' . phpversion() . ')' . "\r\n";

        // Send the mail
        if(!mail($to, $subject, $message, $headers))
            throw new Exception('Failed to send email.');
    }

    /**
     * Get the message top.
     *
     * @return string Message top.
     */
    public static function getTop() {
        // Get the top file
        $file = new File(CARBON_SITE_ROOT . '/mail/mailTop.html');
        $fileReader = new FileReader($file);

        // Return the file contents
        return $fileReader->read();
    }

    /**
     * Get the message bottom.
     *
     * @return string Message bottom.
     */
    public static function getBottom() {
        // Get the top file
        $file = new File(CARBON_SITE_ROOT . '/mail/mailBottom.html');
        $fileReader = new FileReader($file);

        // Return the file contents
        return $fileReader->read();
    }

    /**
     * Get the message header.
     *
     * @param string $subject The mail subject.
     *
     * @return string Message header.
     */
    public static function getHeader($subject) {
        // Get the mail header image URL
        $headerImgUrl = Config::getValue('general', 'site_url', '') . 'style/image/logo/logo_header_mail.png';

        // Get the app name
        $appName = APP_NAME;

        // Return the header
        return <<<EOT
                <table class="row header"
                       style="margin-bottom: 10px; border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; position: relative; background: #BBB; padding: 0px;"
                       bgcolor="#999999">
                    <tr style="vertical-align: top; text-align: left; padding: 0;" align="left">
                        <td class="center" align="center"
                            style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: center; color: #222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0;"
                            valign="top">
                            <center style="width: 100%; min-width: 580px;">

                                <table class="container"
                                       style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: inherit; width: 580px; margin: 0 auto; padding: 0;">
                                    <tr style="vertical-align: top; text-align: left; padding: 0;" align="left">
                                        <td class="wrapper last"
                                            style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; position: relative; color: #222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 10px 0px 0px;"
                                            align="left" valign="top">

                                            <table class="twelve columns"
                                                   style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 580px; margin: 0 auto; padding: 0;">
                                                <tr style="vertical-align: top; text-align: left; padding: 0;"
                                                    align="left">
                                                    <td class="six sub-columns"
                                                        style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; min-width: 0px; width: 50%; color: #222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0px 10px 10px 0px;"
                                                        align="left" valign="top">
                                                        <img src="$headerImgUrl"
                                                             style="outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; width: auto; max-width: 100%; float: left; clear: both; display: block; height: 36px;"
                                                             align="left"
                                                             alt="$appName" /></td>
                                                    <td class="six sub-columns last"
                                                        style="text-align: right; vertical-align: middle; word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; min-width: 0px; width: 50%; color: #222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0px 0px 10px;"
                                                        align="right" valign="middle">
                                                        <span class="template-label"
                                                              style="color: #444; font-weight: bold; font-size: 14px;">$subject</span>
                                                    </td>
                                                    <td class="expander"
                                                        style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; visibility: hidden; width: 0px; color: #222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0;"
                                                        align="left" valign="top"></td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </center>
                        </td>
                    </tr>
                </table>
EOT;
    }

    /**
     * Get the message content top.
     *
     * @return string Message content top.
     */
    public static function getMessageTop() {
        return <<<EOT
                <table class="container"
                       style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: inherit; width: 580px; margin: 0 auto; padding: 0;">
                    <tr style="vertical-align: top; text-align: left; padding: 0;" align="left">
                        <td style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0;"
                            align="left" valign="top">
EOT;
    }

    /**
     * Get the message content header.
     *
     * @param string $hello Hello string.
     * @param string $lead Lead string.
     *
     * @return string The message content header.
     */
    public static function getMessageHeader($hello, $lead) {
        return <<<EOT
                <table class="row"
                       style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; position: relative; display: block; padding: 0px;">
                    <tr style="vertical-align: top; text-align: left; padding: 0;" align="left">
                        <td class="wrapper last"
                            style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; position: relative; color: #222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 10px 0px 0px;"
                            align="left" valign="top">

                            <table class="twelve columns"
                                   style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 580px; margin: 0 auto; padding: 0;">
                                <tr style="vertical-align: top; text-align: left; padding: 0;" align="left">
                                    <td style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0;"
                                        align="left" valign="top">
                                        <h1 style="color: #222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 1.3; word-break: normal; font-size: 30px; margin: 0 0 4px 0; padding: 0;"
                                            align="left">$hello,</h1>

                                        <p class="lead"
                                           style="color: #222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 21px; font-size: 18px; margin: 0 0 10px; padding: 0;"
                                           align="left">$lead</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
EOT;
    }

    /**
     * Get a message content text block.
     *
     * @param string $text The text as a string.
     *
     * @return string The message content text block.
     */
    public static function getMessageText($text) {
        return <<<EOT
                <table class="row"
                                   style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; position: relative; display: block; padding: 0px;">
                                <tr style="vertical-align: top; text-align: left; padding: 0;" align="left">
                                    <td class="wrapper last"
                                        style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; position: relative; color: #222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 10px 0px 0px;"
                                        align="left" valign="top">

                                        <table class="twelve columns"
                                               style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 580px; margin: 0 auto; padding: 0;">
                                            <tr style="vertical-align: top; text-align: left; padding: 0;" align="left">
                                                <td style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0px 0px 10px;"
                                                    align="left" valign="top">
                                                    <p style="color: #222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 19px; font-size: 14px; margin: 0; padding: 0 0 10px;"
                                                       align="left">$text</p>
                                                </td>
                                                <td class="expander"
                                                    style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; visibility: hidden; width: 0px; color: #222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0;"
                                                    align="left" valign="top"></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
EOT;
    }

    /**
     * Get a message content notice block.
     *
     * @param string $notice The notice as a string.
     *
     * @return string The message content notice block.
     */
    public static function getMessageNotice($notice) {
        return <<<EOT
                            <table class="row callout"
                                   style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; position: relative; display: block; padding: 0px;">
                                <tr style="vertical-align: top; text-align: left; padding: 0;" align="left">
                                    <td class="wrapper last"
                                        style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; position: relative; color: #222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0 0 10px;"
                                        align="left" valign="top">

                                        <table class="twelve columns"
                                               style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 580px; margin: 0 auto; padding: 0;">
                                            <tr style="vertical-align: top; text-align: left; padding: 0;" align="left">
                                                <td class="panel"
                                                    style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; background: #ECF8FF; margin: 0; padding: 10px 10px 0 10px; border: 1px solid #B9E5FF;"
                                                    align="left" bgcolor="#ECF8FF" valign="top">
                                                    <p style="color: #222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 19px; font-size: 14px; margin: 0 0 10px; padding: 0;"
                                                       align="left">$notice</p>
                                                </td>
                                                <td class="expander"
                                                    style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; visibility: hidden; width: 0px; color: #222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0;"
                                                    align="left" valign="top"></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
EOT;
    }

    /**
     * Get a message content link.
     *
     * @param string $url The URL.
     * @param string $text The link text.
     *
     * @return string The link.
     */
    public static function getMessageTextLink($url, $text) {
        return '<a href="' . $url . '" style="color: #2BA6CB; text-decoration: none;">' . $text . '</a>';
    }

    /**
     * Get the message content bottom.
     *
     * @return string Message content bottom.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function getMessageBottom() {
        // Get the front page and my account
        $baseLink = Config::getValue('general', 'site_url', '');
        $linkFrontPage = $baseLink;
        $textFrontPage = LanguageManager::getValue('general', 'frontPage', '', static::$prefLangTag);
        $linkMyAccount = $baseLink;
        $textMyAccount = LanguageManager::getValue('general', 'myAccount', '', static::$prefLangTag);

        $balance = LanguageManager::getValue('general', 'balance', '', static::$prefLangTag);
        $service = LanguageManager::getValue('general', 'service', '', static::$prefLangTag);
        $featureNotYetAvailable = LanguageManager::getValue('general', 'featureNotYetAvailable', '', static::$prefLangTag);

        return <<<EOT
            <table class="row footer"
                   style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; position: relative; display: block; padding: 0px;">
                <tr style="vertical-align: top; text-align: left; padding: 0;" align="left">
                    <td class="wrapper"
                        style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; position: relative; color: #222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; background: #EBEBEB; margin: 0; padding: 10px 20px 0px 0px;"
                        align="left" bgcolor="#ebebeb" valign="top">

                        <table class="six columns"
                               style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 280px; margin: 0 auto; padding: 0;">
                            <tr style="vertical-align: top; text-align: left; padding: 0;" align="left">
                                <td class="left-text-pad"
                                    style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0px 0px 10px 10px;"
                                    align="left" valign="top">

                                    <h5 style="color: #222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 1.3; word-break: normal; font-size: 20px; margin: 0; padding: 0 0 10px;"
                                        align="left">$balance</h5>

                                    <p style="color: #222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 19px; font-size: 14px; margin: 0 0 10px; padding: 0;"
                                       align="left"><i>$featureNotYetAvailable</i></p>
                                    </p>
                                </td>
                                <td class="expander"
                                    style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; visibility: hidden; width: 0px; color: #222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0;"
                                    align="left" valign="top"></td>
                            </tr>
                        </table>
                    </td>
                    <td class="wrapper last"
                        style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; position: relative; color: #222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; background: #EBEBEB; margin: 0; padding: 10px 0px 0px;"
                        align="left" bgcolor="#ebebeb" valign="top">

                        <table class="six columns"
                               style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 280px; margin: 0 auto; padding: 0;">
                            <tr style="vertical-align: top; text-align: left; padding: 0;" align="left">
                                <td class="last right-text-pad"
                                    style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0px 0px 10px;"
                                    align="left" valign="top">
                                    <h5 style="color: #222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; text-align: left; line-height: 1.3; word-break: normal; font-size: 20px; margin: 0; padding: 0 0 10px;"
                                        align="left">$service</h5>

                                    <table class="tiny-button"
                                           style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; overflow: hidden; padding: 0; margin-bottom: 6px;">
                                        <tr style="vertical-align: top; text-align: left; padding: 0;"
                                            align="left">
                                            <td style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: center; color: #FFF; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; display: block; width: auto !important; background: #3B5998; margin: 0; padding: 5px 0 4px; border: 1px solid #2D4473;"
                                                align="center" bgcolor="#3b5998" valign="top">
                                                <a href="$linkFrontPage"
                                                   style="color: #FFF; text-decoration: none; font-weight: normal; font-family: Helvetica, Arial, sans-serif; font-size: 12px;">$textFrontPage</a>
                                            </td>
                                        </tr>
                                    </table>
                                    <table class="tiny-button"
                                           style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; overflow: hidden; padding: 0;">
                                        <tr style="vertical-align: top; text-align: left; padding: 0;"
                                            align="left">
                                            <td style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: center; color: #FFF; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; display: block; width: auto !important; background: #3B5998; margin: 0; padding: 5px 0 4px; border: 1px solid #2D4473;"
                                                align="center" bgcolor="#3b5998" valign="top">
                                                <a href="$linkMyAccount"
                                                   style="color: #FFF; text-decoration: none; font-weight: normal; font-family: Helvetica, Arial, sans-serif; font-size: 12px;">$textMyAccount</a>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td class="expander"
                                    style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; visibility: hidden; width: 0px; color: #222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0;"
                                    align="left" valign="top"></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
EOT;
    }

    /**
     * Get the message footer.
     *
     * @return string Message footer as a string.
     *
     * @throws Exception Throws if an error occurred.
     */
    public static function getFooter() {
        // Get some links and translation
        $baseLink = Config::getValue('general', 'site_url', '');
        $linkTerms = $baseLink;
        $textTerms = LanguageManager::getValue('general', 'terms', '', static::$prefLangTag);
        $linkPrivacy = $baseLink;
        $textPrivacy = LanguageManager::getValue('general', 'privacy', '', static::$prefLangTag);
        $linkMailPreferences = $baseLink . 'mailmanager.php';
        $textMailPreferences = LanguageManager::getValue('mail', 'mailPreferences', '', static::$prefLangTag);

        return <<<EOT
                            <table class="row"
                                   style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 100%; position: relative; display: block; padding: 0px;">
                                <tr style="vertical-align: top; text-align: left; padding: 0;" align="left">
                                    <td class="wrapper last"
                                        style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; position: relative; color: #222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 10px 0px 0px;"
                                        align="left" valign="top">

                                        <table class="twelve columns"
                                               style="border-spacing: 0; border-collapse: collapse; vertical-align: top; text-align: left; width: 580px; margin: 0 auto; padding: 0;">
                                            <tr style="vertical-align: top; text-align: left; padding: 0;" align="left">
                                                <td align="center"
                                                    style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; color: #222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0px 0px 10px;"
                                                    valign="top">
                                                    <center style="width: 100%; min-width: 580px;">
                                                        <p style="text-align: center; color: #222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0 0 10px; padding: 0;"
                                                           align="center"><a href="$linkTerms"
                                                                             style="color: #2BA6CB; text-decoration: none;">$textTerms</a>
                                                           &nbsp;&nbsp;&middot;&nbsp;&nbsp;<a href="$linkPrivacy"
                                                                 style="color: #2BA6CB; text-decoration: none;">$textPrivacy</a>
                                                           &nbsp;&nbsp;&middot;&nbsp;&nbsp;<a href="$linkMailPreferences"
                                                                 style="color: #2BA6CB; text-decoration: none;">$textMailPreferences</a>
                                                        </p>
                                                    </center>
                                                </td>
                                                <td class="expander"
                                                    style="word-break: break-word; -webkit-hyphens: auto; -moz-hyphens: auto; hyphens: auto; border-collapse: collapse !important; vertical-align: top; text-align: left; visibility: hidden; width: 0px; color: #222; font-family: 'Helvetica', 'Arial', sans-serif; font-weight: normal; line-height: 19px; font-size: 14px; margin: 0; padding: 0;"
                                                    align="left" valign="top"></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
EOT;
    }
}
