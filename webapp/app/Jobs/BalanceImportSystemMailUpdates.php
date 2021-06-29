<?php

namespace App\Jobs;

use App\Models\BalanceImportEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

/**
 * Mail balance import system users a balance update.
 */
class BalanceImportSystemMailUpdates implements ShouldQueue {

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Preferred queue constant.
     */
    const QUEUE = 'low';

    private $system_id;
    private $event_id;
    private $mail_unregistered_users;
    private $mail_not_joined_users;
    private $mail_joined_users;
    private $message;
    private $invite_to_bar_id;
    private $default_locale;

    /**
     * Create a new job instance.
     *
     * @param int $system_id System ID.
     * @param int|null $event_id Event ID.
     * @param bool $mail_unregistered_users Whether to mail unregistered users.
     * @param bool $mail_not_joined_users Whether to mail not-joined users.
     * @param bool $mail_joined_users Whether to mail joined users.
     * @param string|null $message Optional extra message.
     * @param int|null $invite_to_bar_id Bar ID to invite user to.
     * @param string|null $default_locale The default locale to use if user
     *      locale is unknown.
     */
    public function __construct(
        int $system_id,
        ?int $event_id,
        bool $mail_unregistered_users,
        bool $mail_not_joined_users,
        bool $mail_joined_users,
        $message,
        $invite_to_bar_id,
        $default_locale
    ) {
        // Set queue
        $this->onQueue(Self::QUEUE);

        $this->system_id = $system_id;
        $this->event_id = $event_id;
        $this->mail_unregistered_users = $mail_unregistered_users;
        $this->mail_not_joined_users = $mail_not_joined_users;
        $this->mail_joined_users = $mail_joined_users;
        $this->message = $message;
        $this->invite_to_bar_id = $invite_to_bar_id;
        $this->default_locale = $default_locale;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        // TODO: get list of aliases to send update for, if event is given, limit aliases to the ones part of the event

        // Get the event
        $event = BalanceImportEvent::find($this->event_id);
        if($event == null)
            return;

        $self = $this;
        DB::transaction(function() use($event, $self) {
            // Schedule job for each event change, determine whether to send,
            // then send
            $changes = $event->changes()->approved()->get();
            foreach($changes as $change) {
                // Dispatch background jobs to send update to change user
                BalanceImportEventMailUpdate::dispatch(
                    $change->id,
                    $self->mail_unregistered_users,
                    $self->mail_not_joined_users,
                    $self->mail_joined_users,
                    $self->message,
                    $self->invite_to_bar_id,
                    $self->default_locale,
                );
            }
        });
    }
}
