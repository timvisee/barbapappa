<?php

namespace BarPay\Models;

use BarPay\Controllers\PaymentManualIbanController;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Manual IBAN payment data class.
 *
 * This represents a payment data for a manual IBAN transfer.
 *
 * @property int id
 * @property int payment_id
 * @property string iban IBAN to transfer to.
 * @property string ref A reference code.
 * @property datetime|null transferred_at When the user manuall transferred if done.
 * @property datetime|null confirmed_at When the manual transfer was confirmed by the counter party if done.
 * @property string|null bic Optional BIC corresponding to the IBAN.
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class PaymentManualIban extends Model {

    protected $table = "payment_manual_iban";

    protected $casts = [
        'transferred_at' => 'datetime',
        'confirmed_at' => 'datetime',
    ];

    /**
     * Wait this number of seconds after a transfer, before asking a community
     * manager to confirm the payment is received.
     */
    public const TRANSFER_WAIT = 2 * 24 * 60 * 60;

    /**
     * The controller to use for this paymentable.
     */
    public const CONTROLLER = PaymentManualIbanController::class;

    /**
     * The root for views related to this payment.
     */
    public const VIEW_ROOT = 'barpay::payment.manualiban';

    const STEP_TRANSFER = 'transfer';
    const STEP_TRANSFERRING = 'transferring';
    const STEP_RECEIPT = 'receipt';

    /**
     * Get a relation to the payment this belongs to.
     *
     * @return Relation to the payment.
     */
    public function payment() {
        return $this->morphOne(Payment::class, 'paymentable');
    }

    /**
     * Create the paymentable part for a newly started payment, and attach it to
     * the payment.
     *
     * @param Payment $payment The payment to create it for, and to attach it to.
     * @param Service $service The payment service to use.
     *
     * @return Paymentable The created payment.
     */
    public static function startPaymentable(Payment $payment, Service $service) {
        // TODO: require to be in a transaction?

        // Get the serviceable
        $serviceable = $service->serviceable;

        // Build the paymentable for the payment
        $paymentable = new PaymentManualIban();
        $paymentable->payment_id = $payment->id;
        $paymentable->to_account_holder = $serviceable->account_holder;
        $paymentable->to_iban = $serviceable->iban;
        $paymentable->to_bic = $serviceable->bic;
        // TODO: somehow obtain the target iban here!
        $paymentable->from_iban = '';
        $paymentable->save();

        // Attach the paymentable to the payment
        $payment->setPaymentable($paymentable);

        return $paymentable;
    }

    /**
     * Get the current paymentable step.
     *
     * @return string Paymentable step.
     */
    public function getStep() {
        if($this->transferred_at == null || $this->from_iban == null)
            return Self::STEP_TRANSFER;
        // TODO: fetch days from constant
        if($this->transferred_at > now()->subSeconds(Self::TRANSFER_WAIT))
            return Self::STEP_TRANSFERRING;
        if($this->confirmed_at == null)
            return Self::STEP_RECEIPT;

        throw new \Exception('Paymentable is in invalid step state');
    }

    public function getStepData() {
        // TODO: translate
        // TODO: build this based on the paymentable!
        $steps = [
            Self::STEP_TRANSFER => [
                'label' => 'Transfer',
                'description' => 'Transfer money, enter IBAN',
            ],
            Self::STEP_TRANSFERRING => [
                'label' => 'Transferring',
                'description' => 'Wait on transfer',
            ],
            Self::STEP_RECEIPT => [
                'label' => 'Receipt',
                'description' => 'Wait for receipt',
            ],
        ];

        // Add state to each step, based on current step, return it
        // - -1: upcomming
        // -  0: current
        // -  1: done
        $currentStep = $this->getStep();
        $got = false;
        return collect($steps)
            ->reverse()
            ->map(function($data, $step) use(&$got, $currentStep) {
                // Determine step states
                if(!$got) {
                    if($step == $currentStep) {
                        $got = true;
                        $data['state'] = 0;
                    } else
                        $data['state'] = -1;
                } else
                    $data['state'] = 1;

                // Only leave description on current step
                if($data['state'] != 0)
                    unset($data['description']);

                return $data;
            })
            ->reverse()
            ->toArray();
    }

    public function getStepView() {
        return $this->view('step' . ucfirst($this->getStep()));
    }

    public function getStepAction($prefix = null) {
        // Build the normal action, optionally prefix
        $action = 'step' . ucfirst($this->getStep());
        if(!empty($prefix))
            $action = 'do' . ucfirst($action);

        return $action;
    }

    /**
     * Get a translation for this payment.
     *
     * @return string|null The translation or null if non existent.
     */
    public static function __($key) {
        return __('barpay::payment.manualiban.' . $key);
    }

    /**
     * Get the path for a view related to this payment.
     *
     * @return string The path to the view.
     */
    public static function view($path) {
        return Self::VIEW_ROOT . '.' . $path;
    }

    /**
     * Block direclty deleting.
     */
    public function delete() {
        throw new \Exception('cannot directly delete paymentable, delete the owning payment instead');
    }
}
