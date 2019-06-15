<?php

namespace BarPay\Controllers;

use App\Helpers\ValidationDefaults;
use BarPay\Models\Service;
use BarPay\Models\ServiceBunqMeTab;
use Illuminate\Http\Request;

// TODO: extend something else, possibly a trait
class ServiceBunqMeTabController {

    /**
     * Validate the form input for creating the new serviceable.
     *
     * @param Request $request The request.
     */
    public static function validateCreate(Request $request) {
        // TODO: validate bunq account here!
        $request->validate([
            'bunq_account' => 'required|numeric|exists:bunq_accounts,id',
            'account_holder' => 'required|' . ValidationDefaults::NAME,
            'iban' => 'required|iban|unique:bunq_accounts,iban',
            'bic' => 'nullable|bic',
        ], [
            'iban.unique' => __('barpay::service.bunq.ibanCannotBeReceivingBunqAccount'),
        ]);
    }

    /**
     * Create the service specific serviceable, and link it to the service.
     *
     * @param Request $request The request.
     * @param Service $service The service.
     *
     * @return ServiceBunqMeTab The created serviceable.
     */
    public static function create(Request $request, Service $service) {
        // Create the serviceable
        $serviceable = new ServiceBunqMeTab();
        $serviceable->service_id = $service->id;
        $serviceable->bunq_account_id = $request->input('bunq_account');
        $serviceable->account_holder = $request->input('account_holder');
        $serviceable->iban = $request->input('iban');
        $serviceable->bic = $request->input('bic');
        $serviceable->save();

        // Update serviceable link on service
        $service->setServiceable($serviceable);

        return $serviceable;
    }

    /**
     * Edit the service specific serviceable, and link it to the service.
     *
     * @param Request $request The request.
     * @param Service $service The service.
     * @param ServiceBunqMeTab $serviceable The serviceable.
     */
    public static function edit(Request $request, Service $service, ServiceBunqMeTab $serviceable) {
        $serviceable->account_holder = $request->input('account_holder');
        $serviceable->iban = $request->input('iban');
        $serviceable->bic = $request->input('bic');
        $serviceable->save();
    }
}
