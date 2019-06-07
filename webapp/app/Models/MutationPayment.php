<?php

namespace App\Models;

use App\Mail\Password\Reset;
use App\Managers\PasswordResetManager;
use App\Scopes\EnabledScope;
use App\Utils\EmailRecipient;
use BarPay\Models\Payment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

// TODO: update parent mutation change time, if this model changes

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

    use Mutationable;

    protected $table = "mutations_payment";

    protected $with = ['payment'];

    protected $fillable = [
        'mutation_id',
        'payment_id',
    ];

    /**
     * Get the payment this mutation had an effect on.
     *
     * @return The affected payment.
     */
    public function payment() {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Handle changes as effect of a state change.
     * This method is called when the state of the mutation changes.
     *
     * For a wallet mutation, this method would change the wallet balance when
     * the new state defines success.
     *
     * @param Mutation $mutation The mutation, parent of this instance.
     * @param int $oldState The old state.
     * @param int $newState The new, current state.
     */
    public function applyState(Mutation $mutation, int $oldState, int $newState) {
        // TODO: the new state must be related to the payment state
    }
}
