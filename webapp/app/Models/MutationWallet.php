<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

use App\Mail\Password\Reset;
use App\Managers\PasswordResetManager;
use App\Scopes\EnabledScope;
use App\Utils\EmailRecipient;

// TODO: update parent mutation change time, if this model changes

/**
 * Mutation wallet model.
 * This defines additional information for a wallet mutation, that belongs to a
 * main mutation.
 *
 * @property int id
 * @property int mutation_id
 * @property int|null wallet_id
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class MutationWallet extends Model {

    protected $table = "mutations_wallet";

    protected $with = ['wallet'];

    protected $fillable = [
        'mutation_id',
        'wallet_id',
    ];

    /**
     * Get the main mutation this wallet mutation data belongs to.
     *
     * @return The main mutation.
     */
    public function mutation() {
        return $this->belongsTo('App\Models\Mutation');
    }

    /**
     * Get the wallet this mutation had an effect on.
     *
     * @return The affected wallet.
     */
    public function wallet() {
        return $this->belongsTo('App\Models\Wallet');
    }
}
