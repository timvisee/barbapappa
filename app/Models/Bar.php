<?php

namespace App\Models;

use App\Mail\Password\Reset;
use App\Managers\PasswordResetManager;
use App\Traits\HasPassword;
use App\Traits\HasSlug;
use App\Utils\EmailRecipient;
use App\Utils\SlugUtils;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Bar model.
 *
 * @property int id
 * @property int community_id
 * @property int economy_id
 * @property string name
 * @property bool visible
 * @property bool public
 * @property string|null password
 * @property string|null slug
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Bar extends Model {

    use HasPassword, HasSlug;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];

    /**
     * A scope for only showing bars that have been defined as visible by the
     * owner.
     */
    public function scopeVisible($query) {
        $query->where('visible', true);
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
        return $this->belongsTo('App\Models\Community');
    }

    /**
     * Get the economy this bar uses.
     *
     * @return Economy The economy.
     */
    public function economy() {
        return $this->belongsTo('App\Models\Economy');
    }

    /**
     * A list of users that joined this bar.
     *
     * @return List of joined users.
     */
    public function users() {
        return $this->belongsToMany(
            'App\Models\User',
            'bar_user',
            'bar_id',
            'user_id'
        )->withTimestamps();
    }

    /**
     * Let the given user join this bar.
     * Note: this throws an error if the user has already joined.
     *
     * @param User $user The user to join.
     */
    public function join(User $user) {
        $this->users()->attach($user);
    }

    /**
     * Let the given user leave this bar.
     * Note: this throws an error if the user has not joined.
     *
     * @param User $user The user to leave.
     */
    public function leave(User $user) {
        $this->users()->detach($user);
    }

    /**
     * Check whether the given user is joined this bar.
     *
     * @param User $user The user to check for.
     *
     * @return boolean True if joined, false if not.
     */
    public function isJoined(User $user) {
        return $this->users->contains($user);
    }
}
