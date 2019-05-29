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
        $serviceable->service_id = $service->id;
        $serviceable->account_holder = $request->input('account_holder');
        $serviceable->iban = $request->input('iban');
        $serviceable->bic = $request->input('bic');
        $serviceable->save();

        // Update serviceable link on service
        $service->serviceable_id = $serviceable->id;
        $service->serviceable_type = get_class($serviceable);
        $service->save();

        return $serviceable;
    }
}
