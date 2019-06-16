<?php

namespace BarPay\Controllers;

use App\Helpers\ValidationDefaults;
use BarPay\Models\Service;
use BarPay\Models\ServiceManualIban;
use Illuminate\Http\Request;

// TODO: extend something else, possibly a trait
class ServiceManualIbanController {

    /**
     * Validate the form input for creating the new serviceable.
     *
     * @param Request $request The request.
     */
    public static function validateCreate(Request $request) {
        $request->validate([
            'account_holder' => 'required|' . ValidationDefaults::NAME,
            'iban' => 'required|iban',
            'bic' => 'nullable|bic',
        ]);
    }

    /**
     * Create the service specific serviceable, and link it to the service.
     *
     * @param Request $request The request.
     * @param Service $service The service.
     *
     * @return ServiceManualIban The created serviceable.
     */
    public static function create(Request $request, Service $service) {
        // Create the serviceable
        $serviceable = new ServiceManualIban();
        $serviceable->account_holder = $request->input('account_holder');
        $serviceable->iban = $request->input('iban');
        $serviceable->bic = $request->input('bic');
        $serviceable->save();

        // Update serviceable link on service
        $service->setServiceable($serviceable);

        return $serviceable;
    }

    /**
     * Create the service specific serviceable, and link it to the service.
     *
     * @param Request $request The request.
     * @param Service $service The service.
     * @param ServiceManualIban $serviceable The serviceable.
     */
    public static function edit(Request $request, Service $service, ServiceManualIban $serviceable) {
        $serviceable->account_holder = $request->input('account_holder');
        $serviceable->iban = $request->input('iban');
        $serviceable->bic = $request->input('bic');
        $serviceable->save();
    }
}
