<?php

namespace App\Models;

use App\Models\Email;
use App\Utils\EmailRecipient;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Balance import alias.
 *
 * Represents an alias or user by an email address for an imported balance
 * change.
 *
 * @property int id
 * @property int economy_id
 * @property-read Economy economy
 * @property int|null user_id
 * @property-read User|null user
 * @property string|null name
 * @property string email
 * @property string name
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class BalanceImportAlias extends Model {

    protected $table = 'balance_import_alias';

    protected $fillable = [
        'user_id',
        'name',
        'email',
    ];

    /**
     * A user that has not registered yet.
     */
    public const USER_STATE_UNREGISTERED = 1;

    /**
     * A user that has not joined the selected bar yet.
     */
    public const USER_STATE_NOT_JOINED = 2;

    /**
     * A user that has joined the selected bar.
     */
    public const USER_STATE_JOINED = 3;


    public static function boot() {
        parent::boot();

        // Cascade delete to economy members if it has no linked user
        static::deleting(function($model) {
            // TODO: do this through economy member class
            foreach($model->economyMembers()->get() as $member) {
                if($member->user_id != null)
                    $member->aliases()->detach();
                else
                    $member->delete();
            }
        });
    }

    /**
     * A scope to limit to aliases that have any approved balance import change.
     */
    public function scopeHasApproved($query) {
        $query->whereExists(function($query) {
            $query->selectRaw('1')
                ->from('balance_import_change')
                ->whereRaw('balance_import_alias.id = balance_import_change.alias_id')
                ->whereNotNull('approved_at')
                ->where('approved_at', '<=', now());
        });
    }

    /**
     * Get a relation to the economy this import is part of.
     *
     * @return Relation to the economy.
     */
    public function economy() {
        return $this->belongsTo(Economy::class);
    }

    /**
     * Get a relation to the linked economy members.
     *
     * TODO: shouldn't this only have one economy member?
     *
     * @return Relation to the economy members.
     */
    public function economyMembers() {
        return $this->belongsToMany(
            EconomyMember::class,
            'economy_member_alias',
            'alias_id',
            'member_id'
        )->where('economy_id', $this->economy_id);
    }

    /**
     * Get a relation to the user this alias is linked to.
     *
     * @return Relation to the user.
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
     * Get a relation to user email addresses.
     *
     * This may return multiple email addresses if multiple users entered the
     * same address.
     *
     * @return Relation to user email addresses.
     */
    public function userEmails() {
        return $this->belongsTo(Email::class, 'email', 'email');
    }

    /**
     * Get a relation to all balance import changes.
     *
     * @return Relation to balance import changes.
     */
    public function changes() {
        return $this->hasMany(BalanceImportChange::class, 'alias_id');
    }

    /**
     * Get or create a specific user alias in the given economy for the given
     * name and email address.
     *
     * If the name of the user changes over time, it is automatically updated.
     * If the alias already exists, the name is optional and may be null.
     *
     * If the alias does not exist and the name is null, no alias will be
     * created and null is returned.
     *
     * @param Economy $economy The economy the alias is in.
     * @param string|null $name The name of the user.
     * @param string $email The email address of the alias.
     * @return BalanceImportAlias|null The balance import alias.
     */
    public static function getOrCreate(Economy $economy, $name, $email) {
        $email = strtolower($email);

        // Find the alias by this email address, update it and return
        $alias = $economy
            ->balanceImportAliases()
            ->where('email', $email)
            ->first();
        if($alias != null) {
            // Update the name if it has changed
            if(!empty($name) && $alias->name != $name) {
                $alias->name = $name;
                $alias->save();
            }

            return $alias;
        }

        // The name must be set, attempt to get through verified user email
        $user_email = Email::verified()->where('email', $email)->first();
        $user = $user_email != null ? $user_email->user : null;
        if(empty($name)) {
            if($user_email == null)
                return null;
            $name = $user->name;
        }

        // Create a new alias for this email address
        return $economy->balanceImportAliases()->create([
            'user_id' => $user != null ? $user->id : null,
            'name' => $name,
            'email' => $email,
        ]);
    }

    /**
     * Create an economy member for this alias if it doesn't exist.
     *
     * Should be called as soon as any of the balance import changes is
     * accepted.
     * This does nothing if there already is an economy member.
     */
    public function createEconomyMember() {
        $economy = $this->economy;

        // If there's are registered user, he must join economy, always link alias
        if($this->user_id != null) {
            $user = $this->user;

            if(!$economy->isJoined($user))
                $economy->join($user);

            $economy_member = $economy->members()->user($user)->firstOrFail();
            $economy_member->aliases()->syncWithoutDetaching([$this->id]);
        } else {
            $has_member = $economy
                ->members()
                ->alias($this)
                ->limit(1)
                ->count() > 0;
            if(!$has_member)
                $economy->members()
                    ->create()
                    ->aliases()
                    ->attach($this->id);
        }
    }

    /**
     * Delete all economy member entries for this alias.
     *
     * This will leave member entries also having a user, but it sets the
     * reference to this alias to null.
     */
    public function deleteEconomyMember() {
        $economy = $this->economy;

        // Delete econony members only having this alias
        $alias = $this;
        $economy
            ->members()
            ->alias($this)
            ->whereNotExists(function($query) use($alias) {
                $query->selectRaw('1')
                    ->from('economy_member_alias')
                    ->whereRaw('economy_member.id = economy_member_alias.member_id')
                    ->where('alias_id', '!=', $alias->id);
            })
            ->whereNull('user_id')
            ->delete();

        // Detach from economy members having user and/or other aliases
        $members = $economy
            ->members()
            ->alias($this)
            ->where(function($query) use($alias) {
                $query->whereNotNull('user_id')
                    ->orWhereExists(function($query) use($alias) {
                        $query->selectRaw('1')
                            ->from('economy_member_alias')
                            ->whereRaw('economy_member.id = economy_member_alias.member_id')
                            ->where('alias_id', '!=', $alias->id);
                    });
            })
            ->get();
        foreach($members as $member)
            $member->aliases()->detach($this->id);
    }

    /**
     * Refresh the economy member entries for all aliases of a given user.
     *
     * This will ensure a single economy member is available in each economy for
     * the given user and alias ID, and merges any duplicates.
     * Missing economy members are created.
     *
     * It is highly recommended to run this for cleaning up after:
     * - verifying a user's email address
     * - adding a new economy member entry
     * - creating a new alias
     *
     * @param User $user The user to refresh all economy members for.
     * @throws \Exception Throws if not in a database transaction.
     */
    public static function refreshEconomyMembersForUser(User $user) {
        // We must be in a database transaction
        assert_transaction();

        // Get all aliases for the user
        $aliases = $user->balanceImportAliases()->hasApproved()->get();
        foreach($aliases as $alias) {
            // Remove this alias from economy members being a different user
            $otherMembers = EconomyMember::where('user_id', '!=', $user->id)
                ->whereNotNull('user_id')
                ->alias($alias)
                ->get();
            foreach($otherMembers as $otherMember)
                $otherMember->aliases()->detach($alias->id);

            // Get all member entries for current user and alias and merge
            $members = EconomyMember::where('economy_id', $alias->economy_id)
                ->where(function($query) use($alias, $user) {
                    $query->where('user_id', $user->id)
                        ->orWhere(function($query) use($alias, $user) {
                            $query->alias($alias)
                                ->whereNull('user_id')
                                ->orWhere('user_id', $user->id);
                        });
                })
                ->orderBy('economy_member.created_at', 'ASC')
                ->get();
            if($members->isEmpty())
                continue;

            // Merge all members, make sure alias ID is set
            $member = EconomyMember::mergeAll($members);
            $member->aliases()->syncWithoutDetaching([$alias->id]);
        }
    }

    /**
     * Commit all uncommitted balance import changes for the given user, if
     * possible.
     *
     * @param User $user The user to commit the changes for.
     * @throws \Exception Throws if not in a database transaction.
     */
    public static function tryCommitForUser(User $user) {
        Self::tryCommitForAliases($user->balanceImportAliases()->pluck('id'));
    }

    /**
     * Commit all uncommitted balance import changes for the given aliases, if
     * possible.
     *
     * @param array $alias_ids List of balance import alias IDs.
     * @throws \Exception Throws if not in a database transaction.
     */
    public static function tryCommitForAliases($alias_ids) {
        // We must be in a database transaction
        assert_transaction();

        // Get all uncommitted changes, and commit them
        $changes = BalanceImportChange::whereIn('alias_id', $alias_ids)
            ->orderBy('balance_import_change.created_at', 'ASC')
            ->whereNotNull('approved_at')
            ->whereNull('committed_at')
            ->whereNull('mutation_id')
            ->get();
        foreach($changes as $change)
            if($change->shouldCommit())
                $change->commit();
    }

    /**
     * Get a list of email recipients for this alias.
     *
     * @return [EmailRecipient]
     */
    public function toEmailRecipients() {
        // Attemp to use user recipients for this alias address if available
        if(($user = $this->user) != null) {
            try {
                $recipients = $user->buildEmailRecipients($this->email);
                if(!$recipients->isEmpty())
                    return $recipients;
            } catch(\Exception $e) {}
        }

        // Default to raw alias address
        return collect([new EmailRecipient($this->email, $this->name)]);
    }

    /**
     * Determine user state for this alias.
     *
     * Defines any of:
     * - USER_STATE_UNREGISTERED: User is unregistered
     * - USER_STATE_NOT_JOINED: User is registered
     * - USER_STATE_JOINED: User is registered and joined bar (if specified)
     *
     * If no bar is given USER_STATE_NOT_JOINED will be returned for all users.
     *
     * @param Bar|null [$bar=null] A bar to determine if user has joined.
     * @return int User state.
     */
    public function getUserState($bar = null) {
        if($user = $this->user != null) {
            return ($bar != null && $bar->isJoined($user))
                ? Self::USER_STATE_NOT_JOINED
                : Self::USER_STATE_JOINED;
        }
        return Self::USER_STATE_UNREGISTERED;
    }

    /**
     * Check whether this alias has any matching unverified email address.
     *
     * @return bool True if any unverified email, false if not.
     */
    public function hasUnverifiedEmail() {
        return $this->userEmails()->unverified()->limit(1)->count() > 0;
    }
}
