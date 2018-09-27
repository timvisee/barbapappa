<?php

namespace App\Models;

use App\Helpers\ValidationDefaults;
use App\Mail\Password\Reset;
use App\Managers\PasswordResetManager;
use App\Traits\HasPassword;
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
 * @property bool visible
 * @property bool public
 * @property string|null password
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Community extends Model {

    use HasPassword;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];

    /**
     * A scope for only showing communities that have been defined as visible by
     * the owner.
     */
    public function scopeVisible($query) {
        $query->where('visible', true);
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
     * Find the community by the given slug, or fail.
     *
     * @param string $slug The slug.
     *
     * @return Community The community if found.
     */
    public static function slugOrFail($slug) {
        return Community::where('slug', $slug)->firstOrFail();
    }

    /**
     * Get a list of economies that are part of this community.
     *
     * @return List of economies.
     */
    public function economies() {
        return $this->hasMany('App\Models\Economy');
    }

    /**
     * Get a list of bars that are part of this community.
     *
     * @return List of bars.
     */
    public function bars() {
        return $this->hasMany('App\Models\Bar');
    }

    /**
     * A list of users that joined this community.
     *
     * @return List of joined users.
     */
    public function users() {
        return $this->belongsToMany(
            'App\Models\User',
            'community_user',
            'community_id',
            'user_id'
        )->withTimestamps();
    }

    /**
     * Let the given user join this community.
     * Note: this throws an error if the user has already joined.
     *
     * @param User $user The user to join.
     */
    public function join(User $user) {
        $this->users()->attach($user);
    }

    /**
     * Let the given user leave this community.
     * Note: this throws an error if the user has not joined.
     *
     * @param User $user The user to leave.
     */
    public function leave(User $user) {
        $this->users()->detach($user);
    }

    /**
     * Check whether the given user is joined this community.
     *
     * @param User $user The user to check for.
     *
     * @return boolean True if joined, false if not.
     */
    public function isJoined(User $user) {
        return $this->users->contains($user);
    }

    /**
     * Check whether this community has a slug specified.
     *
     * @return bool True if specified, false if not or if empty.
     */
    public function hasSlug() {
        return !empty($this->slug);
    }
}
