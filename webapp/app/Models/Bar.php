<?php

namespace App\Models;

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

    use HasPassword, HasSlug, Joinable;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password'
    ];

    protected $fillable = [
        'economy_id',
        'name',
        'slug',
        'password',
        'visible',
        'public',
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
     * A list of users that joined this bar.
     *
     * @param array [$pivotColumns] An array of pivot columns to include.
     * @param boolean [$withTimestamps=true] True to include timestamp columns.
     *
     * @return List of joined users.
     */
    public function users($pivotColumns = [], $withTimestamps = true) {
        // Query relation
        $query = $this->belongsToMany(
                User::class,
                'bar_user',
                'bar_id',
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
     * Get a relation to all product mutations of products that were bought at
     * this bar.
     *
     * @return Relation to product mutations at this bar.
     */
    public function productMutations() {
        return $this->hasMany(MutationProduct::class);
    }
}
