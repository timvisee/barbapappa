<?php

namespace App\Managers;

use App\EmailVerification;
use App\Mail\Email\Verify;
use App\Models\Email;
use App\Utils\TokenGenerator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class EmailVerificationManager {

    /**
     * The number of seconds a email verification expires after.
     * @type int
     */
    const EXPIRE_AFTER = 2 * 24 * 60 * 60;

    /**
     * The length in characters of verification tokens.
     * @type int
     */
    const TOKEN_LENGTH = 64;

    /**
     * Create a new email verification entry.
     * This entry has an unique token that may be used to verify the given email address before the token expires.
     *
     * The given email address must not have been verified already, or an exception is thrown.
     *
     * This method does not handle verification email sending. This must be done manually afterwards.
     *
     * @param Email $email The email address to verify.
     * @return EmailVerification The created email verification entry.
     *
     * @throws \Exception Throws if an invalid email address is given, or if the email address is already verified.
     */
    public static function createEntry(Email $email) {
        // Make sure the email address is valid
        if($email == null)
            throw new \Exception('Given email is null');

        // The email address must not be verified already
        if($email->isVerified())
            throw new \Exception('Unable to create email verification entry, email is already verified');

        // Create the entry
        $verification = new EmailVerification();
        $verification->email_id = $email->id;
        $verification->token = self::generateUniqueToken();
        $verification->expire_at = Carbon::now()->addSecond(self::EXPIRE_AFTER);
        $verification->save();

        // Return the verification entry
        return $verification;
    }

    public static function requestVerification(Email $email) {
        // Create a verification entry for this email
        $verification = self::createEntry($email);

        // Create the mailable for the verification
        $mailable = new Verify($verification, true);

        // Send the email
        Mail::to($email->email)->send($mailable);
    }

    public static function verifyToken($token) {
        // TODO: Validate the token
        // TODO: Set verification state of the address
        // TODO: Remove other verification tokens
        // TODO: Send a success message, with a link to mail preferences
    }

    /**
     * Generate a new and unique verification token.
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
            $exists = EmailVerification::where('token', '=', $token)->first() != null;

        } while($exists);

        // Return the generated token
        return $token;
    }
}