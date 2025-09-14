<?php

namespace BarPay\Models;

// TODO: require Model implementation
trait Paymentable {

    /**
     * Get a relation to the payment this belongs to.
     *
     * @return Relation to the payment.
     */
    public function payment() {
        return $this->morphOne(Payment::class, 'paymentable');
    }

    /**
     * Get the raw steps configuration for this paymentable.
     *
     * This returns a list of steps, each having a label and description.
     * The list is keyed by the step identifier.
     *
     * @return array Array of steps.
     */
    protected function getStepsConfig() {
        return collect(Self::STEPS)
            ->mapWithKeys(function($step) {
                return [$step => [
                    'label' => $this->__('steps.' . $step),
                    'description' => $this->__('stepDescriptions.' . $step),
                ]];
            })->toArray();
    }

    /**
     * Get the steps data, usable to show the steps banner on the payment page.
     *
     * This is based on `getStepsConfig()`.
     * A state keys is added to mark what step we're currently at, and the
     * description is removed from all steps but the current.
     *
     * @return array Array of steps data.
     */
    public function getStepsData() {
        // Add state to each step, based on current step, return it
        // - -1: upcomming
        // -  0: current
        // -  1: done
        $current = $this->getStep();
        $got = false;
        return collect($this->getStepsConfig())
            ->reverse()
            ->map(function($data, $step) use(&$got, $current) {
                // Determine step states
                if(!$got) {
                    if($step == $current) {
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

    /**
     * Get the embeddable view for this paymentable to pay as a user based on
     * what step we're currently at.
     * This is shown on the payment progression page.
     *
     * @return string View to use.
     */
    public function getStepPayView() {
        return $this->view('pay' . ucfirst($this->getStep()));
    }

    /**
     * Get the embeddable view for this paymentable to approve as a community
     * manager based on what step we're currently at.
     * This is shown on the payment progression page.
     *
     * @return string View to use.
     */
    public function getStepApproveView() {
        return $this->view('approve' . ucfirst($this->getStep()));
    }

    /**
     * Get the action to run in the paymentable controller based on what step
     * we're currently at.
     *
     * A prefix can be given, such as `do`, in order to link to the POST action.
     *
     * @return string Name of the action to run on the respective controller.
     */
    public function getStepAction($prefix = null) {
        // Build the normal action, optionally prefix
        $action = 'step' . ucfirst($this->getStep());
        if(!empty($prefix))
            $action = $prefix . ucfirst($action);

        return $action;
    }

    /**
     * Get the URL for an external page on which a payment must be completed. If
     * there currently is no such page, or the payment has already been
     * completed, this returns null.
     *
     * We redirect to this through the payment.payRedirect route, so that the
     * user is not routed to the payment page directly.
     *
     * @returns string|null The payment page URL or null.
     */
    public function getPaymentPageUrl() {
        return null;
    }

    /**
     * Get a translation for this payment.
     *
     * @return string|null The translation or null if non existent.
     */
    public static function __($key) {
        return __(Self::LANG_ROOT . '.' . $key);
    }

    /**
     * Get the path for a view related to this payment.
     *
     * @return string The path to the view.
     */
    public static function view($path) {
        return Self::VIEW_ROOT . '.' . $path;
    }
}
