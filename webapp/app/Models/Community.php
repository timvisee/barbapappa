<?php

namespace App\Models;

use App\Traits\HasPassword;
use App\Traits\HasSlug;
use App\Traits\Joinable;
use App\Utils\SlugUtils;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Community model.
 *
 * @property int id
 * @property string name
 * @property string|null slug
 * @property string|null description
 * @property bool show_explore
 * @property bool self_enroll
 * @property string|null password
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Community extends Model {

    use HasPassword, HasSlug, Joinable;
    use \Staudenmeir\EloquentHasManyDeep\HasRelationships;

    protected $table = 'community';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * A scope for communities to publicly show in the explore list.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeShowExplore($query) {
        $query->where('show_explore', true);
    }

    /**
     * Find the community in a smart manner, using the slug if a slug is given.
     *
     * @param string $id The community ID or slug.
     *
     * @return Community The community if found.
     */
    public static function smartFindOrFail($id) {
        if(SlugUtils::isValid($id))
            return Community::slugOrFail($id);
        else
            return Community::findOrFail($id);
    }

    /**
     * Get a list of economies that are part of this community.
     *
     * @return List of economies.
     */
    public function economies() {
        return $this->hasMany(Economy::class);
    }

    /**
     * Get a relation all currencies in economies in this community.
     *
     * @return Relation of currencies.
     */
    public function currencies() {
        return $this->hasManyThrough(
            Currency::class,
            Economy::class
        );
    }

    /**
     * Get a list of bars that are part of this community.
     *
     * @return List of bars.
     */
    public function bars() {
        return $this->hasMany(Bar::class);
    }

    /**
     * Get all wallets created by users in this community.
     *
     * @return The wallets.
     */
    public function wallets() {
        return $this->hasManyDeepFromRelations(
            $this->economies(),
            (new Economy)->wallets()
        );
    }

    /**
     * Get all bunq accounts created in this community scope.
     *
     * @return The bunq accounts.
     */
    public function bunqAccounts() {
        return $this->hasMany(BunqAccount::class);
    }

    /**
     * A list of community member models for users that joined this community.
     *
     * @return Query for list of community member models.
     */
    public function members() {
        return $this->hasMany(CommunityMember::class);
    }

    /**
     * A list of users that joined this community.
     *
     * @param array [$pivotColumns] An array of pivot columns to include.
     * @param boolean [$withTimestamps=true] True to include timestamp columns.
     *
     * @return Query for list of users that are member.
     */
    public function memberUsers($pivotColumns = ['id', 'role'], $withTimestamps = true) {
        // Query relation with pivot model
        $query = $this->belongsToMany(
                User::class,
                'community_member',
                'community_id',
                'user_id'
            )
            ->using(CommunityMember::class);

        // With pivot columns
        if(!empty($pivotColumns))
            $query = $query->withPivot($pivotColumns);

        // With timestamps
        if($withTimestamps)
            $query = $query->withTimestamps();

        return $query;
    }

    /**
     * Count the members this community has.
     *
     * @return int Member count.
     */
    public function memberCount() {
        return $this->memberUsers([], false)->count();
    }

    /**
     * Let the given user join this community.
     *
     * @param User $user The user to join.
     * @param int|null [$role=null] An optional role value to assign to the
     *      user.
     *
     * @throws \Exception Throws if already joined.
     */
    public function join(User $user, $role = null) {
        $this->memberJoin($user, $role);
    }

    /**
     * Let the given user leave this community.
     * Note: this throws an error if the user has not joined.
     *
     * @param User $user The user to leave.
     */
    public function leave(User $user) {
        $this->memberLeave($user);
    }

    /**
     * Let the given user leave this community, if it's an orphan.
     *
     * The user will leave if:
     * - it has not joined any community bars
     * - it has no special community role
     *
     * @param User $user The user to leave if orphan.
     * @throws \Exception Throws if the user is not joined.
     */
    public function leaveIfOrphan(User $user) {
        // User must not have special role
        if($this->member($user)->role != 0)
            return;

        // User must not be a bar member
        $barIds = $this
            ->bars()
            ->select('id')
            ->pluck('id');
        $memberInCommunityBars = BarMember::whereIn('bar_id', $barIds)
            ->where('user_id', $user->id)
            ->limit(1)
            ->count() > 0;
        if($memberInCommunityBars)
            return;

        $this->leave($user);
    }

    /**
     * Get the localized community description.
     *
     * @return string|null Localized community description, or null if none is
     * configured.
     */
    public function description() {
        // TODO: placeholder for when localized descriptions are available
        return $this->description;
    }

    /**
     * This method determines whether this community can be deleted.
     * This does not involve any permission checking. Instead, it ensures there
     * are no dependencies such as economies blocking the safe deletion of this
     * community.
     *
     * Blocking entities:
     * - economies
     * - bars (not yet implemented)
     *
     * See `getDeleteBlockers` as well.
     *
     * @return boolean True if it can be deleted, false if not.
     */
    public function canDelete() {
        // All economies must be deletable
        return $this->economies->every(function($e) {
            return $e->canDelete();
        });
    }

    /**
     * List all entities that currently block this community from being deleted.
     *
     * Blocking entities:
     * - economies
     * - bars (not yet implemented)
     *
     * See `canDelete()` as well.
     *
     * @return array List of entities that block community deletion.
     */
    public function getDeleteBlockers() {
        return $this->economies->filter(function($e) {
            return !$e->canDelete();
        });
    }

    /**
     * Delete this community, and dependent entities that can be deleted.
     *
     * @throws \Exception Throws if this entity cannot be deleted due to
     *      dependent entities. See `canDelete()`.
     */
    public function delete() {
        // Ensure everything can be deleted
        if(!$this->canDelete())
            throw new \Exception('Cannot delete community, has dependent entities that cannot just be deleted');

        // Start a transaction to delete this economy
        $community = $this;
        DB::transaction(function() use($community) {
            // Delete all wallets
            $community->wallets()->delete();

            // Delete this community
            parent::delete();
        });
    }
}
