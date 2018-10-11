<?php

namespace App\Perms;

/**
 * A nobody role.
 * Users that aren't signed in get this role.
 */
const NOBODY = -1;

/**
 * A normal user role.
 * The default role for signed in users.
 */
const USER = 0;

/**
 * The administrator role.
 * Includes permissions from `USER`.
 */
const ADMIN = 20;
