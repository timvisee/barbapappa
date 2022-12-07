<?php

namespace App\Models;

use App\Managers\EmailVerificationManager;
use App\Traits\HasPassword;
use App\Traits\HasSlug;
use App\Traits\Joinable;
use App\Utils\SlugUtils;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Bar model.
 *
 * @property int id
 * @property string name
 * @property int community_id
 * @property int economy_id
 * @property string|null slug
 * @property string|null description
 * @property bool enabled
 * @property bool show_explore
 * @property bool show_community
 * @property bool self_enroll
 * @property string|null password
 * @property int|null inventory_id
 * @property-read Inventory|null inventory
 * @property string|null low_balance_text
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Bar extends Model {

    use HasFactory, HasPassword, HasSlug, Joinable;
    use \Staudenmeir\EloquentHasManyDeep\HasRelationships;

    protected $table = 'bar';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    protected $fillable = [
        'economy_id',
        'name',
        'slug',
        'enabled',
        'password',
        'show_explore',
        'show_community',
        'self_enroll',
        'low_balance_text',
    ];

    /**
     * A scope for bars to publicly show in the explore list.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeShowExplore($query) {
        $query->where('show_explore', true);
    }

    /**
     * A scope for bars to show in the bar list of their community page for
     * users enrolled in that community.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeShowCommunity($query) {
        $query->where('show_community', true);
    }

    /**
     * Find the bar in a smart manner, using the slug if a slug is given.
     *
     * @param string $id The bar ID or slug.
     *
     * @return Bar The bar if found.
     */
    public static function smartFindOrFail($id) {
        if(SlugUtils::isValid($id))
            return Bar::slugOrFail($id);
        else
            return Bar::findOrFail($id);
    }

    /**
     * Get the community this bar is part of.
     *
     * @return The community.
     */
    public function community() {
        return $this->belongsTo(Community::class);
    }

    /**
     * Get the economy this bar uses.
     *
     * @return Economy The economy.
     */
    public function economy() {
        return $this->belongsTo(Economy::class);
    }

    /**
     * A list of bar member models for users that joined this bar.
     *
     * @return Query for list of bar member models.
     */
    public function members() {
        return $this->hasMany(BarMember::class);
    }

    /**
     * Get the inventory assigned to this bar.
     *
     * @return Inventory|null The inventory.
     */
    public function inventory() {
        return $this->belongsTo(Inventory::class);
    }

    /**
     * A list of users that joined this bar.
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
                'bar_member',
                'bar_id',
                'user_id'
            )
            ->using(BarMember::class);

        // With pivot columns
        if(!empty($pivotColumns))
            $query = $query->withPivot($pivotColumns);

        // With timestamps
        if($withTimestamps)
            $query = $query->withTimestamps();

        return $query;
    }

    /**
     * Count the members this bar has.
     *
     * @return int Member count.
     */
    public function memberCount() {
        return $this->memberUsers([], false)->count();
    }

    /**
     * Let the given user join this bar.
     * This automatically joins the user in the related community and economy.
     *
     * @param User $user The user to join.
     * @param int|null [$role=null] An optional role value to assign to the
     *      user.
     *
     * @throws \Exception Throws if already joined.
     */
    public function join(User $user, $role = null) {
        $economy = $this->economy;
        $bar = $this;

        // Join the community, economy and bar
        DB::transaction(function() use($economy, $bar, $user) {
            if(!$economy->isJoined($user))
                $economy->join($user);
            $bar->memberJoin($user);
        });

        // Send verification emails if user did not verify any mail yet
        // We send this now, not after registering, see: https://gitlab.com/timvisee/barbapappa/-/issues/428
        if(!EmailVerificationManager::hasSentRecentlyOrVerified($user->emails)) {
            $isNewUser = $user->created_at >= now()->subWeek();
            $user->emails->each(function($email) use($isNewUser) {
                EmailVerificationManager::createAndSend($email, $isNewUser);
            });
        }
    }

    /**
     * Let the given user leave this bar.
     * Note: this throws an error if the user has not joined.
     *
     * @param User $user The user to leave.
     */
    public function leave(User $user) {
        $community = $this->community;
        $economy = $this->economy;
        $bar = $this;

        // Leave bar and economy
        // TODO: make sure user can actually leave this bar (with economy and community)
        DB::transaction(function() use($bar, $user, $community, $economy) {
            // Leave the bar
            $bar->memberLeave($user);

            // Leave economy if orphan
            if($economy->isJoined($user))
                $economy->leaveIfOrphan($user);
        });
    }

    /**
     * Get a relation to all transactions this bar was a part of.
     * This would include transactions which cover a product bought in this bar.
     *
     * @return Relation to the transactions for this bar.
     */
    public function transactions() {
        return $this
            ->hasManyDeepFromRelations(
                $this->productMutations(),
                (new MutationWallet)->mutation(),
                (new Mutation)->transaction()
            )
            ->distinct();
    }

    /**
     * Count the number of transactions that were part of this bar.
     * This method ensures coutned transactions are distinct.
     *
     * See `Self::transactions()` for more details.
     *
     * @return int Number of unique transactions.
     */
    public function transactionCount() {
        return $this->transactions()->count('transaction.id');
    }

    /**
     * Get a relation to all product mutations of products that were bought at
     * this bar.
     *
     * @return Relation to product mutations at this bar.
     */
    public function productMutations() {
        return $this->hasMany(MutationProduct::class);
    }

    /**
     * Get the localized bar description.
     *
     * @return string|null Localized bar description, or null if none is
     * configured.
     */
    public function description() {
        // TODO: placeholder for when localized descriptions are available
        return $this->description;
    }

    /**
     * Get a relation to kiosk sessions for this bar.
     */
    public function kioskSessions() {
        return $this->hasMany(KioskSession::class);
    }
}
