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
     * The subject of the message.
     * @var string
     */
    public $subject;

    /**
     * Constructor.
     *
     * @param EmailRecipient $recipient Email recipient.
     * @param string $subject Message subject.
     */
    public function __construct(EmailRecipient $recipient, $subject) {
        $this->recipient = $recipient;
        $this->subject = $subject;
    }

    /**
     * Build the message.
     *
     * @return Mailable
     */
    public function build() {
        return $this->to($this->recipient)->subject($this->subject);
    }
}
