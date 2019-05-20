<?php

namespace App\Models;

use App\Helpers\ValidationDefaults;
use App\Mail\Password\Reset;
use App\Managers\PasswordResetManager;
use App\Traits\HasPassword;
use App\Traits\HasSlug;
use App\Traits\Joinable;
use App\Utils\EmailRecipient;
use App\Utils\SlugUtils;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
        return $this->hasManyThrough(Wallet::class, Economy::class);
    }

    /**
     * A list of users that joined this community.
     *
     * @param array [$pivotColumns] An array of pivot columns to include.
     * @param boolean [$withTimestamps=true] True to include timestamp columns.
     *
     * @return Query for list of joined users.
     */
    // TODO: rename this to members?
    public function users($pivotColumns = ['role'], $withTimestamps = true) {
        // Query relation
        $query = $this->belongsToMany(
                User::class,
                'community_user',
                'community_id',
                'user_id'
            );

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
        return $this->users([], false)->count();
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
}
