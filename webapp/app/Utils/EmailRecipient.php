<?php

namespace App\Utils;

use App\Models\Email;
use App\Models\User;

/**
 * Class Recipient.
 *
 * This is a helper class for configuring an email recipient.
 *
 * @property-read string|null email The email address of the recipient.
 * @property-read string|null name The name of the recipient.
 *
 * @package App\Utils
 */
class EmailRecipient {

    /**
     * The email of the recipient as model or as string.
     * @var Email|string|null
     */
    private $to;

    /**
     * The user as recipient.
     * This may be an user model or the name of the recipient as a string.
     * @var User|string|null
     */
    public $user;

    /**
     * Recipient constructor.
     *
     * If no email address or name is specified, it's automatically determined based on the other parameters when possible.
     *
     * @param string|User|Email|null $to The email address as a string. If a User or Email instance is given, the address is fetched automatically.
     * @param string|User|Email|null $user The name as a string. If a User or Email instance is given, the name is fetched automatically.
     */
    public function __construct($to, $user = null) {
        // Set the email address
        if(isset($to))
            $this->setEmail($to);
        else if($user instanceof User || $user instanceof Email)
            $this->setEmail($user);

        // Set the name
        if(isset($user))
            $this->setUser($user);
        else if($to instanceof Email || $to instanceof User)
            $this->setUser($to);
    }

    /**
     * Property getter, also provides magic properties.
     *
     * @param string $name Property name.
     * @return mixed Property value.
     */
    public function __get($name) {
        switch($name) {
            case 'email':
                return $this->getEmailAddress();
            case 'name':
                return $this->getName();
            default:
                return null;
        }
    }

    /**
     * Property checker, also checks for magic properties.
     * @param string $name Property name.
     * @return boolean True if the property exists, false if not.
     */
    public function __isset($name) {
        switch($name) {
            case 'email':
                return $this->getEmailAddress() != null;
            case 'name':
                return $this->getName() != null;
            default:
                return false;
        }
    }

    /**
     * Get the email address if set.
     *
     * @return string|null Email address or null.
     */
    public function getEmailAddress() {
        if($this->to instanceof Email)
            return $this->to->email;
        if(is_string($this->to))
            return $this->to;
        return null;
    }

    /**
     * Get the email model if set.
     *
     * @return Email|null Email model or null.
     */
    public function getEmail() {
        return ($this->to instanceof Email) ? $this->to : null;
    }

    /**
     * Set the email address of the recipient.
     *
     * @param string|User|Email|null $email The email address as a string. If a User or Email instance is given, the address is fetched automatically.
     */
    public function setEmail($email) {
        // Parse User models
        if($email instanceof User)
            $email = $email->getPrimaryEmail();

        // Set the email address
        $this->to = $email;
    }

    /**
     * Get the first name of the user when known.
     * If only the full name is known, the full name is returned.
     * If the name is not set, null is returned.
     *
     * @return string The name or null.
     */
    public function getFirstName() {
        if($this->user instanceof User)
            return $this->user->first_name;
        return $this->getName();
    }

    /**
     * Get the full name of the user.
     * If the name is not set, null is returned.
     *
     * @return string The name or null.
     */
    public function getName() {
        if($this->user instanceof User)
            return $this->user->name;
        if(is_string($this->user))
            return $this->user;
        return null;
    }

    /**
     * Return the user model if set.
     *
     * @return User|null The User model or null.
     */
    public function getUser() {
        return ($this->user instanceof User) ? $this->user : null;
    }

    /**
     * Set the user as recipient.
     *
     * @param string|User|Email|null $user A User model as recipient, or the name of the user as a string. If an Email model instance is given, the user is fetched automatically.
     */
    public function setUser($user) {
        // Parse Email models
        if($user instanceof Email)
            $user = $user->user()->firstOrFail();

        // Set the user
        $this->user = $user;
    }
}
