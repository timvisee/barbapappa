<?php

namespace App\Mail;

use App\Utils\EmailRecipient;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

abstract class PersonalizedEmail extends Mailable {

    use Queueable, SerializesModels;

    /**
     * The recipient that will receive the email.
     * @var EmailRecipient
     */
    public $recipient;

    /**
     * The subject key of the message.
     * @var string|null
     */
    public $subject = null;

    /**
     * The subject language key.
     * @var string|null
     */
    private $subjectKey = null;

    /**
     * The subject language key replacements.
     * @var array
     */
    private $subjectKeyReplace = [];

    /**
     * Constructor.
     *
     * @param EmailRecipient $recipient Email recipient.
     * @param string $subjectKey Message subject language key.
     * @param array $replace Fields to replace in the subject language value.
     */
    public function __construct(EmailRecipient $recipient, $subjectKey, array $replace = []) {
        $this->recipient = $recipient;
        $this->subjectKey = $subjectKey;
        $this->subjectKeyReplace = $replace;
    }

    /**
     * Build the message.
     *
     * @return Mailable
     */
    public function build() {
        // Define the subject
        if(empty($this->subject) && !empty($this->subjectKey))
            $this->subject = __($this->subjectKey, $this->subjectKeyReplace);

        // Build the mailable
        return $this
            ->to($this->recipient)
            ->subject($this->subject);
    }
}
