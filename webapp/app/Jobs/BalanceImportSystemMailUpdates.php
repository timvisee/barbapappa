<?php

namespace App\Jobs;

use App\Models\BalanceImportSystem;
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
    private $bar_id;
    private $invite_to_bar;
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
     * @param int|null $bar_id Related bar ID.
     * @param bool $invite_to_bar Whether to invite user to bar.
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
        ?int $bar_id,
        bool $invite_to_bar,
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
        $this->bar_id = $bar_id;
        $this->invite_to_bar = $invite_to_bar;
        $this->default_locale = $default_locale;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        $self = $this;
        DB::transaction(function() use($self) {
            // Fetch event or system alias IDs to message
            if($self->event_id != null) {
                // Get the event
                $event = BalanceImportEvent::find($self->event_id);
                if($event == null)
                    return;

                // Fetch aliases from approved changes
                $alias_ids = $event
                    ->changes()
                    ->approved()
                    ->get()
                    ->map(function($event) {
                        return $event->alias_id;
                    })
                    ->unique();
            } else {
                // Get the system
                $system = BalanceImportSystem::find($self->system_id);
                if($system == null)
                    return;

                // Fetch system alias IDs
                $alias_ids = $system->economy->balanceImportAliases()->pluck('id');
            }

            // Schedule job for each alias
            foreach($alias_ids as $alias_id) {
                // Dispatch background jobs to send update to alias user
                BalanceImportSystemMailUpdate::dispatch(
                    $self->system_id,
                    $alias_id,
                    $self->event_id,
                    $self->mail_unregistered_users,
                    $self->mail_not_joined_users,
                    $self->mail_joined_users,
                    $self->message,
                    $self->bar_id,
                    $self->invite_to_bar,
                    $self->default_locale,
                );
            }
        });
    }
}
