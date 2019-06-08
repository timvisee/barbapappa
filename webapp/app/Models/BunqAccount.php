<?php

namespace App\Models;

use App\Scopes\EnabledScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * bunq account mccountodel.
 *
 * This specifies a bunq account (with a specific IBAN) which is dedicated to
 * this application, to use for automated payment processing.
 *
 * @property int id
 * @property int community_id
 * @property-read Community community
 * @property boolean enabled
 * @property string|null description
 * @property string token_encrypted
 * @property string token
 * @property string account_holder
 * @property string iban
 * @property string bic
 * @property Carbon|null deleted_at
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class BunqAccount extends Model {

    use SoftDeletes;

    protected $table = "bunq_accounts";

    protected $fillable = ['community_id', 'enabled', 'description', 'iban', 'bic'];

    public static function boot() {
        parent::boot();
        static::addGlobalScope(new EnabledScope);
    }

    /**
     * Get the relation to the economy this product is part of.
     * This might be null if this account is not linked to a specific community.
     *
     * @return Relation to the community this product is part of.
     */
    public function community() {
        return $this->belongsTo(Community::class);
    }
}
