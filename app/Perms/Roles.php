<?php

namespace App\Perms;

/**
 * The roles trait, for easy roles management.
 * This should be used in classes defining role constants for some scope.
 */
trait Roles {

    /**
     * Get a key/value list of user role IDs and their display names.
     *
     * Important: When using this trait, classes must implementg
     * `protected $roles = [];` with a key/value map of role IDs and display names.
     *
     * @return {array} Key/value list of user roles.
     */
    public static function roles() {
        throw new \Exception("the static roles() function is not implemented properly in the roles class it is used in");
    }

    /**
     * Get the scope name/identifier, used in the middleware configuration.
     *
     * @return {string} The scope name/identifier.
     */
    public static function scope() {
        throw new \Exception("the static scope() function is not implemented properly in the roles class it is used in");
    }

    /**
     * Check whether the given role ID is valid.
     * This checks whehter the given role ID is known.
     *
     * @param int $id The role ID.
     * @return bool True if valid, false if not.
     */
    public static function isValid($id) {
        $roles = Self::roles();
        return !empty($roles) && isset($roles[$id]);
    }

    /**
     * Get the display name for a role with the given ID.
     *
     * An exception is thrown if the role ID is unknown.
     *
     * @param int $id The role ID.
     * @return string The display name for the role.
     * @throws \Exception Throws if the given role ID is unknown.
     */
    public static function roleName($id) {
        // Get the roles map
        $roles = Self::roles();

        // Ensure the ID is valid and exists
        if(empty($roles) || $id === null || !isset($roles[$id]))
            throw new \Exception("failed to get role name, unknown role ID given");

        return $roles[$id];
    }

    /**
     * Get the role ID from the given role name.
     *
     * @param {string} $name The name of the role.
     * @return {int} The role ID.
     * @throws \Exception Throws if the given role name is unknown.
     */
    public static function fromName($name) {
        // The name must be valid
        if(empty($name) || strlen(trim($name)) == 0)
            throw new \Exception("failed to get role ID by name, empty name given");

        // Compare role names
        foreach(Self::roles() as $id => $role)
            if(!strcasecmp($name, $role))
                return $id;

        // Role not found, throw an exception
        throw new \Exception("failed to get role ID by name, unknown name: " . $name);
    }
}
