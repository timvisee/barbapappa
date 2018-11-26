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
 * @property bool visible
 * @property bool public
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
     * Get all wallets created by users in this community.
     *
     * @return The wallets.
     */
    public function wallets() {
        return $this->hasManyThrough('App\Models\Wallet', 'App\Models\Economy');
    }

    /**
     * A list of users that joined this community.
     *
     * @param array [$pivotColumns] An array of pivot columns to include.
     * @param boolean [$withTimestamps=true] True to include timestamp columns.
     *
     * @return List of joined users.
     */
    public function users($pivotColumns = ['role'], $withTimestamps = true) {
        // Query relation
        $query = $this->belongsToMany(
                'App\Models\User',
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
}
