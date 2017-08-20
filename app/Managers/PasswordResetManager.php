<?php

namespace App\Managers;

use App\Mail\Password\Request;
use App\Mail\Password\Reset;
use App\Models\Email;
use App\Models\PasswordReset;
use App\Models\User;
use App\Utils\EmailRecipient;
use App\Utils\TokenGenerator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class PasswordResetManager {

    /**
     * The number of seconds a password reset expires after.
     * @type int
     */
    const EXPIRE_AFTER = 24 * 60 * 60; // 24 hours

    /**
     * The length in characters of reset tokens.
     * @type int
     */
    const TOKEN_LENGTH = 32;

    /**
     * Create a new password reset token.
     * With this token the password of a user may be reset.
     *
     * This method does not handle reset email sending. This must be done with a different method.
     *
     * @param User $user The user to reset the password for.
     * @return PasswordReset The created password reset entry.
     *
     * @throws \Exception Throws if the given user is null.
     */
    public static function create(User $user) {
        // Make sure the email address is valid
        if($user == null)
            throw new \Exception('Given user is null');

        // Create the entry
        $reset = new PasswordReset();
        $reset->user_id = $user->id;
        $reset->token = self::generateUniqueToken();
        $reset->used = false;
        $reset->expire_at = Carbon::now()->addSecond(self::EXPIRE_AFTER);
        $reset->save();

        return $reset;
    }

    /**
     * Create a new password reset token and send a reset email to the user.
     * With this token the password of the user may be reset.
     *
     * @param Email $email The email address to send the reset token to.
     * @return PasswordReset The created password reset entry.
     *
     * @throws \Exception Throws if an invalid email address is given.
     */
    public static function createAndSend(Email $email) {
        // Create a reset entry for the user of this email address
        /** @noinspection PhpParamsInspection */
        $reset = self::create($email->user()->firstOrFail());

        // Create the mailable for the verification
        $recipient = new EmailRecipient($email);
        $mailable = new Request($recipient, $reset);

        // Send the mailable
        Mail::send($mailable);

        return $reset;
    }

    /**
     * Use a password reset token.
     *
     * @param string $token Password reset token.
     * @param string $password The new password.
     * @param boolean $invalidateSessions True to invalidate all existing sessions for the user, false to keep the sessions alive.
     * @return PasswordResetResult Password reset result.
     */
    public static function resetPassword($token, $password, $invalidateSessions) {
        // TODO: Make sure the given password is valid

        // The token must not be null
        if(empty($token))
            return new PasswordResetResult(PasswordResetResult::ERR_NO_TOKEN);

        // Get the password reset instance and make sure it's valid
        /** @noinspection PhpDynamicAsStaticMethodCallInspection */
        $reset = PasswordReset::where('token', '=', trim($token))->first();
        if($reset == null)
            return new PasswordResetResult(PasswordResetResult::ERR_INVALID_TOKEN);
        if($reset->isExpired())
            return new PasswordResetResult(PasswordResetResult::ERR_EXPIRED_TOKEN);
        if($reset->used)
            return new PasswordResetResult(PasswordResetResult::ERR_USED_TOKEN);

        // Get the associated user
        /** @var User $user */
        $user = $reset->user()->firstOrFail();

        // Change the password of the user
        $user->password = Hash::make($password);
        $user->save();

        // Mark the token as used
        $reset->used = true;
        $reset->save();

        // Invalidate sessions
        if($invalidateSessions) {
            $user->sessions()->get()->each(function($session) {
                // Return if the session has already expired
                if($session->isExpired())
                    return;

                // Invalidate the session now
                $session->expire_at = Carbon::now();
                $session->save();
            });
        }

        // Get the primary email address for the user
        $email = $user->getPrimaryEmail();

        // Send an additional reset token to allow the user to revert the password if the change was unwanted
        if($email != null) {
            // Create an additional reset token to allow the user to revert the password change
            $extraReset = self::create($user);

            try {
                // Create a mailable
                $recipient = new EmailRecipient($email, $user);
                $mailable = new Reset($recipient, $extraReset);

                // Send the mailable
                Mail::send($mailable);

            } catch (\Exception $e) {}
        }

        // Return the result
        return new PasswordResetResult(PasswordResetResult::OK);
    }

    /**
     * Generate a new and unique reset token.
     * This method blocks until a new unique token has been generated.
     *
     * @return string Unique token.
     */
    private static function generateUniqueToken() {
        // Keep generating tokens until we've an unique one
        do {
            // Generate a new token
            $token = TokenGenerator::generate(self::TOKEN_LENGTH, false);

            // Check whether the token exists
            /** @noinspection PhpDynamicAsStaticMethodCallInspection */
            $exists = PasswordReset::where('token', '=', $token)->first() != null;

        } while($exists);

        // Return the generated token
        return $token;
    }
}