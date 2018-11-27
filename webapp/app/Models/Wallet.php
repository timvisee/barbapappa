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

    /**
     * Format the balance as plain text.
     */
    const BALANCE_PLAIN = 0;

    /**
     * Format the balance as colored text, depending on the value.
     */
    const BALANCE_COLOR = 1;

    /**
     * Format the balance as colored label, depending on the value.
     */
    const BALANCE_LABEL = 2;

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
     * @param decimal|null [$balance=null] The balance, or null for the wallet
     *      balance.
     * @param boolean [$color=true] True to add color to the balance.
     *
     * @return string Formatted balance
     */
    public function formatBalance($balance = null, $formatting = Self::BALANCE_PLAIN) {
        // Get the balance
        if($balance === null)
            $balance = $this->balance;

        // Format the balance
        $balance = currency_format($balance, $this->currency->code);

        // Add color for negative values
        switch($formatting) {
            case Self::BALANCE_PLAIN:
                break;
            case Self::BALANCE_COLOR:
                if($this->balance < 0)
                    $balance = '<span style="color: red;">' . $balance . '</span>';
                else if($this->balance > 0)
                    $balance = '<span style="color: green;">' . $balance . '</span>';
                break;
            case Self::BALANCE_LABEL:
                if($this->balance < 0)
                    $balance = '<div class="ui red horizontal label">' . $balance . '</div>';
                else if($this->balance > 0)
                    $balance = '<div class="ui green horizontal label">' . $balance . '</div>';
                else
                    $balance = '<div class="ui horizontal label">' . $balance . '</div>';
                break;
            default:
                throw new \Exception("Invalid balance formatting type given");
        }

        return $balance;
    }
}
