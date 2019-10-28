<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Economy member model.
 *
 * @property int id
 * @property int economy_id
 * @property-read Economy economy
 * @property int user_id
 * @property-read user
 * @property-read wallets
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class EconomyMember extends Pivot {

    protected $table = 'economy_member';

    protected $with = ['user'];

    /**
     * Get dynamic properties.
     *
     * @param string $name Property name.
     *
     * @return mixed|string Result.
     */
    public function __get($name) {
        switch($name) {
            case 'name':
                return $this->user->name;
            default:
                return parent::__get($name);
        }
    }

    /**
     * Check whether dynamic properties exist.
     *
     * @param string $name Property name.
     *
     * @return bool True if exists, false if not.
     */
    public function __isset($name) {
        switch($name) {
            case 'name':
                return true;
            default:
                return parent::__isset($name);
        }
    }

    /**
     * Get the member economy.
     *
     * @return Economy The economy.
     */
    public function economy() {
        return $this->belongsTo(Economy::class);
    }

    /**
     * Get the member user.
     *
     * @return User The user.
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the wallets that belong to this member.
     *
     * @return Relation to the member wallets.
     */
    public function wallets() {
        return $this
            ->hasMany(Wallet::class, 'economy_member_id')
            ->orderBy('balance', 'DESC');
    }
}
