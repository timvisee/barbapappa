<?php

namespace App\Models;

use App\Helpers\ValidationDefaults;
use App\Mail\Password\Reset;
use App\Managers\PasswordResetManager;
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

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot() {
        parent::boot();

        static::addGlobalScope('visible', function(Builder $builder) {
            $builder->where('visible', true);
        });
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
            'user_id',
            'community_id'
        );
    }

    /**
     * Check whether this community has a password specified.
     *
     * @return bool True if specified, false if not or if empty.
     */
    public function hasPassword() {
        return !empty($this->password);
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
