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

/**
 * Wallet model.
 *
 * This represents a user wallet in an economy.
 *
 * @property int id
 * @property int economy_id
 * @property string name
 * @property decimal balance
 * @property int currency_id
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Wallet extends Model {

    protected $table = "wallets";

    protected $fillable = [
        'economy_id',
        'name',
        'currency_id',
    ];

    /**
     * Get the user this wallet model is from.
     *
     * @return The user.
     */
    public function user() {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * Get the economy this wallet model is part of.
     *
     * @return The economy.
     */
    public function economy() {
        return $this->belongsTo('App\Models\Economy');
    }

    /**
     * Get the used currency.
     *
     * This is not the economy currency as specified in the current economy.
     * Rather it's a direct link to the currency used for this wallet.
     *
     * @return The currency.
     */
    public function currency() {
        return $this->belongsTo('App\Models\Currency');
    }

    /**
     * Format the wallet balance as human readable text using the proper
     * currency format.
     *
     * @return string Formatted balance
     */
    public function formatBalance($color = true) {
        // Format the balance
        $balance = currency_format($this->balance, $this->currency->code);

        // Add color for negative values
        if($color && $this->balance < 0)
            $balance = '<span style="color: red;">' . $balance . '</span>';

        return $balance;
    }
}
