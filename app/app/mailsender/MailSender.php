<?php

/**
 * MailSend.php
 *
 * Created by PhpStorm.
 *
 * Author: Tim
 * Date: 1-9-2015
 */

namespace app\mailsender;

use app\config\Config;
use app\user\User;
use Exception;

// Prevent direct requests to this set_file due to security reasons
defined('CARBON_CORE_INIT') or die('Access denied!');

class MailSender {

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
        $sender = Config::getValue('mail', 'sender', '');

        // Create the headers
        $headers = 'From: ' . $sender . "\r\n" .
            'Reply-To: ' . $sender . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        // Send the mail
        if(!mail($to, $subject, $message, $headers))
            throw new Exception('Failed to send email.');
    }
}
