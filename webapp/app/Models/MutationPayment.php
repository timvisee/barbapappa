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
 * Mutation payment model.
 * This defines additional information for a payment mutation, that belongs to a
 * main mutation.
 *
 * @property int id
 * @property int mutation_id
 * @property int|null payment_id
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class MutationPayment extends Model {

    protected $table = "mutations_payment";

    /**
     * Get the main mutation this payment mutation data belongs to.
     *
     * @return The main mutation.
     */
    public function mutation() {
        return $this->belongsTo('App\Models\Mutation');
    }

    /**
     * Get the payment this mutation had an effect on.
     *
     * @return The affected payment.
     */
    public function payment() {
        return $this->belongsTo('App\Models\Payment');
    }
}
